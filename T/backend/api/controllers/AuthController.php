<?php
// login, register, logout, refreshToken
const TIME_TO_EXPIRE = '+1 minute';
function login($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciais inválidas']);
        return;
    }

    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime(TIME_TO_EXPIRE));

    $stmt = $pdo->prepare("INSERT INTO tokens (token, user_id, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$token, $user['id'], $expiresAt]);

    echo json_encode([
        'token' => $token,
        'expires_at' => $expiresAt,
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ]
    ]);
}
function register($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $role = 'USUARIO';

    if (!in_array($role, ['ADMINISTRADOR', 'USUARIO', 'VISITANTE'])) {
        $role = 'VISITANTE';
    }

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome, email e senha são obrigatórios']);
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        ]);
        echo json_encode(['message' => 'Usuário registrado com sucesso']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()]);
    }
}
function registerListAll($pdo) {
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users WHERE id > 1 ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}
function updateRegister($pdo, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $userId == 1) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuário não encontrado']);
        return;
    }

    $fields = [];
    $params = [];

    if (!empty($data['name'])) {
        $fields[] = 'name = ?';
        $params[] = $data['name'];
    }
    if (!empty($data['email'])) {
        $fields[] = 'email = ?';
        $params[] = $data['email'];
    }
    if (!empty($data['password'])) {
        $fields[] = 'password = ?';
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    if (!empty($data['role']) && in_array($data['role'], ['ADMINISTRADOR', 'USUARIO', 'VISITANTE'])) {
        $fields[] = 'role = ?';
        $params[] = $data['role'];
    }

    if (empty($fields)) {
        echo json_encode(['message' => 'Nada para atualizar']);
        return;
    }

    $params[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['message' => 'Usuário atualizado com sucesso']);
}
function deleteRegister($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    $email = $data['email'] ?? null;

    if (!$userId && !$email) {
        http_response_code(400);
        echo json_encode(['error' => 'Informe user_id ou email para exclusão']);
        return;
    }

    if (!$userId && $email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado com o e-mail fornecido']);
            return;
        }
        $userId = $user['id'];
    }

    if ($userId == 1) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuário não encontrado']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    if ($stmt->rowCount()) {
        echo json_encode(['message' => "Usuário ID $userId removido com sucesso"]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Usuário não encontrado ou já removido']);
    }
}
function logout($pdo) {
    $headers = getallheaders();
    $token = trim(str_replace('Bearer', '', $headers['Authorization'] ?? ''));

    $stmt = $pdo->prepare("DELETE FROM tokens WHERE token = ?");
    $stmt->execute([$token]);

    echo json_encode(['message' => 'Logout realizado com sucesso']);
}
function refreshToken($pdo) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    $stmt = $pdo->prepare("SELECT user_id FROM tokens WHERE token = ? AND expires_at > datetime('now')");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido ou expirado']);
        return;
    }

    // Gera novo token
    $newToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime(TIME_TO_EXPIRE));

    // Remove o antigo e insere o novo
    $pdo->prepare("DELETE FROM tokens WHERE token = ?")->execute([$token]);
    $pdo->prepare("INSERT INTO tokens (token, user_id, expires_at) VALUES (?, ?, ?)")
        ->execute([$newToken, $user['user_id'], $expiresAt]);

    echo json_encode(['token' => $newToken, 'expires_at' => $expiresAt]);
}
function revokeAllTokens($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    $email = $data['email'] ?? null;

    if (!$userId && !$email) {
        http_response_code(400);
        echo json_encode(['error' => 'Informe user_id ou email']);
        return;
    }

    if (!$userId && $email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado com o e-mail fornecido']);
            return;
        }
        $userId = $user['id'];
    }

    $stmt = $pdo->prepare("DELETE FROM tokens WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo json_encode([
        'message' => "Todos os tokens foram revogados para o usuário ID $userId"
    ]);
}



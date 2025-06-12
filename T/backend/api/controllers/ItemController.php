<?php
// listItemsByUser, createItem, updateItem, deleteItem

function listItems($pdo) {
    $stmt = $pdo->query("SELECT items.*, users.name AS owner FROM items JOIN users ON users.id = items.user_id");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
}

function listItemsByUser($pdo) {
    $user = $GLOBALS['current_user'];
    $limit = $_GET['limit'] ?? 10;
    $offset = $_GET['offset'] ?? 0;

    if (!in_array($_GET['order'], ['created_at ASC', 'created_at DESC'])) {
        $order = 'created_at ASC';
    } else {
        $order = $_GET['order'];
    }

    $stmt = $pdo->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY $order LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $user['id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
}

function createItem($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $GLOBALS['current_user']['id'];
    $title = $input['title'] ?? '';
    $description = $input['description'] ?? '';
    $price = $input['price'] ?? 0.0;
    $type = $input['type'] ?? '';

    if (!$title || !$type) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos obrigatórios ausentes']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO items (user_id, title, description, price, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $price, $type]);

    echo json_encode(['message' => 'Item criado com sucesso']);
}

function updateItem($pdo, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $GLOBALS['current_user']['id'];

    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Item não encontrado ou não pertence ao usuário']);
        return;
    }

    $fields = [];
    $params = [];

    foreach(['title', 'description', 'price', 'type'] as $key) {
         if (isset($input[$key])) {
            $fields[] = "$key = ?";
            $params[] = $input[$key];
        }
    }
    
    if (empty($fields)) {
        echo json_encode(['message' => 'Nenhum campo fornecido para atualização']);
        return;
    }

    $params[] = $id;    

    $sql = "UPDATE items SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['message' => 'Item atualizado com sucesso']);
}

function deleteItem($pdo, $id) {
    $user_id = $GLOBALS['current_user']['id'];

    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Item não encontrado ou não pertence ao usuário']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['message' => 'Item removido com sucesso']);
}
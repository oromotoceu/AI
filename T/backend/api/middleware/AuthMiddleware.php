<?php
// checkToken, requireRole
// middleware/AuthMiddleware.php

function checkToken() {
    global $pdo;
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token não fornecido']);
        exit;
    }

    $token = trim(str_replace('Bearer', '', $headers['Authorization']));

    $stmt = $pdo->prepare("SELECT users.* FROM tokens JOIN users ON users.id = tokens.user_id WHERE tokens.token = ? AND tokens.expires_at > datetime('now')");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido ou expirado']);
        exit;
    }

    // Armazena usuário atual no escopo global (ou em contexto compartilhado)
    $GLOBALS['current_user'] = $user;
    return true;
}

function requireRole($requiredRole) {
    $user = $GLOBALS['current_user'] ?? null;

    if (!$user || ($user['role'] !== $requiredRole)) {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso negado para o perfil atual']);
        exit;
    }

    return true;
}
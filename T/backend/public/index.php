<?php
// index.php - Ponto de entrada

require_once '../config/db.php';
require_once '../api/controllers/AuthController.php';
require_once '../api/controllers/ItemController.php';
require_once '../api/controllers/ConfigController.php';
require_once '../api/middleware/AuthMiddleware.php';


// Suporte a CORS básico
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove prefixo /v1
$uri = preg_replace('#^/v1#', '', $uri);

switch (true) {
    case $uri === '/register' && $method === 'GET':
        checkToken();
        requireRole('ADMINISTRADOR');
        registerListAll($pdo);
        break;    
    case $uri === '/register' && $method === 'POST':
        register($pdo);
        break;
    case $uri === '/register' && $method === 'PUT':
        checkToken();
        requireRole('ADMINISTRADOR');
        updateRegister($pdo);
        break;
    case $uri === '/register' && $method === 'DELETE':
        checkToken();
        requireRole('ADMINISTRADOR');
        deleteRegister($pdo);
        break;
    case $uri === '/login' && $method === 'POST':
        login($pdo);
        break;
    case $uri === '/logout' && $method === 'POST':
        checkToken();
        logout($pdo);
        break;
    case $uri === '/items' && $method === 'GET':
        checkToken();
        listItemsByUser($pdo);
        break;
    case $uri === '/items' && $method === 'POST':
        checkToken();
        requireRole('USUARIO');
        createItem($pdo);
        break;
    case preg_match('#^/items/\d+$#', $uri) && $method === 'PUT':
        checkToken();
        requireRole('USUARIO');
        updateItem($pdo, basename($uri));
        break;
    case preg_match('#^/items/\d+$#', $uri) && $method === 'DELETE':
        checkToken();
        requireRole('USUARIO');
        deleteItem($pdo, basename($uri));
        break;
    case $uri === '/token/refresh' && $method === 'POST':
        checkToken();
        refreshToken($pdo);
        break;
    case $uri === '/config' && $method === 'GET':
        checkToken();
        requireRole('ADMINISTRADOR');
        getConfig($pdo);
        break;
    case $uri === '/config' && $method === 'PUT':
        checkToken();
        requireRole('ADMINISTRADOR');
        updateConfig($pdo);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint nao encontrado. '.$uri]);
}
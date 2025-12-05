<?php
use Src\Controllers\PreferencesController;

// CORS headers
require_once __DIR__ . '/configs/bootstrap.php';

// Responde à requisição OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Detecta método e rota
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Base da aplicação no XAMPP
$base = '/BackEndLojaDeSapatos/index.php';

// Remove a base da URL
$route = str_replace($base, '', $path);

// Normaliza rota para minúsculo
$route = strtolower(rtrim($route, '/'));

// Define rotas
switch ("$method $route") {

    case 'GET /user/preferences':
        PreferencesController::getPreferences();
        break;

    case 'PUT /user/preferences/theme':
        PreferencesController::updateTheme();
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
        break;
}
?>
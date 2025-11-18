<?php
use Src\Controllers\PreferencesController;

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Ajuste conforme o nome da pasta no XAMPP
$base = '/BackEndLojaDeSapatos';
$route = str_replace($base, '', $path);

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
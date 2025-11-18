<?php
// Cabeçalhos CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");

// Responde a requisições OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoload do Composer
require_once __DIR__ . '/../BackEndLojaDeSapatos/vendor/autoload.php';

// Carrega variáveis de ambiente
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Roteamento simples
use Src\controllers\PreferencesController;

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Ajusta caminho base se necessário (ex: /BackEndLojaDeSapatos)
$base = '/BackEndLojaDeSapatos'; // ajuste conforme sua pasta no XAMPP
$route = str_replace($base, '', $path);

// Define rotas
switch ("$method $route") {
    case 'GET/user/preferences':
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
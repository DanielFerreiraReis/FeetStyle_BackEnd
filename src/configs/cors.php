<?php
// Carrega o .env (caso você use dotenv)
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
}

// DOMÍNIO PERMITIDO (caso ainda esteja em desenvolvimento)
$allowedOrigin = $_ENV['APP_URL'] ?? 'http://localhost:5173';

// Cabeçalhos de CORS
header("Access-Control-Allow-Origin: {$allowedOrigin}");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Trata requisições OPTIONS (preflight)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

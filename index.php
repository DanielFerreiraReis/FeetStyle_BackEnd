<?php
// CORS headers
header("Access-Control-Allow-Origin: *"); // Evite usar '*' se estiver usando Authorization
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Responde à requisição OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoload e dotenv
require_once __DIR__ . '/../BackEndLojaDeSapatos/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Roteador
require_once __DIR__ . '/src/router.php';
?>
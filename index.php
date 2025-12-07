<?php
// CORS headers
require_once __DIR__  . '/src/configs/cors.php';

// Responde à requisição OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Roteador
require_once __DIR__ . '/src/router.php';
?>
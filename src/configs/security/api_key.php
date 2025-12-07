<?php
//middleware que valida a chave somente quando a rota for privada
// src/security/api_key.php
function checkApiKey() {
    $headers = getallheaders();
    $apiKey = $headers['X-Internal-Key'] ?? null;
    $expected = $_ENV['API_KEY'] ?? null;

    if (!$apiKey || $apiKey !== $expected) {
        http_response_code(403);
        echo json_encode(['error' => 'API Key inválida ou ausente']);
        exit;
    }
}
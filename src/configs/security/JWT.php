<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validateJwt() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] 
        ?? $headers['authorization'] 
        ?? $_SERVER['HTTP_AUTHORIZATION'] 
        ?? '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }
    }

    http_response_code(401);
    echo json_encode(['error' => 'Token ausente']);
    exit;
}
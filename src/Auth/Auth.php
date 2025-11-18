<?php
namespace Src\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    public static function getUserId() {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
                return $decoded->user_id ?? null;
            } catch (\Exception $e) {
                http_response_code(401);
                echo json_encode(['error' => 'Token inválido']);
                exit;
            }
        }

        http_response_code(401);
        echo json_encode(['error' => 'Token ausente']);
        exit;
    }
}
?>
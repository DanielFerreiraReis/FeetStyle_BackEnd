<?php
namespace Src\Controllers;

use Src\Models\Preferences;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PreferencesController {
    private static function getUserIdFromToken() {
        $headers = getallheaders();
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

    public static function getPreferences() {
        $userId = self::getUserIdFromToken();
        $theme = Preferences::getTheme($userId);
        echo json_encode(['theme' => $theme]);
    }

    public static function updateTheme() {
        $userId = self::getUserIdFromToken();
        $input = json_decode(file_get_contents('php://input'), true);
        $theme = $input['theme'] ?? 'Dark';
        Preferences::setTheme($userId, $theme);
        echo json_encode(['status' => 'ok']);
    }
}
?>
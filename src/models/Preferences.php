<?php 
namespace Src\Models;

use Src\Database;

class Preferences {
    public static function getTheme($userId) {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT theme FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 'Dark';
    }

    public static function setTheme($userId, $theme) {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("UPDATE user_preferences SET theme = ? WHERE user_id = ?");
        return $stmt->execute([$theme, $userId]);
    }
}
?>
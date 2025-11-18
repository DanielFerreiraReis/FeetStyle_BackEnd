<?php 
namespace Src\Models;

use Src\Database;

class Preferences {
    public static function getTheme($userId) {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT theme FROM Funcionario WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 'Dark';
    }

    public static function setTheme($userId, $theme) {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("UPDATE Funcionario SET theme = ? WHERE id = ?");
        return $stmt->execute([$theme, $userId]);
    }
}
?>
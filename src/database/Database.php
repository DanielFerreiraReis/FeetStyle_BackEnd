<?php
// verifica a conectividade com o servidor
namespace Src;

use PDO;
use PDOException;
use Dotenv\Dotenv;


class Database
{
    public static function conectar(): ?PDO
    {
        // Carrega o .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Lê as variáveis
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db   = $_ENV['DB_DATABASE'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? '';
        $pass = $_ENV['DB_PASSWORD'] ?? '';

        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            error_log("✅ Conexão bem-sucedida!");
            return $pdo;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => "Erro na conexão: " . $e->getMessage()]);
            exit;
        }
    }
}
?>
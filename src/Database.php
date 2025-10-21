<?php

namespace Src;

use PDO;
use PDOException;

class Database
{
    public static function conectar()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db   = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];

        try {
            return new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            echo "✅ Conexão bem-sucedida!";

        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}

?>
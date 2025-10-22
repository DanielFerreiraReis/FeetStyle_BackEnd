<!-- Parte a ser modificada e entendida == CRUD completo com rotas REST-->
<?php

require __DIR__ . '/vendor/autoload.php';

use Src\Database;
require __DIR__ . '/src/Database.php';


use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

var_dump($_ENV); // 👈 Teste direto

echo $_ENV['APP_NAME']; // Loja de Sapatos
echo getenv('DB_HOST'); // Deve mostrar "localhost"

Database::conectar();
?>
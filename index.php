<!-- Parte a ser modificada e entendida == CRUD completo com rotas REST-->
<?php

require __DIR__ . '/vendor/autoload.php';

use Src\Database;
require __DIR__ . '/src/database/Database.php';


use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

Database::conectar();

echo "API da {$_ENV['APP_NAME']} conectada com sucesso!";
?>
<!-- Parte a ser modificada e entendida == CRUD completo com rotas REST-->

<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\SapatoController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = new SapatoController();

if ($uri === '/api/sapatos' && $method === 'GET') {
    $controller->listar();
} elseif ($uri === '/api/sapatos' && $method === 'POST') {
    $controller->cadastrar();
} elseif (preg_match('/\/api\/sapatos\/(\d+)/', $uri, $matches)) {
    $id = $matches[1];
    if ($method === 'PUT') {
        $controller->atualizar($id);
    } elseif ($method === 'DELETE') {
        $controller->excluir($id);
    } else {
        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada"]);
}
?>
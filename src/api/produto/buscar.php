<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../database/Database.php';


use Src\Database;

// Verifica se o ID foi enviado
if (!isset($_GET["id"])) {
    echo json_encode(["success" => false, "message" => "ID não enviado"]);
    exit;
}

$id = $_GET["id"];

try {
    $db = new Database();
    $conn = Database::conectar();

    $sql = "
        SELECT 
            c.idCalcado AS id,
            c.corCalcado AS cor,
            c.tamanhoCalcado AS tamanho,
            c.genero AS genero,
            c.precoSapato AS valor_unit,
            m.nomeModelo AS descricao,
            c.foto AS image
        FROM Calcado c
        INNER JOIN Modelo m ON m.idModelo = c.idModelo
        WHERE c.idCalcado = :id
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $produto['image'] = "http://localhost/BackEndLojaDeSapatos/uploads/fotosCalcados/" . $produto['image'];

        echo json_encode([
            "success" => true,
            "produto" => $produto
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Produto não encontrado"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro de servidor",
        "error" => $e->getMessage()]);
}
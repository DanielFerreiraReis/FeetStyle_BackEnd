<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Database;

$pdo = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Marca");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nome = $input['nomeMarca'] ?? '';

    if (!$nome) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome da marca é obrigatório']);
        exit;
    }

    // 1. Ano vigente
    $ano = date("Y");

    // 2. Contar quantas marcas já existem neste ano
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Marca WHERE idMarca LIKE :anoPrefix");
    $stmt->execute([':anoPrefix' => $ano . '%']);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Próximo número sequencial
    $sequencial = $total + 1;

    // 4. Montar ID: ano + sequencial com padding
    // Exemplo: 20250001, 20250002...
    $idMarca = $ano . str_pad($sequencial, 4, "0", STR_PAD_LEFT);

    // 5. Inserir com ID manual
    $stmt = $pdo->prepare("INSERT INTO Marca (idMarca, nomeMarca) VALUES (:id, :nome)");
    $stmt->execute([
        ':id' => $idMarca,
        ':nome' => $nome
    ]);

    echo json_encode(['idMarca' => $idMarca, 'nomeMarca' => $nome]);
    exit;
}
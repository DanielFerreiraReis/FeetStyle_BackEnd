<?php
require_once __DIR__ . '/../configs/bootstrap.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Tipo");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nome = $input['nomeTipo'] ?? '';
    $descricao = $input['descricaoTipo'] ?? '';
    $categoria = $input['categoria'] ?? '';

    if (!$nome || !$categoria) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome e categoria são obrigatórios']);
        exit;
    }

    // 1. Ano vigente
    $ano = date("Y");

    // 2. Contar quantos tipos já existem neste ano
    $anoInicio = (int)($ano . "0000");
    $anoFim = (int)($ano . "9999");
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Tipo WHERE idTipo BETWEEN :anoInicio AND :anoFim");
    $stmt->execute([':anoInicio' => $anoInicio, ':anoFim' => $anoFim]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Próximo número sequencial
    $sequencial = $total + 1;

    // 4. Montar ID: ano + sequencial com padding
    $idTipo = (int)($ano . str_pad($sequencial, 4, "0", STR_PAD_LEFT));

    // 5. Inserir com ID manual
    $stmt = $pdo->prepare("INSERT INTO Tipo (idTipo, nomeTipo, descricaoTipo, categoria) 
                           VALUES (:id, :nome, :descricao, :categoria)");
    $stmt->execute([
        ':id' => $idTipo,
        ':nome' => $nome,
        ':descricao' => $descricao,
        ':categoria' => $categoria
    ]);

    echo json_encode([
        'idTipo' => $idTipo,
        'nomeTipo' => $nome,
        'descricaoTipo' => $descricao,
        'categoria' => $categoria
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
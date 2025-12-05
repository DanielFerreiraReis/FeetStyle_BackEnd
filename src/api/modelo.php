<?php
require_once __DIR__ . '/../configs/bootstrap.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Modelo");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nome = $input['nomeModelo'] ?? '';
    $anoLancamento = $input['anoLancamento'] ?? null;
    $anoModelo = $input['anoModelo'] ?? '';
    $idTipos = $input['idTipos'] ?? null;

    if (!$nome || !$anoModelo || !$idTipos) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos obrigatórios ausentes']);
        exit;
    }

    // 1. Ano vigente
    $ano = date("Y");

    // 2. Contar quantos modelos já existem neste ano
    $anoInicio = (int)($ano . "0000");
    $anoFim = (int)($ano . "9999");
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Modelo WHERE idModelo BETWEEN :anoInicio AND :anoFim");
    $stmt->execute([':anoInicio' => $anoInicio, ':anoFim' => $anoFim]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Próximo número sequencial
    $sequencial = $total + 1;

    // 4. Montar ID: ano + sequencial com padding
    $idModelo = (int)($ano . str_pad($sequencial, 4, "0", STR_PAD_LEFT));

    // 5. Inserir com ID manual
    $stmt = $pdo->prepare("INSERT INTO Modelo (idModelo, nomeModelo, anoLancamento, anoModelo, idTipos) 
                           VALUES (:id, :nome, :lancamento, :modelo, :idTipos)");
    $stmt->execute([
        ':id' => $idModelo,
        ':nome' => $nome,
        ':lancamento' => $anoLancamento,
        ':modelo' => $anoModelo,
        ':idTipos' => $idTipos
    ]);

    echo json_encode([
        'idModelo' => $idModelo,
        'nomeModelo' => $nome,
        'anoLancamento' => $anoLancamento,
        'anoModelo' => $anoModelo,
        'idTipos' => $idTipos
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
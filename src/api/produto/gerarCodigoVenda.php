<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../database/Database.php';

use Src\Database;

$pdo = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // 1. Ano vigente
        $ano = date("Y");

        // 2. Definir intervalo para IDs do ano
        $anoInicio = (int)($ano . "0000");
        $anoFim = (int)($ano . "9999");

        // 3. Contar quantas vendas já existem neste ano
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM Venda WHERE idVenda BETWEEN :anoInicio AND :anoFim");
        $stmt->execute([':anoInicio' => $anoInicio, ':anoFim' => $anoFim]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 4. Próximo número sequencial
        $sequencial = $total + 1;

        // 5. Montar código da venda
        $codigoVenda = (int)($ano . str_pad($sequencial, 4, "0", STR_PAD_LEFT));

        // 6. Retornar para o front
        echo json_encode([
            "success" => true,
            "idVenda" => $codigoVenda
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Erro ao gerar código da venda",
            "error" => $e->getMessage()
        ]);
    }
    exit;
}
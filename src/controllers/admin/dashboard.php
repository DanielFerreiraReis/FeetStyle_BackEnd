<?php
require_once __DIR__ . "/../../configs/bootstrap.php";

// Total de funcionários
$stmt = $pdo->query("SELECT COUNT(*) FROM Funcionario");
$totalFuncionarios = $stmt->fetchColumn();

// Total de vendas
$stmt = $pdo->query("SELECT COUNT(*) FROM Venda");
$totalVendas = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'message' => 'Painel carregado com sucesso',
    'stats' => [
        'funcionarios' => $totalFuncionarios,
        'vendas' => $totalVendas
    ]
]);

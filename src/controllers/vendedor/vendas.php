<?php
require_once __DIR__ . "/../../configs/bootstrap.php";
$decoded = validateJwt(); // já retorna array com user_id

$stmt = $pdo->prepare("SELECT * FROM Venda WHERE idFuncionario = ?");
$stmt->execute([$decoded['user_id']]);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'vendas' => $vendas
]);

<?php
require_once __DIR__ . '/../configs/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método inválido"]);
    exit;
}

$id = $_POST['id'] ?? '';
$nome = strtolower($_POST['nome'] ?? '');
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM Funcionario 
    WHERE id = :id AND nome = :nome AND cpf = :cpf
");
$stmt->execute([
    ":id" => $id,
    ":nome" => $nome,
    ":cpf" => $cpf
]);

if ($stmt->fetchColumn() > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Dados não conferem"]);
}
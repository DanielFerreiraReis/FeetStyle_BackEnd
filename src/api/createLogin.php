<?php
require_once __DIR__ . '/../configs/bootstrap.php';
require_once __DIR__ . '/../database/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método inválido"]);
    exit;
}

$idFuncionario = $_POST['idFuncionario'] ?? '';
$userLog = $_POST['userLog'] ?? '';
$password = $_POST['password'] ?? '';

if (!$idFuncionario || !$userLog || !$password) {
    echo json_encode(["success" => false, "message" => "Campos obrigatórios faltando"]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO Login (idFuncionario, userLog, password)
        VALUES (:idFuncionario, :userLog, :password)
    ");

    $stmt->execute([
        ":idFuncionario" => $idFuncionario,
        ":userLog" => $userLog,
        ":password" => $hash
    ]);

    echo json_encode(["success" => true, "message" => "Login criado com sucesso"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
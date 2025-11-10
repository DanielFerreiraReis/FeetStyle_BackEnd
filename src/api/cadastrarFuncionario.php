<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../database/Database.php';

use Src\Database;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

try {
    $pdo = Database::conectar();

    $anoAtual = date('Y');
    do {
        $codigoAleatorio = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $id = $anoAtual . $codigoAleatorio;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Funcionario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existe = $stmt->fetchColumn() > 0;
    } while ($existe);

    // Coleta dos dados
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $status = $_POST['status'] ?? 1;
    $cargo = $_POST['cargo'] ?? '';
    $dataAdmissao = $_POST['dataAdmissao'] ?? '';
    $dataDemissao = $_POST['dataDemissao'] ?? null;
    $salario = $_POST['salario'] ?? 0;
    $cpf = $_POST['cpf'] ?? '';
    $foto = null;
    $role = $_POST['role'] ?? 0;
    $rua = $_POST['rua'] ?? '';
    $numCasa = $_POST['numCasa'] ?? 0;
    $bairro = $_POST['bairro'] ?? null;
    $cep = $_POST['cep'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $detalhamentoEndereco = $_POST['detalhamentoEndereco'] ?? null;

    // Upload da foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $filePath)) {
            $foto = $fileName;
        }
    }

    // Inserção no banco
    $sql = "INSERT INTO Funcionario (id, nome, telefone, email, status, cargo, dataAdmissao, dataDemissao, salario, cpf, foto, role, rua, numCasa, bairro, cep, cidade, estado, detalhamentoEndereco) 
            VALUES (:id, :nome, :telefone, :email, :status, :cargo, :dataAdmissao, :dataDemissao, :salario, :cpf, :foto, :role, :rua, :numCasa, :bairro, :cep, :cidade, :estado, :detalhamentoEndereco)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':cargo', $cargo);
    $stmt->bindParam(':dataAdmissao', $dataAdmissao);
    $stmt->bindParam(':dataDemissao', $dataDemissao);
    $stmt->bindParam(':salario', $salario);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':role', $role, PDO::PARAM_INT);
    $stmt->bindParam(':rua', $rua);
    $stmt->bindParam(':numCasa', $numCasa, PDO::PARAM_INT);
    $stmt->bindParam(':bairro', $bairro);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':detalhamentoEndereco', $detalhamentoEndereco);

    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Funcionário cadastrado com sucesso', 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
}
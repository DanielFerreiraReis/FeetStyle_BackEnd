<?php
require_once __DIR__ . '/../configs/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

try {
    // Geração de ID único
    $anoAtual = date('Y');
    do {
        $codigoAleatorio = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $id = $anoAtual . $codigoAleatorio;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Cliente WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existe = $stmt->fetchColumn() > 0;
    } while ($existe);

    // Dados comuns
    $nome = strtolower($_POST['nome'] ?? '');
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $status = $_POST['status'] ?? 1;
    $rua = strtolower($_POST['rua'] ?? '');
    $numCasa = $_POST['numCasa'] ?? 0;
    $bairro = strtolower($_POST['bairro'] ?? null);
    $cep = $_POST['cep'] ?? '';
    $cidade = strtolower($_POST['cidade'] ?? '');
    $estado = $_POST['estado'] ?? '';
    $detalhamentoEndereco = strtolower($_POST['detalhamentoEndereco'] ?? null);

    // Inserção na tabela Cliente
    $sqlCliente = "INSERT INTO Cliente (id, nome, telefone, email, status, rua, numCasa, bairro, cep, cidade, estado, detalhamentoEndereco)
                   VALUES (:id, :nome, :telefone, :email, :status, :rua, :numCasa, :bairro, :cep, :cidade, :estado, :detalhamentoEndereco)";
    $stmt = $pdo->prepare($sqlCliente);
    $stmt->execute([
        ':id' => $id,
        ':nome' => $nome,
        ':telefone' => $telefone,
        ':email' => $email,
        ':status' => $status,
        ':rua' => $rua,
        ':numCasa' => $numCasa,
        ':bairro' => $bairro,
        ':cep' => $cep,
        ':cidade' => $cidade,
        ':estado' => $estado,
        ':detalhamentoEndereco' => $detalhamentoEndereco
    ]);

    // Verifica se é PF ou PJ
    $tipoCliente = $_POST['tipoCliente'] ?? 'PF';

    if ($tipoCliente === 'PF') {
        $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $sexo = $_POST['sexo'] ?? 'M';
        $dataNascimento = $_POST['dataNascimento'] ?? null;

        $sqlPF = "INSERT INTO PessoaFisica (idCliente, cpf, sexo, dataNascimento)
                  VALUES (:idCliente, :cpf, :sexo, :dataNascimento)";
        $stmtPF = $pdo->prepare($sqlPF);
        $stmtPF->execute([
            ':idCliente' => $id,
            ':cpf' => $cpf,
            ':sexo' => $sexo,
            ':dataNascimento' => $dataNascimento
        ]);
    } else {
        $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
        $dataFundacao = $_POST['dataFundacao'] ?? null;
        $areaAtuacao = $_POST['areaAtuacao'] ?? '';

        $sqlPJ = "INSERT INTO PessoaJuridica (idCliente, cnpj, dataFundacao, areaAtuacao)
                  VALUES (:idCliente, :cnpj, :dataFundacao, :areaAtuacao)";
        $stmtPJ = $pdo->prepare($sqlPJ);
        $stmtPJ->execute([
            ':idCliente' => $id,
            ':cnpj' => $cnpj,
            ':dataFundacao' => $dataFundacao,
            ':areaAtuacao' => $areaAtuacao
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Cliente cadastrado com sucesso', 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
}
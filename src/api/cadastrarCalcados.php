<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Database;

$pdo = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ----------------------------------------------------------
// FUNÇÃO: Ler "data" vindo de JSON puro OU multipart/form-data
// ----------------------------------------------------------
function getInputData() {
    // Caso seja multipart/form-data
    if (!empty($_POST['data'])) {
        return json_decode($_POST['data'], true);
    }

    // Caso seja JSON puro
    $raw = file_get_contents("php://input");

    if ($raw) {
        return json_decode($raw, true);
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Ler os dados (de qualquer método)
    $data = getInputData();

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Nenhum dado recebido ou JSON inválido']);
        exit;
    }

    // 2) Validar ID
    if (empty($data['idCalcado'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID do calçado é obrigatório']);
        exit;
    }

    $idCalcado = $data['idCalcado'];

    // ----------------------------------------------------------
    // 3) Lidar com imagem (opcional)
    // ----------------------------------------------------------

    $nomeArquivo = null;

    // Caso venha imagem via multipart/form-data
    if (isset($_FILES['fotoFile'])) {

        $foto = $_FILES['fotoFile'];
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid('calcado_', true) . '.' . $ext;
        $destino = __DIR__ . '/../../uploads/fotosCalcados/' . $nomeArquivo;

        if (!move_uploaded_file($foto['tmp_name'], $destino)) {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao salvar imagem']);
            exit;
        }
    }

    // ----------------------------------------------------------
    // 4) Inserir no banco
    // ----------------------------------------------------------

    $stmt = $pdo->prepare("
        INSERT INTO Calcado 
        (idCalcado, genero, corCalcado, tamanhoCalcado, precoSapato, 
         dataFabricacao, quantidadeEmStoque, foto, idModelo)
        VALUES (:id, :genero, :cor, :tamanho, :preco, :dataFabricacao, 
                :quantidade, :foto, :idModelo)
    ");

    $stmt->execute([
        ':id' => $data['idCalcado'],
        ':genero' => $data['genero'],
        ':cor' => $data['corCalcado'],
        ':tamanho' => $data['tamanhoCalcado'],
        ':preco' => $data['precoSapato'],
        ':dataFabricacao' => $data['dataFabricacao'],
        ':quantidade' => $data['quantidadeEmStoque'],
        ':foto' => $nomeArquivo,              // null se não enviar imagem
        ':idModelo' => $data['idModelo']
    ]);

    echo json_encode([
        'sucesso' => true,
        'idCalcado' => $idCalcado,
        'foto_salva' => $nomeArquivo,
        'message' => 'Calçado cadastrado com sucesso!'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método não permitido']);
<?php
// Liberação de CORS
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Se for preflight (OPTIONS), encerra aqui
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");

// Conexão com o banco
$host = "localhost";
$user = "root";
$pass = "";
$db   = "DB_LojaDeSapatos";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erro de conexão: " . $conn->connect_error]);
    exit;
}

// Receber dados JSON
$data = json_decode(file_get_contents("php://input"), true);

$idVenda        = $data["idVenda"];
$dataVenda      = $data["dataVenda"];
$idFuncionario  = $data["idFuncionario"];
$metodoPagamento= $data["metodoPagamento"]; // string: "pix", "cartao", etc
$valorTotal     = $data["valorTotal"];
$itens          = $data["itens"];

// Iniciar transação
$conn->begin_transaction();

try {
    // Inserir na tabela Venda
    $stmt = $conn->prepare("
        INSERT INTO Venda (idVenda, dataVenda, quantidade, valorTotal, metodoPagamento, idFuncionario, idCalcado)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Erro prepare Venda: " . $conn->error);
    }

    // quantidade total de itens
    $quantidadeTotal = array_sum(array_column($itens, "quantidade"));

    // Usamos o primeiro item para preencher idCalcado (campo obrigatório)
    $idCalcadoPrimeiro = $itens[0]["idCalcado"];

    // Tipos corretos: i (int), s (string), i (int), d (double), s (string), i (int), i (int)
    $stmt->bind_param(
        "isidsii",
        $idVenda,
        $dataVenda,
        $quantidadeTotal,
        $valorTotal,
        $metodoPagamento,
        $idFuncionario,
        $idCalcadoPrimeiro
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro execute Venda: " . $stmt->error);
    }

    // Inserir itens na tabela VendaItem
    $stmtItem = $conn->prepare("
        INSERT INTO VendaItem (idVenda, idCalcado, quantidade, valorUnitario, totalItem)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmtItem) {
        throw new Exception("Erro prepare VendaItem: " . $conn->error);
    }

    foreach ($itens as $item) {
        $stmtItem->bind_param(
            "iiidd",
            $idVenda,
            $item["idCalcado"],
            $item["quantidade"],
            $item["valorUnitario"],
            $item["totalItem"]
        );

        if (!$stmtItem->execute()) {
            throw new Exception("Erro execute VendaItem: " . $stmtItem->error);
        }
    }

    // Confirmar transação
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Venda registrada com sucesso!"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
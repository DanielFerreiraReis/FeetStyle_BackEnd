<?php
// Usa CORS + autoload + env + PDO + rateLimit do bootstrap
// Já temos: headers, CORS, OPTIONS, autoload, ENV, e **$pdo** conectado
//usos do bootstrap
require_once __DIR__ . '/../../configs/bootstrap.php';

// Verifica se o ID foi enviado
if (!isset($_GET["id"])) {
    echo json_encode(["success" => false, "message" => "ID não enviado"]);
    exit;
}

$id = $_GET["id"];

try {

    // -----------------------------------------------------------------
    // 2. Consulta SQL
    // -----------------------------------------------------------------
    $sql = "
        SELECT 
            c.idCalcado AS id,
            c.corCalcado AS cor,
            c.tamanhoCalcado AS tamanho,
            c.genero AS genero,
            c.precoSapato AS valor_unit,
            m.nomeModelo AS descricao,
            c.foto AS image
        FROM Calcado c
        INNER JOIN Modelo m ON m.idModelo = c.idModelo
        WHERE c.idCalcado = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);   // <-- usa PDO do bootstrap
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    // -----------------------------------------------------------------
    // 3. Retorno
    // -----------------------------------------------------------------
    if ($produto) {

        $produto['image'] =
            "http://localhost/BackEndLojaDeSapatos/uploads/fotosCalcados/" . $produto['image'];

        echo json_encode([
            "success" => true,
            "produto" => $produto
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Produto não encontrado"
        ]);
    }

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erro de servidor",
        "error" => $e->getMessage()
    ]);
}
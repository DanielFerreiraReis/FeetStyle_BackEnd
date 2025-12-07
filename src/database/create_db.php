<?php
require __DIR__ . "/../../vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// CORS
$allowedOrigin = $_ENV['APP_URL'] ?? 'http://localhost:5173';
header("Access-Control-Allow-Origin: {$allowedOrigin}");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Internal-Key");
header("Content-Type: application/json");

// OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Flag API ativa?
$enabled = strtolower($_ENV['STATUS_API_DB'] ?? 'false');
if ($enabled !== 'true') {
    echo json_encode(["success" => false, "message" => "Este endpoint está desativado."]);
    exit;
}

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$db   = $_ENV['DB_DATABASE'] ?? 'DB_LojaDeSapatos';

try {
    // 1 — Conectar sem banco
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 2 — Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // 3 — Reconectar agora dentro do banco
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 4 — Criar tabelas
    $sql = <<<SQL

    use $db;
    
    CREATE TABLE IF NOT EXISTS Funcionario (
        id INT PRIMARY KEY NOT NULL,
        nome VARCHAR(45) NOT NULL,
        telefone BIGINT NOT NULL,
        email VARCHAR(45) NOT NULL,
        status TINYINT(1) NOT NULL,
        cargo VARCHAR(45) NOT NULL,
        dataAdmissao DATE NOT NULL,
        dataDemissao DATE NULL,
        salario DECIMAL(10,2) NOT NULL,
        cpf CHAR(11) UNIQUE NOT NULL,
        foto VARCHAR(255) NOT NULL,
        role TINYINT(1) NOT NULL,
        rua VARCHAR(45) NOT NULL,
        numCasa INT NOT NULL,
        bairro VARCHAR(20),
        cep CHAR(9) NOT NULL,
        cidade VARCHAR(45) NOT NULL,
        estado CHAR(2) NOT NULL,
        detalhamentoEndereco VARCHAR(45),
        theme VARCHAR(10) NOT NULL DEFAULT 'Dark'
    );
    CREATE TABLE IF NOT EXISTS Login (
        idFuncionario INT PRIMARY KEY NOT NULL,
        userLog VARCHAR(45) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        FOREIGN KEY (idFuncionario) REFERENCES Funcionario(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS Cliente (
        id INT PRIMARY KEY NOT NULL,
        nome VARCHAR(45) NOT NULL,
        telefone BIGINT NOT NULL,
        email VARCHAR(45) NOT NULL,
        status TINYINT(1) NOT NULL,
        rua VARCHAR(45) NOT NULL,
        numCasa INT NOT NULL,
        bairro VARCHAR(20),
        cep CHAR(9) NOT NULL,
        cidade VARCHAR(45) NOT NULL,
        estado CHAR(2) NOT NULL,
        detalhamentoEndereco VARCHAR(45)
    );
    CREATE TABLE IF NOT EXISTS PessoaFisica (
        idCliente INT PRIMARY KEY NOT NULL,
        cpf CHAR(11) UNIQUE NOT NULL,
        sexo CHAR(1) NOT NULL,
        dataNascimento DATE NOT NULL,
        FOREIGN KEY (idCliente) REFERENCES Cliente(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS PessoaJuridica (
        idCliente INT PRIMARY KEY NOT NULL,
        cnpj CHAR(14) UNIQUE NOT NULL,
        dataFundacao DATE NOT NULL,
        areaAtuacao VARCHAR(45) NOT NULL,
        FOREIGN KEY (idCliente) REFERENCES Cliente(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS Marca (
        idMarca INT PRIMARY KEY NOT NULL,
        nomeMarca VARCHAR(50) NOT NULL
    );
    CREATE TABLE IF NOT EXISTS Tipo (
        idTipo INT PRIMARY KEY NOT NULL,
        nomeTipo VARCHAR(30) NOT NULL,
        descricaoTipo VARCHAR(100),
        categoria VARCHAR(15)
    );
    CREATE TABLE IF NOT EXISTS Fabricar (
        idMarca INT NOT NULL,
        idTipo INT NOT NULL,
        PRIMARY KEY (idMarca, idTipo),
        FOREIGN KEY (idMarca) REFERENCES Marca(idMarca)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        FOREIGN KEY (idTipo) REFERENCES Tipo(idTipo)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS Modelo (
        idModelo INT PRIMARY KEY NOT NULL,
        nomeModelo VARCHAR(30) UNIQUE NOT NULL,
        anoLancamento DATE,
        anoModelo DATE NOT NULL,
        idTipos INT NOT NULL,
        FOREIGN KEY (idTipos) REFERENCES Tipo(idTipo)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS Calcado (
        idCalcado INT PRIMARY KEY NOT NULL,
        genero CHAR(1),
        corCalcado VARCHAR(15),
        tamanhoCalcado VARCHAR(5),
        precoSapato DOUBLE,
        dataFabricacao DATE,
        quantidadeEmStoque INT NOT NULL,
        foto VARCHAR(255) NOT NULL,
        idModelo INT NOT NULL,
        FOREIGN KEY (idModelo) REFERENCES Modelo(idModelo)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS Venda (
        idVenda INT PRIMARY KEY NOT NULL,
        dataVenda DATE NOT NULL,
        quantidade INT NOT NULL,
        valorTotal DECIMAL(10,2) NOT NULL,
        idFuncionario INT NOT NULL,
        idCliente INT NOT NULL,
        idCalcado INT NOT NULL,
        FOREIGN KEY (idFuncionario) REFERENCES Funcionario(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        FOREIGN KEY (idCliente) REFERENCES Cliente(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        FOREIGN KEY (idCalcado) REFERENCES Calcado(idCalcado)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
    CREATE TABLE IF NOT EXISTS rate_limit (
        ip VARCHAR(50) PRIMARY KEY,
        attempts INT NOT NULL DEFAULT 0,
        blocked_until INT NOT NULL DEFAULT 0
    );
    SQL;

    $pdo->exec($sql);

    echo json_encode([
        "success" => true,
        "message" => "Banco criado e tabelas instaladas com sucesso!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
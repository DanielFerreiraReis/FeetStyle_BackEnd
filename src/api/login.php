<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../database/Database.php';

use Src\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$pdo = Database::conectar();
$input = json_decode(file_get_contents('php://input'), true);
$user = $input['user'] ?? '';
$senha = $input['senha'] ?? '';

if (!$user || !$senha) {
    echo json_encode(['success' => false, 'message' => 'Usuário e senha são obrigatórios']);
    exit;
}

if ($user === 'admin' && $senha === 'admin') {
    $payload = [
        'user_id' => 0,
        'role' => 'admin',
        'exp' => time() + 3600
    ];
    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role' => 'admin',
        'token' => $token,
        'message' => 'Admin padrão'
    ]);
    exit;
}

if ($user === 'vendedor' && $senha === 'vendedor') {
    $payload = [
        'user_id' => 0,
        'role' => 'vendedor',
        'exp' => time() + 3600
    ];
    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role' => 'vendedor',
        'token' => $token,
        'message' => 'Vendedor padrão'
    ]);
    exit;
}

try {
    $sql = "SELECT f.id, f.role, f.status, l.password
            FROM Login l
            JOIN Funcionario f ON l.idFuncionario = f.id
            WHERE l.userLog = :user";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user', $user);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || !password_verify($senha, $result['password'])) {
        echo json_encode(['success' => false, 'message' => 'Credenciais inválidas']);
        exit;
    }

    if ($result['status'] != 1) {
        echo json_encode(['success' => false, 'message' => 'Funcionário inativo']);
        exit;
    }

    $role = $result['role'] == 1 ? 'admin' : 'vendedor';

    $payload = [
        'user_id' => $result['id'],
        'role' => $role,
        'exp' => time() + 3600
    ];

    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role' => $role,
        'token' => $token
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor']);
}

?>
<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Responde a requisições OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../database/Database.php';

use Src\Database;

// Conecta ao banco
$pdo = Database::conectar();

// Recebe os dados do frontend
$input = json_decode(file_get_contents('php://input'), true);
$user = $input['user'] ?? '';
$senha = $input['senha'] ?? '';

// Validação básica
if (!$user || !$senha) {
  echo json_encode(['success' => false, 'message' => 'Usuário e senha são obrigatórios']);
  exit;
}

try {
  // Consulta o login e dados do funcionário
  $sql = "SELECT f.role, f.status, l.password
          FROM Login l
          JOIN Funcionario f ON l.idFuncionario = f.id
          WHERE l.userLog = :user";

  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user', $user);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Verificações
  if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    exit;
  }

  if ($result['status'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Funcionário inativo']);
    exit;
  }

  if (!password_verify($senha, $result['password'])) {
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit;
  }

  // Define o cargo
  $role = $result['role'] == 1 ? 'admin' : 'vendedor';

  // Retorna sucesso
  echo json_encode(['success' => true, 'role' => $role]);

} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Erro no servidor']);
}
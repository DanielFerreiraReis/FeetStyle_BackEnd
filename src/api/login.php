<?php

require_once __DIR__ . '/../configs/bootstrap.php';
require_once __DIR__ . '/../configs/security/rate_limit.php';

use Firebase\JWT\JWT;

// ---------------------------------------------------------------------------
// RATE LIMIT – bloqueia brute force
// ---------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$risk = checkRateLimit($pdo, $ip);

if ($risk === "blocked") {
    echo json_encode([
        'success' => false,
        'message' => 'Muitas tentativas falhas. Tente novamente mais tarde.'
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// RECEBE JSON
// ---------------------------------------------------------------------------
$input = json_decode(file_get_contents('php://input'), true);
$user  = trim($input['user']  ?? '');
$senha = trim($input['senha'] ?? '');

if (!$user || !$senha) {

    registerLoginFail($pdo, $ip);

    echo json_encode([
        'success' => false,
        'message' => 'Usuário e senha obrigatórios'
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// LOGINS PADRÃO (ambiente DEV)
// ---------------------------------------------------------------------------
if ($user === 'admin' && $senha === 'admin') {

    clearRateLimit($pdo, $ip);

    $token = JWT::encode([
        'user_id' => 0,
        'role'    => 'admin',
        'exp'     => time() + 3600
    ], $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role'    => 'admin',
        'token'   => $token
    ]);
    exit;
}

if ($user === 'vendedor' && $senha === 'vendedor') {

    clearRateLimit($pdo, $ip);

    $token = JWT::encode([
        'user_id' => 0,
        'role'    => 'vendedor',
        'exp'     => time() + 3600
    ], $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role'    => 'vendedor',
        'token'   => $token
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// LOGIN PELO BANCO
// ---------------------------------------------------------------------------
try {

    $sql = "SELECT f.id, f.role, f.status, l.password
            FROM Login l
            JOIN Funcionario f ON l.idFuncionario = f.id
            WHERE l.userLog = :user";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user' => $user]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Usuário não encontrado ou senha inválida
    if (!$result || !password_verify($senha, $result['password'])) {

        registerLoginFail($pdo, $ip);

        echo json_encode([
            'success' => false,
            'message' => 'Credenciais inválidas'
        ]);
        exit;
    }

    // Funcionário inativo
    if ($result['status'] != 1) {

        registerLoginFail($pdo, $ip);

        echo json_encode([
            'success' => false,
            'message' => 'Funcionário inativo'
        ]);
        exit;
    }

    // LOGIN OK — limpa tentativas
    clearRateLimit($pdo, $ip);

    $role = $result['role'] == 1 ? 'admin' : 'vendedor';

    $token = JWT::encode([
        'user_id' => $result['id'],
        'role'    => $role,
        'exp'     => time() + 3600
    ], $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'success' => true,
        'role'    => $role,
        'token'   => $token
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Erro no servidor'
    ]);
}
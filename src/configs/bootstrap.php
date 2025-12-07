<?php

// ============================================================================
// CORS
// ============================================================================
require_once __DIR__ . '/cors.php';

// ============================================================================
// AUTOLOAD (Composer)
// ============================================================================
require_once __DIR__ . '/../../vendor/autoload.php';

// Requisição OPTIONS (pré-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ============================================================================
// ENVIRONMENT (.env)
// ============================================================================
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->safeLoad();

// ============================================================================
// DATABASE CONNECTION
// ============================================================================
require_once __DIR__ . '/../database/Database.php';

use Src\Database;

$pdo = Database::conectar();

// ============================================================================
// RATE LIMIT (Anti-Brute-Force)
// ============================================================================
require_once __DIR__ . '/security/rate_limit.php';

$ip = $_SERVER['REMOTE_ADDR'];
$rateAttempts = checkRateLimit($pdo, $ip); // tava aqui o erro 🏆
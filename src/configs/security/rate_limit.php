<?php

// Máximo de tentativas antes do bloqueio
const MAX_ATTEMPTS = 5;

// Tempo de bloqueio (10 min)
const BLOCK_TIME = 10 * 60;

/**
 * Obtém registro do IP
 */
function getAttempt(PDO $pdo, string $ip) {
    $stmt = $pdo->prepare("SELECT * FROM rate_limit WHERE ip = ?");
    $stmt->execute([$ip]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Cria registro inicial
 */
function createAttempt(PDO $pdo, string $ip) {
    $stmt = $pdo->prepare(
        "INSERT INTO rate_limit (ip, attempts, blocked_until) VALUES (?, 0, 0)"
    );
    $stmt->execute([$ip]);
}

/**
 * Verifica status do rate limit
 *
 * Retornos:
 *  - ok
 *  - warning
 *  - blocked
 */
function checkRateLimit(PDO $pdo, string $ip) {
    $data = getAttempt($pdo, $ip);

    if (!$data) {
        createAttempt($pdo, $ip);
        return "ok";
    }

    if ($data["blocked_until"] > time()) {
        return "blocked";
    }

    if ($data["attempts"] >= MAX_ATTEMPTS - 1) {
        return "warning";
    }

    return "ok";
}

/**
 * Registra tentativa falha
 */
function registerLoginFail(PDO $pdo, string $ip) {
    $data = getAttempt($pdo, $ip);

    if (!$data) {
        createAttempt($pdo, $ip);
        $data = getAttempt($pdo, $ip);
    }

    $attempts = $data["attempts"] + 1;
    $blockedUntil = $data["blocked_until"];

    if ($attempts >= MAX_ATTEMPTS) {
        $blockedUntil = time() + BLOCK_TIME;
    }

    $stmt = $pdo->prepare(
        "UPDATE rate_limit SET attempts = ?, blocked_until = ? WHERE ip = ?"
    );
    $stmt->execute([$attempts, $blockedUntil, $ip]);
}

/**
 * Limpa tentativas após sucesso
 */
function clearRateLimit(PDO $pdo, string $ip) {
    $stmt = $pdo->prepare(
        "UPDATE rate_limit SET attempts = 0, blocked_until = 0 WHERE ip = ?"
    );
    $stmt->execute([$ip]);
}
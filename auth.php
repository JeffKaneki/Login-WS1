<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function require_login(): array
{
    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }

    return $_SESSION['user'];
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid form request. Please return to the form and try again.');
    }
}

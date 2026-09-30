<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/security.php';

startSecureSession();

function adminIsAuthenticated(): bool
{
    return !empty($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
}

function requireAdmin(): void
{
    if (!adminIsAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

function attemptAdminLogin(string $usuario, string $senha): bool
{
    $expectedUser = (string) (getenv('ADMIN_USER') ?: '');
    $expectedHash = (string) (getenv('ADMIN_PASSWORD_SHA256') ?: '');

    if ($expectedUser === '' || $expectedHash === '') {
        return false;
    }

    $userOk = hash_equals($expectedUser, $usuario);
    $passwordOk = hash_equals(strtolower($expectedHash), hash('sha256', $senha));

    if (!$userOk || !$passwordOk) {
        usleep(350000);
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_user'] = $expectedUser;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}

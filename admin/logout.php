<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

requireValidCsrf();
unset($_SESSION['admin_authenticated'], $_SESSION['admin_user']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header('Location: login.php');
exit;

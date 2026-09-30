<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

requireValidCsrf();

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || $senha === '') {
    header('Location: login.php?erro=Credenciais inválidas');
    exit;
}

// Atraso pequeno e uniforme reduz tentativas automatizadas sem revelar se o e-mail existe.
usleep(250000);

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        header('Location: login.php?erro=Credenciais inválidas');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => (int) $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
    ];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: agendamento.php');
    exit;
} catch (PDOException $e) {
    error_log('Falha no login: ' . $e->getMessage());
    header('Location: login.php?erro=Erro no servidor');
    exit;
}

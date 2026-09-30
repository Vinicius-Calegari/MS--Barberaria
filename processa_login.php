<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || $senha === '') {
    header('Location: login.php?erro=Credenciais inválidas');
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        header('Location: login.php?erro=Credenciais inválidas');
        exit;
    }

    session_start();
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => (int) $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
    ];

    header('Location: agendamento.php');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: login.php?erro=Erro no servidor');
    exit;
}

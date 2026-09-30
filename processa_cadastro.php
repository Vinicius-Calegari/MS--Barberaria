<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

if ($nome === '' || mb_strlen($nome) > 100 || !$email || $telefone === '') {
    header('Location: cadastro.php?erro=Preencha os dados corretamente');
    exit;
}
if ($senha !== $confirmarSenha) {
    header('Location: cadastro.php?erro=As senhas não coincidem');
    exit;
}
if (strlen($senha) < 8) {
    header('Location: cadastro.php?erro=A senha deve ter pelo menos 8 caracteres');
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn()) {
        header('Location: cadastro.php?erro=Este e-mail já está cadastrado');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nome, $email, $telefone, password_hash($senha, PASSWORD_DEFAULT)]);

    session_start();
    session_regenerate_id(true);
    $_SESSION['usuario'] = ['id' => (int) $pdo->lastInsertId(), 'nome' => $nome, 'email' => $email];
    header('Location: agendamento.php?sucesso=Cadastro realizado com sucesso!');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: cadastro.php?erro=Erro ao cadastrar usuário');
    exit;
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

requireValidCsrf();

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');
$confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

if ($nome === '' || mb_strlen($nome) > 100 || !$email || $telefone === '') {
    header('Location: cadastro.php?erro=' . rawurlencode('Preencha os dados corretamente.'));
    exit;
}

if (!preg_match('/^[0-9()+\-\s]{8,20}$/', $telefone)) {
    header('Location: cadastro.php?erro=' . rawurlencode('Telefone inválido.'));
    exit;
}

if ($senha !== $confirmarSenha) {
    header('Location: cadastro.php?erro=' . rawurlencode('As senhas não coincidem.'));
    exit;
}

if (strlen($senha) < 8) {
    header('Location: cadastro.php?erro=' . rawurlencode('A senha deve ter pelo menos 8 caracteres.'));
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nome, $email, $telefone, password_hash($senha, PASSWORD_DEFAULT)]);

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => (int) $pdo->lastInsertId(),
        'nome' => $nome,
        'email' => $email,
    ];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: agendamento.php?sucesso=' . rawurlencode('Cadastro realizado com sucesso.'));
    exit;
} catch (PDOException $e) {
    $mysqlCode = (int) ($e->errorInfo[1] ?? 0);
    if ($e->getCode() === '23000' || $mysqlCode === 1062) {
        header('Location: cadastro.php?erro=' . rawurlencode('Este e-mail já está cadastrado.'));
        exit;
    }

    error_log('Falha no cadastro: ' . $e->getMessage());
    header('Location: cadastro.php?erro=' . rawurlencode('Não foi possível concluir o cadastro.'));
    exit;
}

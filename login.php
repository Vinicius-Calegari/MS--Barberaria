<?php

declare(strict_types=1);

require_once __DIR__ . '/config/security.php';
startSecureSession();
if (isset($_SESSION['usuario']['id'])) {
    header('Location: agendamento.php');
    exit;
}
$erro = trim((string) ($_GET['erro'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Entrar | MS Barbearia</title>
    <link rel="icon" href="img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/rework.css">
</head>
<body>
<div class="auth-page">
    <section class="auth-visual">
        <div><span class="eyebrow">Área do cliente</span><h2>Seu horário,<br>do seu jeito.</h2><p class="muted">Entre para reservar, consultar e gerenciar seus próximos atendimentos.</p></div>
    </section>
    <main class="auth-panel">
        <div class="auth-card">
            <a href="index.php"><img class="auth-logo" src="img/Logo.png" alt="MS Barbearia"></a>
            <span class="eyebrow">Bem-vindo de volta</span>
            <h1>Entre na sua conta</h1>
            <p class="muted">Acesse sua agenda em poucos segundos.</p>
            <?php if ($erro !== ''): ?><div class="alert-ms error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form action="processa_login.php" method="POST" autocomplete="on">
                <?= csrfField() ?>
                <div class="field-ms"><label for="email">E-mail</label><input class="control-ms" type="email" id="email" name="email" autocomplete="email" placeholder="voce@email.com" required autofocus></div>
                <div class="field-ms"><label for="senha">Senha</label><input class="control-ms" type="password" id="senha" name="senha" autocomplete="current-password" placeholder="Sua senha" required></div>
                <button class="btn-ms primary" style="width:100%;margin-top:8px" type="submit"><i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar</button>
            </form>
            <p class="auth-foot">Ainda não tem conta? <a href="cadastro.php">Criar minha conta</a></p>
            <p class="auth-foot"><a href="index.php"><i class="fa-solid fa-arrow-left"></i> Voltar ao site</a></p>
        </div>
    </main>
</div>
</body>
</html>

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
    <title>Criar conta | MS Barbearia</title>
    <link rel="icon" href="img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/rework.css">
</head>
<body>
<div class="auth-page">
    <section class="auth-visual">
        <div><span class="eyebrow">Primeiro passo</span><h2>Seu próximo corte<br>começa aqui.</h2><p class="muted">Crie sua conta uma vez e passe a reservar seus horários online.</p></div>
    </section>
    <main class="auth-panel">
        <div class="auth-card">
            <a href="index.php"><img class="auth-logo" src="img/Logo.png" alt="MS Barbearia"></a>
            <span class="eyebrow">Cadastro rápido</span>
            <h1>Crie sua conta</h1>
            <p class="muted">Seus dados são usados apenas para identificar e gerenciar seus agendamentos.</p>
            <?php if ($erro !== ''): ?><div class="alert-ms error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form action="processa_cadastro.php" method="POST" autocomplete="on">
                <?= csrfField() ?>
                <div class="field-ms"><label for="nome">Nome completo</label><input class="control-ms" id="nome" name="nome" maxlength="100" autocomplete="name" required></div>
                <div class="field-ms"><label for="email">E-mail</label><input class="control-ms" type="email" id="email" name="email" autocomplete="email" required></div>
                <div class="field-ms"><label for="telefone">Telefone</label><input class="control-ms" type="tel" id="telefone" name="telefone" inputmode="tel" autocomplete="tel" placeholder="(31) 99999-9999" required></div>
                <div class="form-grid">
                    <div class="field-ms"><label for="senha">Senha</label><input class="control-ms" type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required></div>
                    <div class="field-ms"><label for="confirmar_senha">Confirmar senha</label><input class="control-ms" type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required></div>
                </div>
                <button class="btn-ms primary" style="width:100%;margin-top:8px" type="submit"><i class="fa-solid fa-user-plus"></i> Criar conta e continuar</button>
            </form>
            <p class="auth-foot">Já possui conta? <a href="login.php">Entrar agora</a></p>
            <p class="auth-foot"><a href="index.php"><i class="fa-solid fa-arrow-left"></i> Voltar ao site</a></p>
        </div>
    </main>
</div>
</body>
</html>

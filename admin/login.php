<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

if (adminIsAuthenticated()) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    $usuario = trim((string) ($_POST['usuario'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if (attemptAdminLogin($usuario, $senha)) {
        header('Location: index.php');
        exit;
    }

    $erro = 'Credenciais administrativas inválidas.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin | MS Barbearia</title>
    <link rel="icon" href="../img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/rework.css">
</head>
<body>
<div class="auth-page">
    <section class="auth-visual">
        <div>
            <span class="eyebrow">Gestão MS</span>
            <h2>Agenda sob controle.</h2>
            <p class="muted">Acompanhe reservas, clientes e operação diária em um único painel.</p>
        </div>
    </section>
    <main class="auth-panel">
        <div class="auth-card">
            <a href="../index.php"><img src="../img/Logo.png" class="auth-logo" alt="MS Barbearia"></a>
            <span class="eyebrow">Acesso restrito</span>
            <h1>Painel administrativo</h1>
            <p class="muted">Entre com as credenciais configuradas no ambiente de produção.</p>
            <?php if ($erro !== ''): ?><div class="alert-ms error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="POST" autocomplete="off">
                <?= csrfField() ?>
                <div class="field-ms">
                    <label for="usuario">Usuário</label>
                    <input class="control-ms" id="usuario" name="usuario" autocomplete="username" required autofocus>
                </div>
                <div class="field-ms">
                    <label for="senha">Senha</label>
                    <input class="control-ms" type="password" id="senha" name="senha" autocomplete="current-password" required>
                </div>
                <button class="btn-ms primary" style="width:100%;margin-top:8px" type="submit"><i class="fa-solid fa-lock"></i> Entrar no painel</button>
            </form>
            <p class="auth-foot"><a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Voltar para o site</a></p>
        </div>
    </main>
</div>
</body>
</html>

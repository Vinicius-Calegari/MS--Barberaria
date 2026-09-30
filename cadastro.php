<?php

declare(strict_types=1);

require_once __DIR__ . '/config/security.php';
startSecureSession();

if (isset($_SESSION['usuario'])) {
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
    <title>Cadastro | MS Barbearia</title>
    <link rel="icon" type="image/x-icon" href="img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#050505;--card:#111;--brand:#970f0f;--brand2:#d31a1a;--text:#fff;--muted:#aaa}*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:radial-gradient(circle at top,#351010,#050505 45%);color:var(--text);font-family:Poppins,sans-serif;padding:24px}.card{width:min(520px,100%);background:rgba(17,17,17,.95);border:1px solid rgba(151,15,15,.65);border-radius:22px;padding:30px;box-shadow:0 25px 70px #0008}.logo{display:block;width:92px;margin:0 auto 12px}.card h1{font-family:Bangers,cursive;letter-spacing:2px;text-align:center;font-size:2.2rem;margin:0 0 6px}.subtitle{text-align:center;color:var(--muted);margin:0 0 24px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field{margin-bottom:14px}.field.full{grid-column:1/-1}label{display:block;margin-bottom:6px;font-weight:600}input{width:100%;padding:13px 14px;border-radius:10px;border:1px solid #333;background:#080808;color:#fff;outline:none}input:focus{border-color:var(--brand2);box-shadow:0 0 0 3px rgba(211,26,26,.14)}button{width:100%;border:0;border-radius:11px;padding:14px;background:linear-gradient(135deg,var(--brand),var(--brand2));color:#fff;font-weight:700;cursor:pointer}button:hover{filter:brightness(1.08)}.alert{background:#401116;border:1px solid #842029;color:#ffd7dc;padding:12px;border-radius:10px;margin-bottom:18px}.footer{text-align:center;color:var(--muted);margin-top:18px}.footer a{color:#ff6b6b;text-decoration:none}@media(max-width:620px){.grid{grid-template-columns:1fr}.field.full{grid-column:auto}.card{padding:22px}}
    </style>
</head>
<body>
<main class="card">
    <img class="logo" src="img/Logo.png" alt="MS Barbearia">
    <h1>Crie sua conta</h1>
    <p class="subtitle">Cadastre-se para reservar e gerenciar seus horários.</p>

    <?php if ($erro !== ''): ?>
        <div class="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="processa_cadastro.php" method="POST" autocomplete="on">
        <?= csrfField() ?>
        <div class="grid">
            <div class="field full">
                <label for="nome">Nome completo</label>
                <input id="nome" name="nome" maxlength="100" autocomplete="name" required>
            </div>
            <div class="field full">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" autocomplete="email" required>
            </div>
            <div class="field full">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" autocomplete="tel" inputmode="tel" required>
            </div>
            <div class="field">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="field">
                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <button type="submit"><i class="fas fa-user-plus me-1"></i> Criar conta</button>
    </form>

    <p class="footer">Já tem conta? <a href="login.php">Entrar</a></p>
</main>
</body>
</html>

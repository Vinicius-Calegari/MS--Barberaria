<?php
require_once __DIR__ . '/config/security.php';
startSecureSession();
if (isset($_SESSION['usuario'])) {
    header("Location: agendamento.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MS Barbearia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root{--preto:#000;--dourado:#970f0f;--dourado-claro:#f80404;--branco:#fff;--cinza:#f5f5f5}*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}body{background-color:var(--preto);color:var(--branco);background-image:url('img/bg-login.jpg');background-size:cover;background-position:center;background-blend-mode:overlay;min-height:100vh;display:flex;justify-content:center;align-items:center}.login-container{width:100%;max-width:450px;padding:2rem}.login-box{background:rgba(0,0,0,.85);border:2px solid var(--dourado);border-radius:15px;padding:2.5rem;box-shadow:0 10px 30px rgba(0,0,0,.5);backdrop-filter:blur(5px)}.login-logo{width:120px;display:block;margin:0 auto 1.5rem}.login-box h2{text-align:center;color:var(--dourado);font-family:'Bangers',cursive;letter-spacing:2px;font-size:2.2rem;margin-bottom:1.5rem}.input-group{margin-bottom:1.5rem;position:relative}.input-group label{display:block;margin-bottom:.5rem;color:var(--dourado);font-weight:600}.input-group input{width:100%;padding:12px 15px 12px 40px;border:1px solid var(--dourado);border-radius:8px;background:rgba(255,255,255,.1);color:var(--branco);font-size:1rem}.input-group input:focus{outline:none;border-color:var(--dourado-claro);box-shadow:0 0 0 3px rgba(207,161,47,.3)}.input-group i{position:absolute;left:15px;top:38px;color:var(--dourado)}.btn-login{width:100%;padding:12px;background:var(--dourado);color:var(--branco);border:0;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;text-transform:uppercase;letter-spacing:1px}.btn-login:hover{background:var(--dourado-claro)}.register-link{text-align:center;margin-top:1.5rem;color:var(--cinza)}.register-link a{color:var(--dourado);font-weight:600}.alert{padding:12px;border-radius:8px;margin-bottom:1.5rem;text-align:center}.alert-danger{background:rgba(220,53,69,.2);border:1px solid rgba(220,53,69,.5);color:#f8d7da}@media(max-width:576px){.login-box{padding:1.5rem}.login-container{padding:1rem}}
    </style>
</head>
<body>
<div class="login-container"><div class="login-box">
<img src="img/logo.png" alt="MS Barbearia" class="login-logo"><h2>Área do Cliente</h2>
<?php if (isset($_GET['erro'])): ?><div class="alert alert-danger"><?php echo htmlspecialchars($_GET['erro'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
<form action="processa_login.php" method="POST" autocomplete="on">
<?php echo csrfField(); ?>
<div class="input-group"><label for="email">E-mail</label><i class="fas fa-envelope"></i><input type="email" id="email" name="email" autocomplete="email" placeholder="seu@email.com" required></div>
<div class="input-group"><label for="senha">Senha</label><i class="fas fa-lock"></i><input type="password" id="senha" name="senha" autocomplete="current-password" placeholder="••••••••" required></div>
<button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Entrar</button>
<p class="register-link">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</form></div></div>
</body></html>

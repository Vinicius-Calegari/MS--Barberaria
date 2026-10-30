<?php
declare(strict_types=1);
require_once __DIR__.'/config/security.php';
startSecureSession();
if(isset($_SESSION['usuario']['id'])){header('Location: agendamento.php');exit;}
$erro=trim((string)($_GET['erro']??''));
?>
<!doctype html>
<html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#08080a"><meta name="robots" content="noindex">
<title>Minha conta · MS Barbearia</title><link rel="icon" href="img/Logo.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="css/rework.css"><link rel="stylesheet" href="css/premium.css"><link rel="stylesheet" href="css/auth-studio.css">
</head>
<body class="auth-studio auth-client">
<div class="auth-studio-shell">
<section class="auth-story" aria-label="A experiência MS">
 <div class="story-overlay"></div><div class="story-top"><span class="story-index">01 / 02</span><span class="story-location"><span class="story-dot"></span> FERROS · MG</span></div>
 <div class="story-halo halo-first"></div><div class="story-halo halo-second"></div>
 <div class="story-content">
  <div class="story-tag"><i class="fa-solid fa-scissors"></i> A EXPERIÊNCIA CONTINUA AQUI</div>
  <h2>UM CORTE.<br>UMA <em>ATITUDE.</em><br>SEU MOMENTO.</h2>
  <p>Escolha seu horário, acompanhe suas reservas e viva a experiência MS do seu jeito.</p>
  <div class="story-benefits"><span><i class="fa-regular fa-calendar-check"></i> Reserva online</span><span><i class="fa-solid fa-bolt"></i> Prático e rápido</span><span><i class="fa-solid fa-crown"></i> Seu estilo</span></div>
 </div>
 <div class="story-foot"><span>MS BARBEARIA</span><span>FEITO COM PRESENÇA <i class="fa-solid fa-arrow-up-right-from-square"></i></span></div>
</section>
<main class="auth-workspace">
 <div class="auth-workspace-glow" aria-hidden="true"></div>
 <div class="auth-form-column">
  <div class="auth-header">
   <a href="index.php" class="auth-back" aria-label="Voltar à página inicial"><i class="fa-solid fa-arrow-left"></i> Voltar para o site</a>
   <a href="index.php" class="auth-lockup" aria-label="MS Barbearia — início">
    <span class="ms-emblem emblem-large"><img src="img/Logo.png" alt=""></span>
    <span class="lockup-text"><strong>MS <span>BARBEARIA</span></strong><small>ESTILO É IDENTIDADE.</small></span>
   </a>
  </div>
  <div class="auth-form-intro">
   <div class="auth-kicker"><span class="kicker-dash"></span> ÁREA DO CLIENTE <span class="auth-kicker-number">01 / ACESSO</span></div>
   <h1>SEU HORÁRIO.<br><em>SUAS REGRAS.</em></h1>
   <p>Entre na sua conta para reservar e acompanhar seus próximos atendimentos.</p>
  </div>
  <?php if($erro!==''):?><div class="alert-ms error" role="alert"><i class="fa-solid fa-circle-exclamation"></i> <?=htmlspecialchars($erro,ENT_QUOTES,'UTF-8')?></div><?php endif;?>
  <form class="studio-form" action="processa_login.php" method="POST" autocomplete="on">
    <?=csrfField()?>
    <div class="field-ms"><label for="email">Seu e-mail</label><div class="studio-input-wrap"><i class="fa-regular fa-envelope input-start" aria-hidden="true"></i><input class="control-ms" type="email" name="email" id="email" placeholder="voce@email.com" autocomplete="email" required autofocus></div></div>
    <div class="field-ms"><label for="senha">Senha</label><div class="studio-input-wrap"><i class="fa-solid fa-lock input-start" aria-hidden="true"></i><input class="control-ms" type="password" name="senha" id="senha" placeholder="Digite sua senha" autocomplete="current-password" required><button class="password-toggle" type="button" data-toggle-password="senha" aria-label="Mostrar senha" aria-pressed="false"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div></div>
    <button class="btn-ms primary studio-submit" type="submit"><span>Entrar na minha conta</span><i class="fa-solid fa-arrow-right"></i></button>
  </form>
  <div class="auth-secondary"><span>Novo por aqui?</span> <a href="cadastro.php">Crie sua conta <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
  <div class="auth-support"><i class="fa-solid fa-shield-halved"></i><span>Seus dados e sua agenda protegidos.</span></div>
  <div class="auth-legal">© <?=date('Y')?> MS BARBEARIA <span>•</span> FERROS, MINAS GERAIS</div>
 </div>
</main></div>
<script src="js/auth-studio.js" defer></script>
</body></html>

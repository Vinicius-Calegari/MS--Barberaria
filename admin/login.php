<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
if(adminIsAuthenticated()){header('Location: index.php');exit;}
$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 requireValidCsrf();
 $usuario=trim((string)($_POST['usuario']??''));
 $senha=(string)($_POST['senha']??'');
 if(attemptAdminLogin($usuario,$senha)){header('Location: index.php');exit;}
 $erro='Usuário ou senha administrativa incorretos.';
}
?>
<!doctype html>
<html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#08090b"><meta name="robots" content="noindex,nofollow">
<title>MS Studio · Acesso administrativo</title><link rel="icon" href="../img/Logo.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../css/rework.css"><link rel="stylesheet" href="../css/premium.css"><link rel="stylesheet" href="../css/auth-studio.css">
</head>
<body class="auth-studio auth-admin">
<div class="auth-studio-shell">
<section class="auth-story" aria-label="MS Studio — operação">
 <div class="story-overlay"></div><div class="story-top"><span class="story-index">MS STUDIO / 2026</span><span class="story-location"><span class="story-dot"></span> PAINEL PRIVADO</span></div>
 <div class="story-halo halo-first"></div><div class="story-halo halo-second"></div>
 <div class="story-content"><div class="story-tag"><i class="fa-solid fa-layer-group"></i> CENTRAL DE OPERAÇÕES MS</div>
 <h2>SEU NEGÓCIO.<br>SEU RITMO.<br><em>SEU CONTROLE.</em></h2>
 <p>Agenda, produtos, clientes, estoque e financeiro reunidos em uma única visão.</p>
 <div class="studio-module-grid">
  <div><i class="fa-regular fa-calendar-check"></i><strong>AGENDA</strong><small>Atendimentos e presença</small></div>
  <div><i class="fa-solid fa-shirt"></i><strong>MS STORE</strong><small>Produtos e pedidos</small></div>
  <div><i class="fa-solid fa-chart-line"></i><strong>FINANCEIRO</strong><small>Receita e resultados</small></div>
 </div></div>
 <div class="story-foot"><span>MS STUDIO</span><span>GESTÃO COM IDENTIDADE <i class="fa-solid fa-arrow-up-right-from-square"></i></span></div>
</section>
<main class="auth-workspace">
 <div class="auth-workspace-glow" aria-hidden="true"></div>
 <div class="auth-form-column">
  <div class="auth-header">
   <a href="../index.php" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar para o site</a>
   <a href="../index.php" class="auth-lockup" aria-label="MS Barbearia — página inicial">
    <span class="ms-emblem emblem-large"><img src="../img/Logo.png" alt=""></span>
    <span class="lockup-text"><strong>MS <span>STUDIO</span></strong><small>BUSINESS CONTROL CENTER</small></span>
   </a>
  </div>
  <div class="auth-form-intro"><div class="auth-kicker"><span class="kicker-dash"></span> ACESSO RESTRITO <span class="auth-kicker-number">ADMIN / 01</span></div>
   <h1>CONTROLE<br><em>NA SUA MÃO.</em></h1>
   <p>Acesse o painel de gestão com suas credenciais administrativas.</p>
  </div>
  <?php if($erro!==''):?><div class="alert-ms error" role="alert"><i class="fa-solid fa-circle-exclamation"></i> <?=htmlspecialchars($erro,ENT_QUOTES,'UTF-8')?></div><?php endif;?>
  <form class="studio-form" method="POST" autocomplete="off">
   <?=csrfField()?>
   <div class="field-ms"><label for="usuario">Usuário administrativo</label><div class="studio-input-wrap"><i class="fa-regular fa-user input-start" aria-hidden="true"></i><input class="control-ms" id="usuario" name="usuario" autocomplete="username" placeholder="Seu usuário de acesso" required autofocus></div></div>
   <div class="field-ms"><label for="senha">Senha de acesso</label><div class="studio-input-wrap"><i class="fa-solid fa-lock input-start" aria-hidden="true"></i><input class="control-ms" type="password" id="senha" name="senha" autocomplete="current-password" placeholder="Sua senha administrativa" required><button class="password-toggle" type="button" data-toggle-password="senha" aria-pressed="false" aria-label="Mostrar senha"><i class="fa-regular fa-eye" aria-hidden="true"></i></button></div></div>
   <button class="btn-ms primary studio-submit" type="submit"><span>Acessar MS Studio</span><i class="fa-solid fa-arrow-right"></i></button>
  </form>
  <div class="auth-support"><i class="fa-solid fa-shield-halved"></i><span>Ambiente reservado à administração da MS.</span></div>
  <div class="auth-legal">© <?=date('Y')?> MS STUDIO <span>•</span> ACESSO AUTORIZADO</div>
 </div>
</main></div>
<script src="../js/auth-studio.js" defer></script>
</body></html>

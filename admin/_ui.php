<?php
declare(strict_types=1);
require_once __DIR__.'/../config/security.php';
require_once __DIR__.'/../config/commerce.php';
function adminSidebar(string $active): void {
 $links=[
  ['index.php','Dashboard','fa-chart-pie','dashboard'],
  ['index.php#agenda','Agendamentos','fa-calendar-days','agenda'],
  ['servicos.php','Serviços e preços','fa-scissors','servicos'],
  ['produtos.php','Produtos e estoque','fa-shirt','produtos'],
  ['vendas.php','Pedidos e vendas','fa-bag-shopping','vendas'],
  ['financeiro.php','Financeiro','fa-chart-line','financeiro'],
  ['index.php#clientes','Clientes','fa-users','clientes'],
 ];
 echo '<aside class="admin-sidebar"><a class="admin-brand" href="index.php"><span class="ms-emblem"><img src="../img/Logo.png" alt="Símbolo do leão MS"></span><div><strong>MS STUDIO</strong><div class="muted" style="font-size:.7rem">Painel de gestão</div></div></a>';
 echo '<div class="admin-sidebar-label">MENU PRINCIPAL</div><nav class="admin-nav" aria-label="Menu administrativo">';
 foreach($links as [$href,$label,$icon,$id]){
   echo '<a href="'.msEscape($href).'" class="'.($active===$id?'active':'').'"><i class="fa-solid '.msEscape($icon).'"></i>'.msEscape($label).'</a>';
 }
 echo '<a href="../index.php" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i>Ver site</a></nav>';
 echo '<form action="logout.php" method="POST" class="admin-signout">'.csrfField().'<button type="submit" class="btn-ms ghost"><i class="fa-solid fa-right-from-bracket"></i> Sair do painel</button></form></aside>';
}
function adminNotice(string $error,string $success): void {
 if($error!=='')echo '<div class="alert-ms error" role="alert">'.msEscape($error).'</div>';
 if($success!=='')echo '<div class="alert-ms success" role="status">'.msEscape($success).'</div>';
}

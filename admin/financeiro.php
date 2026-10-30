<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/_ui.php';
require_once __DIR__.'/../config/database.php';
requireAdmin();$pdo=getDBConnection();
$tz=new DateTimeZone('America/Sao_Paulo');
$period=(string)($_GET['mes']??(new DateTimeImmutable('now',$tz))->format('Y-m'));
if(!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$period))$period=(new DateTimeImmutable('now',$tz))->format('Y-m');
$from=new DateTimeImmutable($period.'-01 00:00:00',$tz);$to=$from->modify('first day of next month');
$data=msReport($pdo,$from,$to);
$st=$pdo->prepare('SELECT id,descricao,categoria,valor,data_despesa FROM ms_despesas WHERE data_despesa>=? AND data_despesa<? ORDER BY data_despesa DESC,id DESC LIMIT 150');
$st->execute([$from->format('Y-m-d'),$to->format('Y-m-d')]);$expenses=$st->fetchAll();
$daily=[];
$stm=$pdo->prepare("SELECT DATE(data_agendamento) dia,COALESCE(SUM(preco_cobrado),0) receita FROM agendamentos WHERE comparecimento='compareceu' AND status<>'cancelado' AND data_agendamento>=? AND data_agendamento<? GROUP BY DATE(data_agendamento)");
$stm->execute([$from->format('Y-m-d H:i:s'),$to->format('Y-m-d H:i:s')]);
foreach($stm->fetchAll() as $r)$daily[(string)$r['dia']]=(float)$r['receita'];
$stm=$pdo->prepare("SELECT DATE(finalizado_em) dia,COALESCE(SUM(total),0) receita FROM ms_pedidos WHERE status='concluido' AND finalizado_em>=? AND finalizado_em<? GROUP BY DATE(finalizado_em)");
$stm->execute([$from->format('Y-m-d H:i:s'),$to->format('Y-m-d H:i:s')]);
foreach($stm->fetchAll() as $r)$daily[(string)$r['dia']]=($daily[(string)$r['dia']]??0)+(float)$r['receita'];
$points=[];
for($day=0;$day<(int)$from->format('t');$day++){
 $date=$from->modify('+'.$day.' days');
 $points[]=['day'=>(int)$date->format('d'),'value'=>(float)($daily[$date->format('Y-m-d')]??0)];
}
$max=max(1,max(array_column($points,'value')));
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Financeiro | MS Studio</title><link rel="icon" href="../img/Logo.ico"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../css/rework.css"><link rel="stylesheet" href="../css/premium.css"></head>
<body class="admin-premium"><div class="admin-layout"><?php adminSidebar('financeiro');?>
<main class="admin-content"><header class="admin-head"><div><span class="eyebrow">MS Studio / Inteligência de negócio</span><h1>Gestão financeira</h1><p class="muted">Serviços realizados + vendas concluídas − custo das peças − despesas lançadas.</p></div><form method="GET" class="admin-filters"><label for="mes" class="visually-hidden">Mês do relatório</label><input id="mes" class="control-ms" type="month" name="mes" value="<?=msEscape($period)?>" required><button class="btn-ms ghost">Aplicar</button></form></header>
<?php adminNotice((string)($_GET['erro']??''),(string)($_GET['sucesso']??''));?>
<section class="stats-grid finance-stats">
<div class="stat-card"><i class="fa-solid fa-chart-line"></i><span>Receita total</span><strong><?=msMoney($data['receita'])?></strong></div>
<div class="stat-card"><i class="fa-solid fa-scissors"></i><span>Receita dos cortes</span><strong><?=msMoney($data['servicos'])?></strong><small class="muted"><?=$data['atendimentos']?> atendimentos realizados</small></div>
<div class="stat-card"><i class="fa-solid fa-shirt"></i><span>Vendas de produtos</span><strong><?=msMoney($data['produtos'])?></strong><small class="muted"><?=$data['vendas']?> pedidos concluídos</small></div>
<div class="stat-card"><i class="fa-solid fa-box"></i><span>Custo das peças vendidas</span><strong><?=msMoney($data['custo'])?></strong></div>
<div class="stat-card"><i class="fa-solid fa-receipt"></i><span>Despesas cadastradas</span><strong><?=msMoney($data['despesas'])?></strong></div>
<div class="stat-card result-card"><i class="fa-solid fa-coins"></i><span>Resultado operacional estimado</span><strong><?=msMoney($data['resultado'])?></strong></div>
</section>
<section class="panel-ms"><div class="section-head"><div><span class="eyebrow">Desempenho</span><h2 class="section-title">RECEITA POR DIA</h2></div><p>Somente atendimentos marcados como compareceu e pedidos de produtos concluídos.</p></div><div class="finance-chart" role="img" aria-label="Gráfico da receita diária do mês">
<?php foreach($points as $p):?><div class="finance-bar-col" title="Dia <?=$p['day']?>: <?=msMoney($p['value'])?>"><div class="finance-bar" style="height:<?=max(2,round(100*$p['value']/$max,2))?>%"></div><small><?=($p['day']===1||$p['day']%5===0)?$p['day']:''?></small></div><?php endforeach;?>
</div></section>
<div class="finance-columns"><section class="panel-ms"><div class="section-head"><div><span class="eyebrow">Controle</span><h2 class="section-title">LANÇAR DESPESA</h2></div></div>
<form method="POST" action="despesa_action.php" class="admin-product-form">
<?=csrfField()?><input type="hidden" name="acao" value="criar"><input type="hidden" name="mes" value="<?=msEscape($period)?>">
<div class="field-ms"><label for="exp_nome">Descrição</label><input class="control-ms" id="exp_nome" name="descricao" maxlength="150" placeholder="Ex.: produtos de limpeza" required></div>
<div class="field-ms"><label for="exp_cat">Categoria</label><select class="control-ms" id="exp_cat" name="categoria"><option>Operação</option><option>Insumos</option><option>Aluguel</option><option>Energia</option><option>Equipamentos</option><option>Outros</option></select></div>
<div class="field-ms"><label for="exp_val">Valor (R$)</label><input class="control-ms" id="exp_val" name="valor" type="number" min="0.01" step="0.01" required></div>
<div class="field-ms"><label for="exp_date">Data</label><input class="control-ms" id="exp_date" type="date" name="data_despesa" value="<?=msEscape((new DateTimeImmutable('today',$tz))->format('Y-m-d'))?>" required></div>
<button type="submit" class="btn-ms primary form-wide"><i class="fa-solid fa-plus"></i> Registrar despesa</button>
</form></section>
<section class="panel-ms"><div class="section-head"><div><span class="eyebrow">Histórico</span><h2 class="section-title">DESPESAS DO MÊS</h2></div></div>
<?php if(!$expenses):?><p class="muted">Nenhuma despesa registrada neste mês.</p><?php else:?><div class="expense-list"><?php foreach($expenses as $exp):?><div class="expense-row"><div><strong><?=msEscape($exp['descricao'])?></strong><small><?=msEscape($exp['categoria'])?> · <?=msEscape(date('d/m/Y',strtotime((string)$exp['data_despesa'])))?></small></div><strong><?=msMoney((float)$exp['valor'])?></strong><form method="POST" action="despesa_action.php" onsubmit="return confirm('Excluir esta despesa?')"><?=csrfField()?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?=(int)$exp['id']?>"><input type="hidden" name="mes" value="<?=msEscape($period)?>"><button class="cart-close" type="submit" aria-label="Excluir despesa"><i class="fa-solid fa-trash"></i></button></form></div><?php endforeach;?></div><?php endif;?>
</section></div>
<p class="finance-disclaimer"><i class="fa-solid fa-circle-info"></i> Este é um resultado operacional estimado: não inclui custos não cadastrados, impostos, comissões ou taxas. Para lucro líquido real, registre todas as despesas e encargos. Custos de produtos são fotografados no momento do pedido.</p>
</main></div><script src="../js/premium.js" defer></script></body></html>

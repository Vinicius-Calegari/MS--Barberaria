<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/_ui.php';
require_once __DIR__.'/../config/database.php';
requireAdmin();$pdo=getDBConnection();
$status=trim((string)($_GET['status']??''));
if(!in_array($status,['','pendente','concluido','cancelado'],true))$status='';
$sql='SELECT id,cliente_nome,telefone,observacao,status,total,criado_em,finalizado_em FROM ms_pedidos';
$params=[];
if($status!==''){$sql.=' WHERE status=?';$params[]=$status;}
$sql.=' ORDER BY criado_em DESC,id DESC LIMIT 150';$st=$pdo->prepare($sql);$st->execute($params);$orders=$st->fetchAll();
$items=[];
if($orders){$ids=array_column($orders,'id');$placeholders=implode(',',array_fill(0,count($ids),'?'));$st=$pdo->prepare("SELECT pedido_id,nome_snapshot,preco_unitario,custo_unitario,quantidade,subtotal FROM ms_pedido_itens WHERE pedido_id IN ($placeholders) ORDER BY id");$st->execute($ids);foreach($st->fetchAll() as $row){$items[(int)$row['pedido_id']][]=$row;}}
$counts=$pdo->query("SELECT status,COUNT(*) total FROM ms_pedidos GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Pedidos | MS Studio</title><link rel="icon" href="../img/Logo.ico"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../css/rework.css"><link rel="stylesheet" href="../css/premium.css"></head>
<body class="admin-premium"><div class="admin-layout"><?php adminSidebar('vendas');?>
<main class="admin-content"><header class="admin-head"><div><span class="eyebrow">MS Studio / Comercial</span><h1>Pedidos & vendas</h1><p class="muted">Confirme apenas as vendas realmente concluídas. O estoque será atualizado automaticamente.</p></div><a class="btn-ms primary" href="produtos.php"><i class="fa-solid fa-plus"></i>Produtos</a></header>
<?php adminNotice((string)($_GET['erro']??''),(string)($_GET['sucesso']??''));?>
<section class="admin-mini-stats"><div class="stat-card"><span>Aguardando</span><strong><?=((int)($counts['pendente']??0))?></strong></div><div class="stat-card"><span>Concluídos</span><strong><?=((int)($counts['concluido']??0))?></strong></div><div class="stat-card"><span>Cancelados</span><strong><?=((int)($counts['cancelado']??0))?></strong></div></section>
<section class="panel-ms"><div class="section-head"><div><span class="eyebrow">Caixa e pedidos</span><h2 class="section-title">MOVIMENTAÇÕES</h2></div><p>A conclusão é registrada com data e hora. Os pedidos pendentes não entram no faturamento.</p></div>
<form class="admin-filters" method="GET"><select class="control-ms" name="status" aria-label="Filtrar status"><option value="">Todos os status</option><?php foreach(['pendente'=>'Pendente','concluido'=>'Concluído','cancelado'=>'Cancelado'] as $k=>$label):?><option value="<?=$k?>" <?=$status===$k?'selected':''?>><?=$label?></option><?php endforeach;?></select><button class="btn-ms ghost">Filtrar</button></form>
<?php if(!$orders):?><div class="empty-shop compact"><i class="fa-solid fa-bag-shopping"></i><h3>Sem pedidos por aqui.</h3><p>Os pedidos enviados da MS Store aparecerão nesta lista.</p></div><?php else:?>
<div class="order-list"><?php foreach($orders as $order):$id=(int)$order['id'];?>
<article class="order-card"><div class="order-head"><div><span class="order-id">PEDIDO #<?=$id?></span><h3><?=msEscape($order['cliente_nome'])?></h3><span class="muted"><?=msEscape(date('d/m/Y H:i',strtotime((string)$order['criado_em'])))?> · <?=msEscape($order['telefone'])?></span></div><div class="order-right"><strong><?=msMoney((float)$order['total'])?></strong><span class="status <?= $order['status']==='concluido'?'confirmado':($order['status']==='cancelado'?'cancelado':'pendente')?>"><?=msEscape($order['status'])?></span></div></div>
<div class="order-products"><?php foreach($items[$id]??[] as $item):?><div class="order-product"><span><?=(int)$item['quantidade']?>× <?=msEscape($item['nome_snapshot'])?></span><strong><?=msMoney((float)$item['subtotal'])?></strong></div><?php endforeach;?></div>
<?php if($order['observacao']):?><div class="order-note"><i class="fa-regular fa-note-sticky"></i> <?=msEscape($order['observacao'])?></div><?php endif;?>
<div class="order-actions"><a class="btn-ms ghost" target="_blank" rel="noopener noreferrer" href="https://wa.me/55<?=msEscape(preg_replace('/\D+/','',(string)$order['telefone']))?>?text=<?=rawurlencode('Olá, tudo bem? Aqui é a MS Barbearia sobre o pedido #'.$id.'.')?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
<?php if($order['status']==='pendente'):?>
<form method="POST" action="venda_action.php" onsubmit="return confirm('Confirmar que esta venda foi finalizada e dar baixa no estoque?')"><?=csrfField()?><input type="hidden" name="id" value="<?=$id?>"><input type="hidden" name="acao" value="concluir"><button class="btn-ms success"><i class="fa-solid fa-check"></i> Concluir venda</button></form>
<form method="POST" action="venda_action.php" onsubmit="return confirm('Cancelar este pedido?')"><?=csrfField()?><input type="hidden" name="id" value="<?=$id?>"><input type="hidden" name="acao" value="cancelar"><button class="btn-ms danger">Cancelar</button></form>
<?php endif;?>
<?php if($order['status']==='concluido'):?><form method="POST" action="venda_action.php" onsubmit="return confirm('Cancelar venda concluída? As unidades voltarão ao estoque e a receita será estornada.')"><?=csrfField()?><input type="hidden" name="id" value="<?=$id?>"><input type="hidden" name="acao" value="estornar"><button class="btn-ms ghost">Estornar venda</button></form><?php endif;?>
</div></article>
<?php endforeach;?></div><?php endif;?></section>
</main></div><script src="../js/premium.js" defer></script></body></html>

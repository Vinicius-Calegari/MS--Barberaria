<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/_ui.php';
require_once __DIR__.'/../config/database.php';
requireAdmin();$pdo=getDBConnection();
$busca=mb_substr(trim((string)($_GET['q']??'')),0,80);
$sql='SELECT id,nome,descricao,categoria,tamanho,preco,custo,estoque,ativo,destaque,(imagem IS NOT NULL) tem_imagem FROM ms_produtos';
$params=[];
if($busca!==''){$sql.=' WHERE nome LIKE ? OR categoria LIKE ?';$params=['%'.$busca.'%','%'.$busca.'%'];}
$sql.=' ORDER BY criado_em DESC,id DESC LIMIT 250';
$st=$pdo->prepare($sql);$st->execute($params);$produtos=$st->fetchAll();
$stats=msShopCounts($pdo);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Produtos e estoque | MS Studio</title><link rel="icon" href="../img/Logo.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../css/rework.css"><link rel="stylesheet" href="../css/premium.css"></head>
<body class="admin-premium"><div class="admin-layout"><?php adminSidebar('produtos');?>
<main class="admin-content">
<header class="admin-head"><div><span class="eyebrow">MS Studio / Catálogo</span><h1>Produtos & estoque</h1><p class="muted">Cadastre suas peças, envie fotos e acompanhe o estoque em tempo real.</p></div><a class="btn-ms ghost" href="../produtos.php" target="_blank" rel="noopener">Ver loja <i class="fa-solid fa-arrow-up-right-from-square"></i></a></header>
<?php adminNotice((string)($_GET['erro']??''),(string)($_GET['sucesso']??''));?>
<div class="demo-alert"><i class="fa-solid fa-circle-info"></i><span><strong>Produtos conceituais:</strong> itens com categoria terminada em "· DEMO" são exemplos de vitrine, não podem ser vendidos e não entram nos indicadores de estoque real. Para transformar um deles em produto verdadeiro, atualize nome, descrição, categoria, preço e estoque e confirme que a peça existe fisicamente.</span></div>
<section class="admin-mini-stats"><div class="stat-card"><span>Produtos disponíveis</span><strong><?=$stats['produtos']?></strong></div><div class="stat-card"><span>Estoque baixo (até 3)</span><strong><?=$stats['estoque_baixo']?></strong></div><div class="stat-card"><span>Pedidos aguardando</span><strong><?=$stats['pedidos_pendentes']?></strong></div></section>
<section class="panel-ms admin-add-product"><div class="section-head"><div><span class="eyebrow">Novo item</span><h2 class="section-title">CADASTRAR PRODUTO</h2></div><p>As imagens ficam armazenadas no banco de dados, não no disco temporário do Render.</p></div>
<form method="POST" action="produto_action.php" enctype="multipart/form-data" class="admin-product-form">
<?=csrfField()?><input type="hidden" name="acao" value="criar">
<div class="field-ms"><label for="p_nome">Nome *</label><input id="p_nome" class="control-ms" type="text" name="nome" maxlength="130" required placeholder="Ex.: Camiseta oversized MS"></div>
<div class="field-ms"><label for="p_categoria">Categoria *</label><input id="p_categoria" class="control-ms" name="categoria" maxlength="70" value="Roupas" required placeholder="Camisetas, bonés, calças..."></div>
<div class="field-ms"><label for="p_tamanho">Tamanhos / variação</label><input id="p_tamanho" class="control-ms" name="tamanho" maxlength="80" placeholder="Ex.: M (cadastre cada tamanho como um item)"></div>
<div class="field-ms"><label for="p_preco">Preço de venda (R$) *</label><input id="p_preco" class="control-ms" type="number" min="0.01" max="999999.99" step="0.01" name="preco" required></div>
<div class="field-ms"><label for="p_custo">Custo unitário (R$)</label><input id="p_custo" class="control-ms" type="number" min="0" max="999999.99" step="0.01" name="custo" value="0"></div>
<div class="field-ms"><label for="p_estoque">Estoque inicial *</label><input id="p_estoque" class="control-ms" type="number" min="0" max="100000" name="estoque" value="1" required></div>
<div class="field-ms form-wide"><label for="p_descricao">Descrição</label><textarea id="p_descricao" class="control-ms" name="descricao" maxlength="2000" rows="3" placeholder="Tecido, cores, caimento e outras informações..."></textarea></div>
<div class="field-ms"><label for="p_foto">Foto (JPEG, PNG ou WebP · até 1 MB)</label><input id="p_foto" class="control-ms file-input" type="file" name="foto" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div>
<div class="field-ms form-boolean"><label><input type="checkbox" name="destaque" value="1"> Destaque na loja</label><label><input type="checkbox" name="ativo" value="1" checked> Publicado</label></div>
<button class="btn-ms primary form-wide" type="submit"><i class="fa-solid fa-plus"></i> Cadastrar na MS Store</button></form>
</section>
<section class="panel-ms"><div class="section-head"><div><span class="eyebrow">Gestão de catálogo</span><h2 class="section-title">SEUS PRODUTOS</h2></div><p><?=count($produtos)?> produto(s) cadastrado(s)</p></div>
<form class="admin-filters" method="GET"><input class="control-ms" type="search" name="q" maxlength="80" placeholder="Pesquisar peça ou categoria" value="<?=msEscape($busca)?>"><button class="btn-ms ghost">Buscar</button></form>
<?php if(!$produtos):?><div class="empty-shop compact"><i class="fa-solid fa-shirt"></i><h3>Seu catálogo começa aqui.</h3><p>Cadastre uma peça acima para publicá-la no site.</p></div><?php else:?>
<div class="admin-product-grid"><?php foreach($produtos as $p):?>
<article class="admin-product-card"><div class="admin-product-top"><div class="admin-product-thumb"><?php if($p['tem_imagem']):?><img src="../produto-imagem.php?id=<?=(int)$p['id']?>" alt="Imagem de <?=msEscape($p['nome'])?>" loading="lazy"><?php else:?><i class="fa-solid fa-shirt"></i><?php endif;?></div><div><strong><?=msEscape($p['nome'])?></strong><p><?=msEscape($p['categoria'])?> · <?=msEscape($p['tamanho'])?></p><span class="status <?=str_ends_with((string)$p['categoria'],' · DEMO')?'pendente':(((int)$p['ativo'])?'confirmado':'cancelado')?>"><?=str_ends_with((string)$p['categoria'],' · DEMO')?'CONCEITO · não à venda':(((int)$p['ativo'])?'Publicado':'Oculto')?></span> <span class="status <?=((int)$p['estoque']<=3)?'pendente':'confirmado'?>">Estoque: <?=(int)$p['estoque']?></span></div></div>
<details class="admin-product-details"><summary>Editar produto <i class="fa-solid fa-chevron-down"></i></summary>
<form method="POST" action="produto_action.php" enctype="multipart/form-data" class="admin-edit-grid">
<?=csrfField()?><input type="hidden" name="acao" value="editar"><input type="hidden" name="id" value="<?=(int)$p['id']?>">
<label>Nome<input class="control-ms" name="nome" value="<?=msEscape($p['nome'])?>" maxlength="130" required></label><label>Categoria<input class="control-ms" name="categoria" value="<?=msEscape($p['categoria'])?>" maxlength="70" required></label>
<label>Tamanho / variação<input class="control-ms" name="tamanho" value="<?=msEscape($p['tamanho'])?>" maxlength="80"></label>
<label>Preço (R$)<input class="control-ms" type="number" name="preco" value="<?=number_format((float)$p['preco'],2,'.','')?>" min="0.01" step="0.01" required></label>
<label>Custo (R$)<input class="control-ms" type="number" name="custo" value="<?=number_format((float)$p['custo'],2,'.','')?>" min="0" step="0.01"></label>
<label>Estoque<input class="control-ms" type="number" name="estoque" value="<?=(int)$p['estoque']?>" min="0" max="100000" required></label>
<label class="form-wide">Descrição<textarea class="control-ms" name="descricao" maxlength="2000" rows="2"><?=msEscape($p['descricao'])?></textarea></label>
<label class="form-wide">Trocar foto (opcional)<input class="control-ms file-input" type="file" name="foto" accept=".jpg,.jpeg,.png,.webp"></label>
<div class="form-wide form-boolean"><label><input type="checkbox" name="ativo" value="1" <?=((int)$p['ativo'])?'checked':''?>> Publicado</label><label><input type="checkbox" name="destaque" value="1" <?=((int)$p['destaque'])?'checked':''?>> Destaque</label></div>
<button type="submit" class="btn-ms primary form-wide"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button></form></details>
</article><?php endforeach;?></div><?php endif;?>
</section></main></div><script src="../js/premium.js" defer></script></body></html>

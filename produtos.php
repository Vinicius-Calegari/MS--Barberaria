<?php
declare(strict_types=1);
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/commerce.php';
startSecureSession();
$pdo=getDBConnection();
$categoria=trim((string)($_GET['categoria']??''));
$termo=mb_substr(trim((string)($_GET['q']??'')),0,80);
$cats=$pdo->query("SELECT DISTINCT categoria FROM ms_produtos WHERE ativo=1 AND estoque>0 ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
$sql="SELECT id,nome,descricao,categoria,tamanho,preco,estoque,destaque,(imagem IS NOT NULL) tem_imagem FROM ms_produtos WHERE ativo=1 AND estoque>0";
$args=[];
if($categoria!==''&&in_array($categoria,$cats,true)){$sql.=" AND categoria=?";$args[]=$categoria;}
if($termo!==''){$sql.=" AND (nome LIKE ? OR descricao LIKE ?)";$args[]='%'.$termo.'%';$args[]='%'.$termo.'%';}
$sql.=" ORDER BY destaque DESC, criado_em DESC LIMIT 100";
$stmt=$pdo->prepare($sql);$stmt->execute($args);$produtos=$stmt->fetchAll();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Coleção MS Barber: roupas e acessórios selecionados em Ferros, Minas Gerais. Escolha suas peças e reserve pelo WhatsApp.">
<meta name="theme-color" content="#090909"><title>MS Store | Estilo além da cadeira</title>
<link rel="icon" href="img/Logo.ico"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="css/rework.css"><link rel="stylesheet" href="css/premium.css"></head>
<body class="store-page">
<div class="ambient-orb orb-one" aria-hidden="true"></div>
<header class="ms-nav"><div class="container-ms ms-nav-inner">
<a class="ms-brand" href="index.php"><img src="img/Logo.png" alt="Logo MS"><div><strong>MS BARBEARIA</strong><span>MS Store · Ferros MG</span></div></a>
<nav class="ms-links" aria-label="Navegação"><a href="index.php">Início</a><a href="index.php#servicos">Serviços</a><a class="nav-current" href="produtos.php">Loja</a><a href="index.php#contato">Contato</a></nav>
<div class="nav-actions"><button type="button" class="btn-ms ghost store-cart-button" data-open-cart><i class="fa-solid fa-bag-shopping"></i> Sacola <span class="cart-count" data-cart-count>0</span></button><a class="btn-ms primary" href="<?= isset($_SESSION['usuario']['id'])?'agendamento.php':'login.php' ?>">Agendar <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
</div></header>
<main>
<section class="store-hero"><div class="container-ms store-hero-grid">
<div class="reveal"><span class="eyebrow">MS Store · Nossa curadoria</span><h1 class="display">O ESTILO NÃO<br><em>TERMINA</em> NA CADEIRA.</h1><p>Peças para levar sua atitude para além da barbearia. Escolha, monte sua sacola e combine a retirada diretamente com a MS.</p><a href="#colecao" class="btn-ms primary">Explorar coleção <i class="fa-solid fa-arrow-down"></i></a></div>
<div class="store-hero-art tilt-card reveal" aria-hidden="true"><div class="fashion-orbit"><span>MS</span><small>EST. FERROS · MG</small></div><div class="store-art-ring"></div><div class="store-art-ring inner"></div><span class="store-art-label">IDENTIDADE EM CADA DETALHE</span></div>
</div></section>
<section class="section-ms" id="colecao"><div class="container-ms">
<div class="section-head"><div><span class="eyebrow">Vitrine MS</span><h2 class="section-title">A COLEÇÃO</h2></div><p>Escolha os produtos disponíveis. O pedido é enviado ao WhatsApp e só é confirmado após atendimento da loja.</p></div>
<form class="store-filters" action="produtos.php" method="GET">
<label class="store-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><input name="q" type="search" placeholder="Buscar sua próxima peça..." value="<?= msEscape($termo) ?>" maxlength="80" aria-label="Buscar produtos"></label>
<select name="categoria" class="control-ms" aria-label="Filtrar categoria"><option value="">Todas as categorias</option><?php foreach($cats as $cat):?><option value="<?=msEscape($cat)?>" <?=$categoria===$cat?'selected':''?>><?=msEscape($cat)?></option><?php endforeach;?></select>
<button class="btn-ms ghost" type="submit">Filtrar <i class="fa-solid fa-arrow-right"></i></button>
</form>
<?php if(!$produtos):?><div class="empty-shop reveal"><i class="fa-solid fa-shirt"></i><h3><?= $termo!==''||$categoria!==''?'Nenhum produto encontrado':'A nova coleção está chegando.' ?></h3><p><?= $termo!==''||$categoria!==''?'Tente outra pesquisa ou explore o catálogo completo.':'Acompanhe as novidades ou fale com a MS pelo WhatsApp.' ?></p><a class="btn-ms primary" href="<?= $termo!==''||$categoria!==''?'produtos.php':'https://wa.me/5531995262066' ?>"><?= $termo!==''||$categoria!==''?'Ver tudo':'Conversar no WhatsApp' ?></a></div><?php else:?>
<div class="shop-grid"><?php foreach($produtos as $produto):?>
<article class="shop-card tilt-card reveal" data-product-id="<?= (int)$produto['id'] ?>">
<div class="shop-photo"><?php if($produto['tem_imagem']):?><img loading="lazy" src="produto-imagem.php?id=<?=(int)$produto['id']?>" alt="<?=msEscape($produto['nome'])?>"><?php else:?><div class="shop-placeholder"><i class="fa-solid fa-shirt"></i><span>MS STORE</span></div><?php endif;?><?php if((int)$produto['destaque']):?><span class="shop-tag">DESTAQUE</span><?php endif;?></div>
<div class="shop-info"><div class="shop-category"><?=msEscape($produto['categoria'])?> · <?=msEscape($produto['tamanho'])?></div><h3><?=msEscape($produto['nome'])?></h3><p><?=msEscape(mb_strimwidth((string)$produto['descricao'],0,115,'…'))?></p><div class="shop-buy-row"><strong><?=msMoney((float)$produto['preco'])?></strong><button class="btn-ms primary add-to-cart" type="button" data-id="<?=(int)$produto['id']?>" data-name="<?=msEscape($produto['nome'])?>" data-price="<?=number_format((float)$produto['preco'],2,'.','')?>" data-stock="<?=(int)$produto['estoque']?>" aria-label="Adicionar <?=msEscape($produto['nome'])?> à sacola"><i class="fa-solid fa-plus"></i> Adicionar</button></div></div>
</article>
<?php endforeach;?></div><?php endif;?>
</div></section>
<section class="section-ms" style="padding-top:0"><div class="container-ms"><div class="cta-band store-cta"><div><span class="eyebrow">Sua próxima visita</span><h3>UM CORTE NOVO. UMA PEÇA NOVA.</h3><p>Marque seu horário e conheça a coleção pessoalmente.</p></div><a href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'cadastro.php'?>" class="btn-ms primary">Reservar horário <i class="fa-solid fa-arrow-right"></i></a></div></div></section>
</main>
<div class="cart-backdrop" data-close-cart hidden></div>
<aside class="cart-drawer" id="sacola" role="dialog" aria-modal="true" aria-label="Sacola de compras" aria-hidden="true">
<div class="cart-head"><div><span class="eyebrow">Seu estilo</span><h2>Sua sacola</h2></div><button type="button" class="cart-close" data-close-cart aria-label="Fechar sacola"><i class="fa-solid fa-xmark"></i></button></div>
<div class="cart-items" data-cart-items><p class="muted">Sua sacola está vazia.</p></div>
<div class="cart-bottom"><div class="cart-total">Subtotal <strong data-cart-total>R$ 0,00</strong></div><p class="cart-disclaimer">Pedido sujeito à confirmação e disponibilidade. Sem pagamento online.</p>
<form id="checkout-form"><input type="hidden" name="csrf_token" value="<?=msEscape(csrfToken())?>">
<div class="field-ms"><label for="comprador">Seu nome</label><input id="comprador" class="control-ms" name="nome" required minlength="2" maxlength="120" placeholder="Nome completo"></div>
<div class="field-ms"><label for="telefone-pedido">WhatsApp</label><input id="telefone-pedido" class="control-ms" name="telefone" required maxlength="25" placeholder="(31) 99999-9999" inputmode="tel"></div>
<div class="field-ms"><label for="obs-pedido">Observação (opcional)</label><input id="obs-pedido" class="control-ms" name="observacao" maxlength="600" placeholder="Cor, entrega, retirada..."></div>
<p class="cart-feedback" role="status" aria-live="polite"></p><button type="submit" class="btn-ms primary cart-submit">Pedir pelo WhatsApp <i class="fa-brands fa-whatsapp"></i></button>
</form></div></aside>
<footer class="ms-footer"><div class="container-ms footer-inner"><span>© <?=date('Y')?> MS Barbearia · Ferros, MG</span><span>Estilo também é atitude. <a href="index.php">Voltar para casa</a></span></div></footer>
<script src="js/premium.js" defer></script><script src="js/store.js" defer></script>
</body></html>

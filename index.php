<?php
declare(strict_types=1);
require_once __DIR__.'/config/security.php';
require_once __DIR__.'/config/commerce.php';
startSecureSession();
$servicos=require __DIR__.'/config/services.php';
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="MS Barbearia em Ferros, MG. Corte, barba, estilo e agora MS Store. Agende online e descubra a nova coleção.">
<meta name="theme-color" content="#090909"><title>MS BARBEARIA — Sua marca. Seu estilo.</title>
<link rel="icon" href="img/Logo.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="css/rework.css"><link rel="stylesheet" href="css/premium.css">
</head><body class="premium-home">
<div class="ambient-orb orb-one" aria-hidden="true"></div>
<div class="scroll-progress" aria-hidden="true"></div>
<header class="ms-nav"><div class="container-ms ms-nav-inner">
<a class="ms-brand" href="#inicio" aria-label="MS Barbearia: início"><span class="ms-emblem"><img src="img/Logo.png" alt="Símbolo do leão MS"></span><div><strong>MS BARBEARIA</strong><span>Ferros · Minas Gerais</span></div></a>
<button type="button" class="nav-mobile-toggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="ms-site-nav"><i class="fa-solid fa-bars"></i></button>
<nav class="ms-links" id="ms-site-nav" aria-label="Navegação principal">
<a href="#servicos">Serviços</a><a href="#experiencia">Experiência</a><a href="produtos.php">MS Store <span class="nav-new">NOVO</span></a><a href="#contato">Contato</a>
</nav>
<div class="nav-actions">
<a class="btn-ms ghost" href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'login.php'?>"><i class="fa-regular fa-user"></i> <?=isset($_SESSION['usuario']['id'])?'Minha agenda':'Entrar'?></a>
<a class="btn-ms primary" href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'cadastro.php'?>">Agendar <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
</div></div></header>
<main>
<section class="hero-ms hero-premium" id="inicio">
<div class="hero-grain" aria-hidden="true"></div>
<div class="container-ms hero-split">
<div class="hero-content-ms reveal">
<div class="hero-index"><span class="live-dot"></span> FERROS, MINAS GERAIS <span class="hero-index-separator"></span> DESDE SEMPRE, FEITO COM ATITUDE</div>
<span class="eyebrow">Seu próximo nível começa aqui</span>
<h1 class="display">O SEU ESTILO<br>NÃO PEDE<br><em>PERMISSÃO.</em></h1>
<p>Precisão em cada detalhe. Presença em cada corte. Na MS, você encontra mais que um serviço: encontra sua assinatura.</p>
<div class="hero-actions"><a class="btn-ms primary btn-large magnetic" href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'cadastro.php'?>">Marcar meu horário <i class="fa-solid fa-arrow-right"></i></a><a class="btn-ms ghost btn-large" href="produtos.php">Conhecer MS Store <i class="fa-solid fa-bag-shopping"></i></a></div>
<div class="hero-foot"><span><strong>07</strong> serviços especializados</span><span><i class="fa-solid fa-location-dot"></i> Ferros · MG</span><span><i class="fa-solid fa-star"></i> Feito pra você</span></div>
</div>
<div class="hero-stage tilt-card" aria-label="Marca MS em composição tridimensional">
<div class="stage-backdrop"></div><div class="stage-ring ring-outer"></div><div class="stage-ring ring-inner"></div>
<div class="ms-3d-object"><div class="ms-3d-disc"><span>MS</span><small>BARBEARIA</small></div><div class="ms-3d-shadow"></div></div>
<div class="stage-glow"></div><span class="stage-coordinate top">01 / THE MS EXPERIENCE</span><span class="stage-coordinate bottom">PRECISÃO · PRESENÇA · IDENTIDADE</span>
</div></div>
<div class="hero-scroll">SCROLL TO EXPLORE <span></span> 01 — 04</div>
</section>

<div class="marquee-band" aria-label="Qualidade e estilo"><div class="marquee-track" aria-hidden="true"><span>CORTE COM PERSONALIDADE</span><i class="fa-solid fa-star"></i><span>MS STORE</span><i class="fa-solid fa-star"></i><span>ATITUDE EM CADA DETALHE</span><i class="fa-solid fa-star"></i><span>FERROS · MG</span><i class="fa-solid fa-star"></i><span>CORTE COM PERSONALIDADE</span><i class="fa-solid fa-star"></i><span>MS STORE</span><i class="fa-solid fa-star"></i><span>ATITUDE EM CADA DETALHE</span><i class="fa-solid fa-star"></i><span>FERROS · MG</span></div></div>

<section class="section-ms premium-services" id="servicos"><div class="container-ms">
<div class="section-head reveal"><div><span class="eyebrow">01 / Nosso ofício</span><h2 class="section-title">SERVIÇOS QUE<br><em>DEFINEM</em> VOCÊ.</h2></div><p>Corte, acabamento e identidade. Escolha a experiência, veja o valor e reserve seu horário sem complicação.</p></div>
<div class="services-grid">
<?php $i=0;foreach($servicos as $nome=>$item):$i++;?>
<article class="service-card premium-service tilt-card reveal">
<div class="service-image"><img src="<?=msEscape($item['imagem'])?>" alt="<?=msEscape($nome)?>" loading="lazy"><span class="service-number"><?=str_pad((string)$i,2,'0',STR_PAD_LEFT)?></span></div>
<div class="service-body"><div class="service-top"><div class="service-name"><?=msEscape($nome)?></div><div class="price"><?=msMoney((float)$item['preco'])?></div></div>
<p><?=msEscape($item['descricao'])?></p><div class="service-meta"><span><i class="fa-regular fa-clock"></i> <?= (int)$item['duracao']?> minutos</span><span><i class="fa-solid <?=msEscape($item['icone'])?>"></i> MS EXPERIENCE</span></div>
<a class="service-link" href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php?servico='.rawurlencode($nome):'login.php'?>">Reservar serviço <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
</article>
<?php endforeach;?></div></div></section>

<section class="section-ms experience-section" id="experiencia"><div class="container-ms experience-grid">
<div class="experience-photos reveal"><img class="experience-main-img" src="img/Sobre-nos.jpeg" alt="Ambiente da MS Barbearia" loading="lazy"><div class="experience-portrait-note"><span>MS</span><small>MAIS QUE UMA BARBEARIA</small></div><div class="experience-frame"></div></div>
<div class="experience-copy reveal"><span class="eyebrow">02 / A experiência</span><h2 class="section-title">MAIS QUE<br>VISUAL.<br><em>É PRESENÇA.</em></h2><p>Da conversa ao último detalhe do acabamento, cada atendimento é pensado para respeitar quem você é — e valorizar como quer se apresentar ao mundo.</p>
<div class="experience-features"><div><span>01</span><strong>Horário reservado</strong><p>Você escolhe quando quer ser atendido e acompanha tudo pelo site.</p></div><div><span>02</span><strong>Identidade própria</strong><p>Do clássico ao ousado, seu corte precisa fazer sentido para você.</p></div><div><span>03</span><strong>Atendimento próximo</strong><p>Sem complicação, sem pressa. Técnica e cuidado em cada detalhe.</p></div></div>
<a class="btn-ms ghost" href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'cadastro.php'?>">Viver essa experiência <i class="fa-solid fa-arrow-right"></i></a></div></div></section>

<section class="section-ms home-store" id="loja"><div class="container-ms"><div class="store-preview-grid">
<div class="store-preview-copy reveal"><span class="eyebrow">03 / Apresentamos a MS Store</span><h2 class="section-title">VISTA A<br><em>SUA</em> ATITUDE.</h2><p>A identidade MS agora acompanha você fora da cadeira. Roupas e peças escolhidas para quem leva estilo a sério.</p><div class="store-preview-points"><span><i class="fa-solid fa-check"></i> Peças selecionadas</span><span><i class="fa-solid fa-check"></i> Pedido pelo WhatsApp</span><span><i class="fa-solid fa-check"></i> Retirada combinada</span></div><a class="btn-ms primary btn-large" href="produtos.php">Explorar a MS Store <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>
<div class="store-preview-art tilt-card reveal" aria-label="Ilustração da MS Store"><div class="orbit-text">IDENTIDADE · MS STORE · FERROS ·</div><div class="store-preview-emblem"><span>MS</span><small>STORE</small></div><span class="store-corner">EST. FERROS MG / 2026</span></div>
</div></div></section>

<section class="section-ms" id="contato"><div class="container-ms"><div class="section-head reveal"><div><span class="eyebrow">04 / Chegue mais</span><h2 class="section-title">FALE COM<br><em>A MS.</em></h2></div><p>Quer confirmar seu horário, tirar uma dúvida ou saber mais sobre a coleção? Estamos por aqui.</p></div>
<div class="contact-grid"><a class="contact-card contact-premium reveal" href="https://wa.me/5531995262066" target="_blank" rel="noopener noreferrer"><div class="contact-icon"><i class="fa-brands fa-whatsapp"></i></div><h3>Chama no WhatsApp</h3><p>(31) 9 9526-2066</p><span>Conversar <i class="fa-solid fa-arrow-up-right-from-square"></i></span></a><div class="contact-card contact-premium reveal"><div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div><h3>Onde estamos</h3><p>R. Prof. Otaviano de Brito, 27<br>Ferros · Minas Gerais</p><span>Venha nos conhecer</span></div><div class="contact-card contact-premium reveal"><div class="contact-icon"><i class="fa-regular fa-clock"></i></div><h3>Nosso horário</h3><p>Terça a sábado<br>09h às 19h</p><span>Agende seu momento</span></div></div></div></section>
<section class="final-cta"><div class="container-ms reveal"><span class="eyebrow">Tudo começa com uma escolha</span><h2>O PRÓXIMO<br><em>É O SEU.</em></h2><a href="<?=isset($_SESSION['usuario']['id'])?'agendamento.php':'cadastro.php'?>" class="btn-ms primary btn-large">Reservar agora <i class="fa-solid fa-arrow-right"></i></a></div></section>
</main>
<footer class="ms-footer"><div class="container-ms footer-inner"><div><strong>MS BARBEARIA</strong><span>© <?=date('Y')?> · Ferros, Minas Gerais · Todos os direitos reservados.</span></div><div><a href="produtos.php">MS Store</a><a href="admin/login.php">Área administrativa</a></div></div></footer>
<script src="js/premium.js" defer></script>
</body></html>

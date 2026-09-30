<?php

declare(strict_types=1);

require_once __DIR__ . '/config/security.php';
startSecureSession();
$servicos = require __DIR__ . '/config/services.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MS Barbearia em Ferros MG. Corte, barba, pigmentação, luzes e agendamento online.">
    <meta name="theme-color" content="#070707">
    <title>MS Barbearia | Corte, barba e estilo</title>
    <link rel="icon" href="img/Logo.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/rework.css">
</head>
<body>
<header class="ms-nav">
    <div class="container-ms ms-nav-inner">
        <a class="ms-brand" href="#inicio">
            <img src="img/Logo.png" alt="MS Barbearia">
            <div><strong>MS BARBEARIA</strong><span>Ferros · Minas Gerais</span></div>
        </a>
        <nav class="ms-links" aria-label="Navegação principal">
            <a href="#servicos">Serviços</a>
            <a href="#experiencia">Experiência</a>
            <a href="#contato">Contato</a>
        </nav>
        <div class="nav-actions">
            <?php if (isset($_SESSION['usuario']['id'])): ?>
                <a class="btn-ms ghost" href="agendamento.php"><i class="fa-regular fa-calendar"></i> Minha agenda</a>
            <?php else: ?>
                <a class="btn-ms ghost" href="login.php">Entrar</a>
            <?php endif; ?>
            <a class="btn-ms primary" href="<?= isset($_SESSION['usuario']['id']) ? 'agendamento.php' : 'cadastro.php' ?>"><i class="fa-solid fa-calendar-check"></i> Agendar</a>
        </div>
    </div>
</header>

<main>
    <section class="hero-ms" id="inicio">
        <div class="container-ms hero-content-ms">
            <span class="eyebrow">Barbearia · Estilo · Experiência</span>
            <h1 class="display">Seu visual.<br>Seu momento.</h1>
            <p>A MS Barbearia une técnica, cuidado e ambiente pensado para quem não quer apenas cortar o cabelo — quer sair com presença.</p>
            <div class="hero-actions">
                <a class="btn-ms primary" href="<?= isset($_SESSION['usuario']['id']) ? 'agendamento.php' : 'cadastro.php' ?>"><i class="fa-solid fa-calendar-check"></i> Reservar horário</a>
                <a class="btn-ms ghost" href="#servicos"><i class="fa-solid fa-arrow-down"></i> Ver serviços</a>
            </div>
            <div class="hero-chips">
                <span class="hero-chip"><i class="fa-solid fa-location-dot"></i> Ferros, MG</span>
                <span class="hero-chip"><i class="fa-regular fa-clock"></i> Ter–Sáb · 09h–19h</span>
                <span class="hero-chip"><i class="fa-brands fa-whatsapp"></i> Atendimento direto</span>
            </div>
        </div>
    </section>

    <section class="section-ms" id="servicos">
        <div class="container-ms">
            <div class="section-head">
                <div><span class="eyebrow">Escolha seu estilo</span><h2 class="section-title">Serviços</h2></div>
                <p>Valores e tempo estimado claros antes da reserva. Selecione o serviço e conclua o agendamento online.</p>
            </div>
            <div class="services-grid">
                <?php foreach ($servicos as $nome => $dados): ?>
                    <article class="service-card">
                        <div class="service-image"><img src="<?= htmlspecialchars($dados['imagem'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" alt="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"></div>
                        <div class="service-body">
                            <div class="service-top"><div class="service-name"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></div><div class="price">R$ <?= number_format((float)$dados['preco'], 2, ',', '.') ?></div></div>
                            <p><?= htmlspecialchars($dados['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="service-meta"><span><i class="fa-regular fa-clock"></i> ~<?= (int)$dados['duracao'] ?> min</span><span><i class="fa-solid <?= htmlspecialchars($dados['icone'], ENT_QUOTES, 'UTF-8') ?>"></i> MS</span></div>
                            <a class="btn-ms ghost" href="<?= isset($_SESSION['usuario']['id']) ? 'agendamento.php?servico='.rawurlencode($nome) : 'login.php' ?>">Agendar este serviço <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-ms" id="experiencia">
        <div class="container-ms about-grid">
            <div class="about-photo"><img src="img/Sobre-nos.jpeg" loading="lazy" alt="MS Barbearia"></div>
            <div>
                <span class="eyebrow">Mais que um corte</span>
                <h2 class="section-title">Detalhe em cada atendimento.</h2>
                <p class="muted">A proposta da MS é simples: atendimento próximo, técnica atual e uma experiência que respeita seu estilo.</p>
                <div class="feature-list">
                    <div class="feature"><i class="fa-solid fa-check"></i><div><strong>Reserva online</strong><span>Escolha data e horário com disponibilidade atualizada.</span></div></div>
                    <div class="feature"><i class="fa-solid fa-check"></i><div><strong>Serviços bem definidos</strong><span>Preço e proposta claros antes do atendimento.</span></div></div>
                    <div class="feature"><i class="fa-solid fa-check"></i><div><strong>Atendimento personalizado</strong><span>Seu estilo orienta a escolha do corte e acabamento.</span></div></div>
                </div>
                <a class="btn-ms primary" href="<?= isset($_SESSION['usuario']['id']) ? 'agendamento.php' : 'cadastro.php' ?>">Quero reservar</a>
            </div>
        </div>
    </section>

    <section class="section-ms" style="padding-top:20px">
        <div class="container-ms cta-band">
            <div><span class="eyebrow">Sem fila, sem complicação</span><h3>Seu próximo horário começa aqui.</h3><p>Crie sua conta, escolha o serviço e reserve em poucos passos.</p></div>
            <a class="btn-ms primary" href="<?= isset($_SESSION['usuario']['id']) ? 'agendamento.php' : 'cadastro.php' ?>"><i class="fa-solid fa-calendar-days"></i> Agendar agora</a>
        </div>
    </section>

    <section class="section-ms" id="contato">
        <div class="container-ms">
            <div class="section-head"><div><span class="eyebrow">Fale com a MS</span><h2 class="section-title">Contato</h2></div><p>Precisa confirmar algum detalhe antes do atendimento? Fale direto com a barbearia.</p></div>
            <div class="contact-grid">
                <a class="contact-card" href="https://wa.me/5531995262066" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i><h3>WhatsApp</h3><p>(31) 9 9526-2066</p></a>
                <div class="contact-card"><i class="fa-solid fa-location-dot"></i><h3>Endereço</h3><p>R. Prof. Otaviano de Brito, 27 · Ferros, MG</p></div>
                <div class="contact-card"><i class="fa-regular fa-clock"></i><h3>Horário</h3><p>Terça a sábado · 09h às 19h</p></div>
            </div>
        </div>
    </section>
</main>

<footer class="ms-footer">
    <div class="container-ms footer-inner"><div>© <?= date('Y') ?> MS Barbearia. Todos os direitos reservados.</div><div><a href="admin/login.php">Área administrativa</a></div></div>
</footer>
</body>
</html>

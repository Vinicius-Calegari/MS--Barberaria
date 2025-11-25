<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ms Barbearia em Ferros MG - Cortes profissionais, barba e produtos masculinos. Agende seu horário online.">
    <meta name="keywords" content="barbearia Ferros, corte masculino, barba, pigmentação, luzes, agendamento online">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://msbarbearia.com/">
    <meta property="og:title" content="Ms Barbearia - Cortes Profissionais em Ferros MG">
    <meta property="og:description" content="Cortes profissionais, barba e produtos masculinos. Agende seu horário online.">
    <meta property="og:image" content="https://msbarbearia.com/img/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://msbarbearia.com/">
    <meta property="twitter:title" content="Ms Barbearia - Cortes Profissionais em Ferros MG">
    <meta property="twitter:description" content="Cortes profissionais, barba e produtos masculinos. Agende seu horário online.">
    <meta property="twitter:image" content="https://msbarbearia.com/img/og-image.jpg">

    <!-- Schema.org markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Ms Barbearia",
      "image": "https://msbarbearia.com/img/logo.jpg",
      "telephone": "(31) 9 9526-2066",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "R. Prof. Otaviano de Brito, 27",
        "addressLocality": "Ferros",
        "addressRegion": "MG"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "09:00",
        "closes": "19:00"
      },
      "priceRange": "$$"
    }
    </script>

    <title>Ms Barbearia - Cortes Profissionais</title>
    <link rel="icon" type="image/x-icon" href="img/Logo.ico">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Permanent+Marker&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar-premium">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">
                    <div class="brand-logo-container">
                        <i class="fas fa-cut logo-icon"></i>
                        <div class="brand-text">
                            <span class="brand-name">MS BARBEARIA</span>
                            <small class="brand-tagline">Excelência em cuidados masculinos</small>
                        </div>
                    </div>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#inicio">
                                <i class="fas fa-home nav-icon"></i>
                                <span class="nav-text">Início</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#servicos">
                                <i class="fas fa-cut nav-icon"></i>
                                <span class="nav-text">Serviços</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#agendamento">
                                <i class="fas fa-calendar-alt nav-icon"></i>
                                <span class="nav-text">Agendamento</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contato">
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <span class="nav-text">Contato</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="ms-lg-3">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <a href="logout.php" class="btn btn-outline-gold ms-2">
                                <i class="fas fa-sign-out-alt nav-icon"></i>
                                <span class="nav-text">Sair</span>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-gold">
                                <i class="fas fa-user nav-icon"></i>
                                <span class="nav-text">Login</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section" id="inicio">
        <div class="hero-content">
            <div class="container">
                <h1 class="animate__animated animate__fadeInDown">MS BARBEARIA</h1>
                <p class="lead animate__animated animate__fadeInUp">Cortes profissionais, barba e produtos masculinos</p>
                <div class="animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="<?php echo isset($_SESSION['usuario']) ? 'agendamento.php' : 'login.php'; ?>" class="btn btn-gold">Agende Agora</a>
                   <a 
  href="https://api.whatsapp.com/send?phone=5531995262066&text=Olá!%20Gostaria%20de%20comprar%20roupas%20da%20MS%20Barbearia." 
  class="btn btn-outline-gold" 
  target="_blank"
>
  Compre Roupas
</a>

                </div>
            </div>
        </div>
    </section>

    <!-- Sobre Nós -->
    <section class="about-section" id="sobre">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate__animated animate__fadeIn">Sobre Nós</h2>
            </div>
            
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="about-image-wrapper animate__animated animate__fadeInLeft">
                        <img src="img/Sobre-nos.jpeg" alt="Equipe da Ms Barbearia" class="img-fluid">
                        <div class="about-image-badge">
                            <i class="fas fa-award"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="animate__animated animate__fadeInRight">
                        <p class="highlight-text">
                            A <span class="brand-highlight">MS Barbearia</span> nasceu do sonho de oferecer mais que um simples corte - uma experiência única de cuidados masculinos.
                        </p>
                        
                        <ul class="about-features">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Missão: Transformar aparências e elevar autoestimas</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Técnicas modernas com atendimento personalizado</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Profissionais qualificados e produtos premium</span>
                            </li>
                        </ul>
                        
                        <blockquote class="about-quote">
                            "Na MS Barbearia, cada detalhe é pensado para proporcionar a melhor experiência em cuidados masculinos."
                        </blockquote>
                        
                        <a href="#servicos" class="btn btn-gold">Nossos Serviços</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Destaques -->
    <section class="highlights">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 highlight-item animate__animated animate__fadeInUp">
                    <i class="fas fa-users fa-3x"></i>
                    <h3>+5000</h3>
                    <p>Clientes Atendidos</p>
                </div>
                <div class="col-md-3 col-6 highlight-item animate__animated animate__fadeInUp animate__delay-1s">
                    <i class="fas fa-user-tie fa-3x"></i>
                    <h3>2+</h3>
                    <p>Anos de Experiência</p>
                </div>
                <div class="col-md-3 col-6 highlight-item animate__animated animate__fadeInUp animate__delay-2s">
                    <i class="fas fa-thumbs-up fa-3x"></i>
                    <h3>100%</h3>
                    <p>Satisfação Garantida</p>
                </div>
                <div class="col-md-3 col-6 highlight-item animate__animated animate__fadeInUp animate__delay-3s">
                    <i class="fas fa-star fa-3x"></i>
                    <h3>4.9</h3>
                    <p>Avaliação Média</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços -->
    <section class="services-section" id="servicos">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate__animated animate__fadeIn">Nossos Serviços</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/degrade.png" alt="Corte Degradê">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Degradê</h3>
                            <p class="service-description">Corte moderno com transição suave entre os comprimentos</p>
                            <p class="service-price">R$ 50,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Degradê" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/social.png" alt="Corte Social">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Social</h3>
                            <p class="service-description">Corte clássico e elegante para ocasiões formais</p>
                            <p class="service-price">R$ 45,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Social" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/barba.jpg" alt="Barba">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Barba</h3>
                            <p class="service-description">Aparo, modelagem e cuidados com a barba</p>
                            <p class="service-price">R$ 35,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Barba" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/pigmentacao.jpg" alt="Pigmentação">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Pigmentação</h3>
                            <p class="service-description">Realce e definição para barba e sobrancelhas</p>
                            <p class="service-price">R$ 120,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Pigmentação" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/luzes.jpg" alt="Luzes">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Luzes</h3>
                            <p class="service-description">Técnica de coloração para realçar o visual</p>
                            <p class="service-price">R$ 90,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Luzes" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="service-item">
                        <div class="service-image">
                            <img src="img/nevou.jpg" alt="Nevou">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title">Nevou</h3>
                            <p class="service-description">Estilo único para quem busca diferenciação</p>
                            <p class="service-price">R$ 80,00</p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="agendamento.php?servico=Nevou" class="btn btn-sm btn-gold">Agendar</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-gold">Login para agendar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeria -->
    <section class="gallery-section" id="galeria">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate__animated animate__fadeIn">Galeria</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn">
                    <div class="gallery-item">
                        <img src="img/galeria1.png" alt="Ambiente da barbearia">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-1s">
                    <div class="gallery-item">
                        <img src="img/galeria3.png" alt="Cadeiras de barbeiro">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-2s">
                    <div class="gallery-item">
                        <img src="img/galeria2.png" alt="Atendimento ao cliente">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-3s">
                    <div class="gallery-item">
                        <img src="img/galeria4.png" alt="Produtos da barbearia">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn">
                    <div class="gallery-item">
                        <img src="img/galeria5.png" alt="Corte de cabelo">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-1s">
                    <div class="gallery-item">
                        <img src="img/galeria6.png" alt="Modelagem de barba">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-2s">
                    <div class="gallery-item">
                        <img src="img/galeria8.png" alt="Produtos de cuidado">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6 mb-4 animate__animated animate__fadeIn animate__delay-3s">
                    <div class="gallery-item">
                        <img src="img/galeria7.png" alt="Ambiente relaxante">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Localização -->
    <section class="location-section" id="contato">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate__animated animate__fadeIn">Localização</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-5 mb-5 mb-lg-0 animate__animated animate__fadeInLeft">
                    <div class="location-card">
                        <div class="location-info">
                            <h4>Onde nos encontrar</h4>
                            <p><i class="fas fa-map-marker-alt"></i> R. Prof. Otaviano de Brito, 27 - Centro, Ferros - MG</p>
                            <p><i class="fas fa-phone-alt"></i> (31) 9 9526-2066</p>
                            <p><i class="fas fa-envelope"></i> contato@msbarbearia.com</p>
                            <p><i class="fas fa-clock"></i> Terça a Sábado: 09:00 - 19:00</p>
                            
                            <div class="social-links mt-4">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7 animate__animated animate__fadeInRight">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3750.715254403225!2d-43.02472568508511!3d-19.94034998659237!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa5d9d9a5d9d9a5%3A0xa5d9d9a5d9d9a5!2sR.%20Prof.%20Otaviano%20de%20Brito%2C%2027%20-%20Centro%2C%20Ferros%20-%20MG%2C%2035800-000!5e0!3m2!1spt-BR!2sbr!4v1620000000000!5m2!1spt-BR!2sbr" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section class="testimonials-section" id="depoimentos">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate__animated animate__fadeIn">Depoimentos</h2>
            </div>
            
            <div class="row">
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">Melhor barbearia da região! O atendimento é impecável e o corte sempre sai exatamente como eu quero. Recomendo muito!</p>
                        <p class="testimonial-author">- João Silva</p>
                    </div>
                </div>
                
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">Fiz pigmentação na barba e o resultado foi incrível! Profissionais qualificados e ambiente super agradável.</p>
                        <p class="testimonial-author">- Carlos Oliveira</p>
                    </div>
                </div>
                
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">Comprei várias roupas na loja da barbearia e a qualidade é excelente. Além disso, o corte que fiz ficou perfeito!</p>
                        <p class="testimonial-author">- Pedro Santos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
   <!-- Rodapé -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <a href="#" class="footer-logo">
                        <i class="fas fa-cut"></i> MS BARBEARIA
                    </a>
                    <p class="footer-quote">"Excelência em cuidados masculinos desde 2010."</p>
                    
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/ms_barbeariamg"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/5531995262066"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Links</h5>
                        <ul>
                            <li><a href="#inicio">Início</a></li>
                            <li><a href="#servicos">Serviços</a></li>
                            <li><a href="#agendamento">Agendamento</a></li>
                            <li><a href="#contato">Contato</a></li>
                            <li><a href="politica-privacidade.php">Política de Privacidade</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Serviços</h5>
                        <ul>
                            <li><a href="#servicos">Cortes</a></li>
                            <li><a href="#servicos">Barba</a></li>
                            <li><a href="#servicos">Pigmentação</a></li>
                            <li><a href="#servicos">Luzes</a></li>
                            <li><a href="#servicos">Nevou</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-4">
                    <div class="footer-links">
                        <h5>Contato</h5>
                        <ul>
                            <li><i class="fas fa-map-marker-alt me-2"></i> R. Prof. Otaviano de Brito, 27 - Ferros, MG</li>
                            <li><i class="fas fa-phone-alt me-2"></i> (31) 9 9526-2066</li>
                            <li><i class="fas fa-envelope me-2"></i> contato@msbarbearia.com</li>
                            <li><i class="fas fa-clock me-2"></i> Ter-Sáb: 09:00 - 19:00</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> MS Barbearia. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/5531995262066" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Aviso de Cookies -->
    <div class="cookie-consent">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-3 mb-md-0">Este site utiliza cookies para melhorar sua experiência. Ao continuar navegando, você concorda com nossa <a href="politica-privacidade.php" class="text-white">Política de Privacidade</a>.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-sm btn-primary" id="accept-cookies">Aceitar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="scripts.js"></script>
    
    <script>
    // Verifica se o usuário já aceitou os cookies
    if (!localStorage.getItem('cookiesAceitos')) {
        document.querySelector('.cookie-consent').style.display = 'block';
    }
    
    // Aceitar cookies
    document.getElementById('accept-cookies').addEventListener('click', function() {
        localStorage.setItem('cookiesAceitos', 'true');
        document.querySelector('.cookie-consent').style.display = 'none';
    });
    
    // Scroll suave para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    </script>
</body>
</html>
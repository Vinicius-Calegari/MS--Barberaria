<?php
session_start();
require_once 'config/database.php';

// Processar agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $servico = $_POST['servico'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];

    // Validar dados
    if (empty($nome) || empty($telefone) || empty($servico) || empty($data) || empty($horario)) {
        $erro = "Preencha todos os campos!";
    } else {
        // Verificar disponibilidade
        $horariosAgendados = json_decode(file_get_contents('dados/horarios.json'), true);
        $chaveHorario = $data . ' ' . $horario;

        if (isset($horariosAgendados[$chaveHorario])) {
            $erro = "Horário já ocupado! Escolha outro.";
        } else {
            // Registrar horário
            $horariosAgendados[$chaveHorario] = [
                'nome' => $nome,
                'telefone' => $telefone,
                'servico' => $servico
            ];
            file_put_contents('dados/horarios.json', json_encode($horariosAgendados));

            // Redirecionar para WhatsApp
            $mensagem = "Novo Agendamento:%0A%0A" .
                       "*Nome:* $nome%0A" .
                       "*Telefone:* $telefone%0A" .
                       "*Serviço:* $servico%0A" .
                       "*Data:* " . date('d/m/Y', strtotime($data)) . "%0A" .
                       "*Horário:* $horario";
            
            header("Location: https://wa.me/5531995262066?text=" . urlencode($mensagem));
            exit();
        }
    }
}

// Se veio de um link de serviço
$servicoSelecionado = isset($_GET['servico']) ? $_GET['servico'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agendamento online para a MS Barbearia em Ferros MG - Cortes profissionais, barba e pigmentação.">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://msbarbearia.com/agendamento.php">
    <meta property="og:title" content="Agendamento Online | MS Barbearia">
    <meta property="og:description" content="Agende seu horário na melhor barbearia de Ferros MG">
    <meta property="og:image" content="https://msbarbearia.com/img/og-image.jpg">

    <title>Agendamento Online | MS Barbearia</title>
    <link rel="icon" type="image/x-icon" href="img/Logo.ico">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Permanent+Marker&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    
    <style>
        .agendamento-form {
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .horarios-disponiveis {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        
        .horario-btn {
            background: #333;
            border: 1px solid #d4af37;
            color: white;
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .horario-btn:hover {
            background: #d4af37;
            color: #000;
        }
        
        .horario-btn.ocupado {
            background: #ff4444;
            border-color: #ff4444;
            cursor: not-allowed;
        }
    </style>
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
                            <a class="nav-link" href="index.php#inicio">
                                <i class="fas fa-home nav-icon"></i>
                                <span class="nav-text">Início</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#servicos">
                                <i class="fas fa-cut nav-icon"></i>
                                <span class="nav-text">Serviços</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="agendamento.php">
                                <i class="fas fa-calendar-alt nav-icon"></i>
                                <span class="nav-text">Agendamento</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#contato">
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

    <!-- Seção de Agendamento -->
    <section class="hero-section-interna" style="background-image: url('img/barbearia-agendamento.jpg');">
        <div class="hero-content">
            <div class="container">
                <h1 class="animate__animated animate__fadeInDown">AGENDAMENTO ONLINE</h1>
                <p class="lead animate__animated animate__fadeInUp">Reserve seu horário com facilidade</p>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX">
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="agendamento-form animate__animated animate__fadeInUp">
                        <h2 class="text-center mb-4"><i class="fas fa-calendar-alt me-2"></i> Preencha seus dados</h2>
                        
                        <form method="POST" id="formAgendamento">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="servico" class="form-label">Serviço</label>
                                <select class="form-select" id="servico" name="servico" required>
                                    <option value="">Selecione...</option>
                                    <option value="Degradê" <?= $servicoSelecionado == 'Degradê' ? 'selected' : '' ?>>Degradê - R$ 50,00</option>
                                    <option value="Social" <?= $servicoSelecionado == 'Social' ? 'selected' : '' ?>>Social - R$ 45,00</option>
                                    <option value="Barba" <?= $servicoSelecionado == 'Barba' ? 'selected' : '' ?>>Barba - R$ 35,00</option>
                                    <option value="Pigmentação" <?= $servicoSelecionado == 'Pigmentação' ? 'selected' : '' ?>>Pigmentação - R$ 120,00</option>
                                    <option value="Luzes" <?= $servicoSelecionado == 'Luzes' ? 'selected' : '' ?>>Luzes - R$ 90,00</option>
                                    <option value="Nevou" <?= $servicoSelecionado == 'Nevou' ? 'selected' : '' ?>>Nevou - R$ 80,00</option>
                                    <option value="Corte+Barba" <?= $servicoSelecionado == 'Corte+Barba' ? 'selected' : '' ?>>Corte + Barba - R$ 70,00</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="data" class="form-label">Data</label>
                                    <input type="date" class="form-control" id="data" name="data" min="<?= date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="horario" class="form-label">Horário</label>
                                    <select class="form-select" id="horario" name="horario" required>
                                        <option value="">Selecione a data primeiro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-gold btn-lg">
                                    <i class="fas fa-calendar-check me-2"></i> Confirmar Agendamento
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mt-4 text-center animate__animated animate__fadeInUp animate__delay-1s">
                        <p>Problemas com o agendamento? <a href="https://wa.me/5531995262066" class="text-gold">Fale conosco no WhatsApp</a></p>
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
                            <li><a href="index.php#inicio">Início</a></li>
                            <li><a href="index.php#servicos">Serviços</a></li>
                            <li><a href="agendamento.php">Agendamento</a></li>
                            <li><a href="index.php#contato">Contato</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Serviços</h5>
                        <ul>
                            <li><a href="agendamento.php?servico=Degradê">Degradê</a></li>
                            <li><a href="agendamento.php?servico=Barba">Barba</a></li>
                            <li><a href="agendamento.php?servico=Pigmentação">Pigmentação</a></li>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="scripts.js"></script>
    
    <script>
    // Carregar horários disponíveis quando selecionar uma data
    $('#data').change(function() {
        var dataSelecionada = $(this).val();
        
        if (dataSelecionada) {
            $.ajax({
                url: 'buscar-horarios.php',
                type: 'POST',
                data: { data: dataSelecionada },
                success: function(response) {
                    $('#horario').html(response);
                }
            });
        }
    });

    // Máscara para telefone
    $('#telefone').inputmask('(99) 99999-9999');
    </script>
</body>
</html>
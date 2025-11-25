<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: agendamento.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - MS Barbearia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --preto: #000000;
            --dourado: #970f0f;
            --dourado-claro:  #df1010ff;
            --branco: #ffffff;
            --cinza: #f5f5f5;
            --vermelho: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--preto);
            color: var(--branco);
            background-image: url('img/bg-cadastro.jpg');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .register-container {
            width: 100%;
            max-width: 550px;
        }
        
        .register-box {
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid var(--dourado);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }
        
        .register-box:hover {
            transform: translateY(-5px);
        }
        
        .register-logo {
            width: 120px;
            display: block;
            margin: 0 auto 1.5rem;
            filter: drop-shadow(0 0 10px rgba(207, 161, 47, 0.5));
        }
        
        .register-box h2 {
            text-align: center;
            color: var(--dourado);
            font-family: 'Bangers', cursive;
            letter-spacing: 2px;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 1.5rem;
        }
        
        .form-row .input-group {
            flex: 1;
        }
        
        .input-group {
            margin-bottom: 1.2rem;
            position: relative;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dourado);
            font-weight: 600;
        }
        
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid var(--dourado);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--branco);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: var(--dourado-claro);
            box-shadow: 0 0 0 3px rgba(207, 161, 47, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 38px;
            color: var(--dourado);
        }
        
        .password-strength {
            height: 4px;
            background: #333;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background 0.3s ease;
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background: var(--dourado);
            color: var(--preto);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-register:hover {
            background: var(--dourado-claro);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(207, 161, 47, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--cinza);
        }
        
        .login-link a {
            color: var(--dourado);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--dourado-claro);
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #f8d7da;
        }
        
        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border: 1px solid rgba(25, 135, 84, 0.5);
            color: #d1e7dd;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        @media (max-width: 576px) {
            .register-box {
                padding: 1.5rem;
            }
            
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <img src="img/logo.png" alt="MS Barbearia" class="register-logo">
            <h2>Criar Conta</h2>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET['erro']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['sucesso']); ?>
                </div>
            <?php endif; ?>
            
            <form action="processa_cadastro.php" method="POST" id="registerForm">
                <div class="form-row">
                    <div class="input-group">
                        <label for="nome">Nome Completo</label>
                        <i class="fas fa-user"></i>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required>
                    </div>
                    <div class="input-group">
                        <label for="telefone">Telefone</label>
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="telefone" name="telefone" placeholder="(31) 99999-9999" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                </div>
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="senha">Senha</label>
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="data_nascimento" name="data_nascimento" required>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Cadastrar
                </button>
                
                <p class="login-link">Já tem conta? <a href="login.php">Faça login</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Validação de senha em tempo real
        const senhaInput = document.getElementById('senha');
        const strengthBar = document.getElementById('strengthBar');
        
        senhaInput.addEventListener('input', function() {
            const senha = this.value;
            let strength = 0;
            
            // Verifica o comprimento
            if (senha.length >= 8) strength += 1;
            if (senha.length >= 12) strength += 1;
            
            // Verifica caracteres especiais
            if (/[!@#$%^&*(),.?":{}|<>]/.test(senha)) strength += 1;
            
            // Verifica números
            if (/\d/.test(senha)) strength += 1;
            
            // Verifica letras maiúsculas e minúsculas
            if (/[a-z]/.test(senha) && /[A-Z]/.test(senha)) strength += 1;
            
            // Atualiza a barra de força
            const width = strength * 20;
            let color = '#dc3545'; // Vermelho
            
            if (strength >= 3) color = '#ffc107'; // Amarelo
            if (strength >= 4) color = '#28a745'; // Verde
            
            strengthBar.style.width = width + '%';
            strengthBar.style.background = color;
        });
        
        // Validação do formulário
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (senha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }
            
            if (senha.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres!');
                return false;
            }
            
            return true;
        });
        
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length > 0) {
                value = '(' + value;
            }
            
            if (value.length > 3) {
                value = value.substring(0, 3) + ') ' + value.substring(3);
            }
            
            if (value.length > 10) {
                value = value.substring(0, 10) + '-' + value.substring(10);
            }
            
            this.value = value;
        });
    </script>
</body>
</html>
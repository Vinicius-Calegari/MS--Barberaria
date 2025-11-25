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
    <title>Login - MS Barbearia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --preto: #000000;
            --dourado: #970f0f;
            --dourado-claro: #f80404ff;
            --branco: #ffffff;
            --cinza: #f5f5f5;
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
            background-image: url('img/bg-login.jpg');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
        }
        
        .login-box {
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid var(--dourado);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }
        
        .login-box:hover {
            transform: translateY(-5px);
        }
        
        .login-logo {
            width: 120px;
            display: block;
            margin: 0 auto 1.5rem;
            filter: drop-shadow(0 0 10px rgba(207, 161, 47, 0.5));
        }
        
        .login-box h2 {
            text-align: center;
            color: var(--dourado);
            font-family: 'Bangers', cursive;
            letter-spacing: 2px;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dourado);
            font-weight: 600;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid var(--dourado);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--branco);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus {
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
        
        .btn-login {
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
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-login:hover {
            background: var(--dourado-claro);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(207, 161, 47, 0.4);
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--cinza);
        }
        
        .register-link a {
            color: var(--dourado);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
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
        
        /* Responsividade */
        @media (max-width: 576px) {
            .login-box {
                padding: 1.5rem;
            }
            
            .login-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="img/logo.png" alt="MS Barbearia" class="login-logo">
            <h2>Área do Cliente</h2>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET['erro']); ?>
                </div>
            <?php endif; ?>
            
            <form action="processa_login.php" method="POST">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
                <p class="register-link">Não tem conta? <a href="cadastro.php" id="registerBtn">Cadastre-se</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
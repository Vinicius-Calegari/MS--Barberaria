<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($senha !== $confirmar_senha) {
        header("Location: cadastro.php?erro=As senhas não coincidem");
        exit();
    }
    
    try {
        $pdo = getDBConnection();
        
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            header("Location: cadastro.php?erro=Este e-mail já está cadastrado");
            exit();
        }
        
        // Inserir novo usuário
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios 
                              (nome, email, telefone, senha) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone, $senha_hash]);
        
        // Logar automaticamente
        session_start();
        $_SESSION['usuario'] = [
            'id' => $pdo->lastInsertId(),
            'nome' => $nome,
            'email' => $email
        ];
        
        header("Location: agendamento.php?sucesso=Cadastro realizado com sucesso!");
        exit();
    } catch (PDOException $e) {
        header("Location: cadastro.php?erro=Erro ao cadastrar usuário");
        exit();
    }
} else {
    header("Location: cadastro.php");
    exit();
}
?>
<?php
// Configurações básicas
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ms_barbearia');

function getDBConnection() {
    try {
        // Primeiro tenta conectar sem especificar o banco
        $dsn = "mysql:host=".DB_HOST.";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verifica se o banco existe, se não, cria
        $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".DB_NAME."'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("CREATE DATABASE ".DB_NAME." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        // Agora conecta ao banco específico
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
        return new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}

// Criação automática das tabelas (executa apenas uma vez)
function criarTabelas() {
    $pdo = getDBConnection();
    
    $sqlUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        telefone VARCHAR(20),
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $sqlAgendamentos = "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        servico VARCHAR(50) NOT NULL,
        barbeiro VARCHAR(50) NOT NULL,
        data_agendamento DATETIME NOT NULL,
        observacoes TEXT,
        status ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    
    $pdo->exec($sqlUsuarios);
    $pdo->exec($sqlAgendamentos);
}

// Executa a criação das tabelas
criarTabelas();
?>
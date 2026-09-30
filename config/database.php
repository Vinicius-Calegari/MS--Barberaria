<?php

declare(strict_types=1);

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function getDBConnection(): PDO
{
    $host = env('DB_HOST', 'localhost');
    $port = env('DB_PORT', '3306');
    $name = env('DB_NAME', 'ms_barbearia');
    $user = env('DB_USER', 'root');
    $password = env('DB_PASSWORD');

    try {
        return new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit('Não foi possível conectar ao banco de dados.');
    }
}

function criarTabelas(): void
{
    $pdo = getDBConnection();
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        telefone VARCHAR(20),
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        servico VARCHAR(50) NOT NULL,
        barbeiro VARCHAR(50) NOT NULL,
        data_agendamento DATETIME NOT NULL,
        observacoes TEXT,
        status ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
        CONSTRAINT fk_agendamento_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        INDEX idx_agendamentos_usuario_data (usuario_id, data_agendamento),
        INDEX idx_agendamentos_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

criarTabelas();

<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        telefone VARCHAR(20),
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        servico VARCHAR(50) NOT NULL,
        barbeiro VARCHAR(50) NOT NULL,
        data_agendamento DATETIME NOT NULL,
        observacoes TEXT,
        status ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
        CONSTRAINT fk_agendamento_usuario
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        INDEX idx_agendamentos_usuario_data (usuario_id, data_agendamento),
        INDEX idx_agendamentos_status (status),
        INDEX idx_agendamentos_data (data_agendamento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$check = $pdo->prepare(
    "SELECT COUNT(*)
     FROM information_schema.statistics
     WHERE table_schema = DATABASE()
       AND table_name = 'agendamentos'
       AND index_name = 'uq_agendamento_barbeiro_horario'"
);
$check->execute();

if ((int) $check->fetchColumn() === 0) {
    // Não escondemos problemas de dados: se houver duplicatas antigas, a migração falha
    // e o deploy é interrompido para que a inconsistência seja corrigida explicitamente.
    $pdo->exec(
        'ALTER TABLE agendamentos
         ADD CONSTRAINT uq_agendamento_barbeiro_horario
         UNIQUE (barbeiro, data_agendamento)'
    );
    echo "Restrição de horário único criada.\n";
} else {
    echo "Restrição de horário único já existe.\n";
}

echo "Migração concluída.\n";

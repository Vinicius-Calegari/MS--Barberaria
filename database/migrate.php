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
    "CREATE TABLE IF NOT EXISTS servicos (
        nome VARCHAR(50) PRIMARY KEY,
        preco DECIMAL(10,2) NOT NULL,
        duracao INT NOT NULL DEFAULT 60,
        ativo TINYINT(1) NOT NULL DEFAULT 1,
        atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$servicosPadrao = [
    ['Degradê', 50.00, 60],
    ['Social', 45.00, 60],
    ['Barba', 35.00, 45],
    ['Pigmentação', 120.00, 90],
    ['Luzes', 90.00, 90],
    ['Nevou', 80.00, 90],
    ['Corte+Barba', 70.00, 90],
];
$seed = $pdo->prepare('INSERT IGNORE INTO servicos (nome, preco, duracao) VALUES (?, ?, ?)');
foreach ($servicosPadrao as $servico) {
    $seed->execute($servico);
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        servico VARCHAR(50) NOT NULL,
        barbeiro VARCHAR(50) NOT NULL,
        data_agendamento DATETIME NOT NULL,
        preco_cobrado DECIMAL(10,2) NULL,
        observacoes TEXT,
        status ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
        comparecimento ENUM('aguardando','compareceu','faltou') NOT NULL DEFAULT 'aguardando',
        compareceu_em DATETIME NULL,
        CONSTRAINT fk_agendamento_usuario
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        INDEX idx_agendamentos_usuario_data (usuario_id, data_agendamento),
        INDEX idx_agendamentos_status (status),
        INDEX idx_agendamentos_data (data_agendamento),
        INDEX idx_agendamentos_comparecimento (comparecimento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?"
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

if (!columnExists($pdo, 'agendamentos', 'preco_cobrado')) {
    $pdo->exec('ALTER TABLE agendamentos ADD COLUMN preco_cobrado DECIMAL(10,2) NULL AFTER data_agendamento');
    echo "Coluna preco_cobrado criada.\n";
}
if (!columnExists($pdo, 'agendamentos', 'comparecimento')) {
    $pdo->exec("ALTER TABLE agendamentos ADD COLUMN comparecimento ENUM('aguardando','compareceu','faltou') NOT NULL DEFAULT 'aguardando' AFTER status");
    echo "Coluna comparecimento criada.\n";
}
if (!columnExists($pdo, 'agendamentos', 'compareceu_em')) {
    $pdo->exec('ALTER TABLE agendamentos ADD COLUMN compareceu_em DATETIME NULL AFTER comparecimento');
    echo "Coluna compareceu_em criada.\n";
}

$indexComparecimento = $pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = DATABASE() AND table_name = 'agendamentos' AND index_name = 'idx_agendamentos_comparecimento'"
);
$indexComparecimento->execute();
if ((int) $indexComparecimento->fetchColumn() === 0) {
    $pdo->exec('ALTER TABLE agendamentos ADD INDEX idx_agendamentos_comparecimento (comparecimento)');
}

$check = $pdo->prepare(
    "SELECT COUNT(*)
     FROM information_schema.statistics
     WHERE table_schema = DATABASE()
       AND table_name = 'agendamentos'
       AND index_name = 'uq_agendamento_barbeiro_horario'"
);
$check->execute();
if ((int) $check->fetchColumn() === 0) {
    $pdo->exec(
        'ALTER TABLE agendamentos
         ADD CONSTRAINT uq_agendamento_barbeiro_horario
         UNIQUE (barbeiro, data_agendamento)'
    );
    echo "Restrição de horário único criada.\n";
} else {
    echo "Restrição de horário único já existe.\n";
}

// Preenche somente registros antigos que ainda não possuem snapshot do preço.
$pdo->exec(
    "UPDATE agendamentos a
     JOIN servicos s ON s.nome = a.servico
     SET a.preco_cobrado = s.preco
     WHERE a.preco_cobrado IS NULL"
);

echo "Migração concluída.\n";

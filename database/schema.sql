CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS servicos (
    nome VARCHAR(50) PRIMARY KEY,
    preco DECIMAL(10,2) NOT NULL,
    duracao INT NOT NULL DEFAULT 60,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servico VARCHAR(50) NOT NULL,
    barbeiro VARCHAR(50) NOT NULL,
    data_agendamento DATETIME NOT NULL,
    preco_cobrado DECIMAL(10,2),
    observacoes TEXT,
    status ENUM('pendente','confirmado','cancelado') DEFAULT 'pendente',
    comparecimento ENUM('aguardando','compareceu','faltou') NOT NULL DEFAULT 'aguardando',
    compareceu_em DATETIME NULL,
    CONSTRAINT fk_agendamento_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT uq_agendamento_barbeiro_horario UNIQUE (barbeiro, data_agendamento),
    INDEX idx_agendamentos_usuario_data (usuario_id, data_agendamento),
    INDEX idx_agendamentos_status (status),
    INDEX idx_agendamentos_data (data_agendamento),
    INDEX idx_agendamentos_comparecimento (comparecimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO servicos (nome, preco, duracao) VALUES
('Degradê', 50.00, 60),
('Social', 45.00, 60),
('Barba', 35.00, 45),
('Pigmentação', 120.00, 90),
('Luzes', 90.00, 90),
('Nevou', 80.00, 90),
('Corte+Barba', 70.00, 90);

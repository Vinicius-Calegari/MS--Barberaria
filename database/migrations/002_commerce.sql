-- MS Barber Commerce · migration 002 · additive/idempotent
CREATE TABLE IF NOT EXISTS ms_produtos (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(130) NOT NULL,
 descricao TEXT NULL,
 categoria VARCHAR(70) NOT NULL DEFAULT 'Roupas',
 tamanho VARCHAR(80) NOT NULL DEFAULT 'Único',
 preco DECIMAL(10,2) NOT NULL,
 custo DECIMAL(10,2) NOT NULL DEFAULT 0,
 estoque INT UNSIGNED NOT NULL DEFAULT 0,
 imagem MEDIUMBLOB NULL,
 imagem_tipo VARCHAR(32) NULL,
 destaque TINYINT(1) NOT NULL DEFAULT 0,
 ativo TINYINT(1) NOT NULL DEFAULT 1,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_ms_produtos_ativos (ativo,destaque,id),
 INDEX idx_ms_produtos_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ms_pedidos (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 cliente_nome VARCHAR(120) NOT NULL,
 telefone VARCHAR(30) NOT NULL,
 observacao VARCHAR(600) NULL,
 status ENUM('pendente','concluido','cancelado') NOT NULL DEFAULT 'pendente',
 total DECIMAL(12,2) NOT NULL DEFAULT 0,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 finalizado_em DATETIME NULL,
 INDEX idx_ms_pedidos_status_data (status, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ms_pedido_itens (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 pedido_id BIGINT UNSIGNED NOT NULL,
 produto_id INT UNSIGNED NOT NULL,
 nome_snapshot VARCHAR(130) NOT NULL,
 preco_unitario DECIMAL(10,2) NOT NULL,
 custo_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
 quantidade INT UNSIGNED NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL,
 CONSTRAINT fk_ms_item_pedido FOREIGN KEY(pedido_id) REFERENCES ms_pedidos(id) ON DELETE CASCADE,
 CONSTRAINT fk_ms_item_produto FOREIGN KEY(produto_id) REFERENCES ms_produtos(id) ON DELETE RESTRICT,
 INDEX idx_ms_itens_produto (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ms_despesas (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 descricao VARCHAR(150) NOT NULL,
 categoria VARCHAR(70) NOT NULL DEFAULT 'Operação',
 valor DECIMAL(10,2) NOT NULL,
 data_despesa DATE NOT NULL,
 registrado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_ms_despesas_data (data_despesa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

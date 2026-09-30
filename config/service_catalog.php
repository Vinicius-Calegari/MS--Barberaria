<?php

declare(strict_types=1);

/**
 * Retorna o catálogo padrão mesclado com preços/estado persistidos no MySQL.
 * Se a tabela ainda não existir (ex.: primeira inicialização local), usa os defaults.
 */
function getServiceCatalog(PDO $pdo, bool $somenteAtivos = true): array
{
    $catalogo = require __DIR__ . '/services.php';

    try {
        $sql = 'SELECT nome, preco, duracao, descricao, imagem, icone, ativo FROM servicos';
        if ($somenteAtivos) {
            $sql .= ' WHERE ativo = 1';
        }
        $sql .= ' ORDER BY ordem ASC, nome ASC';

        $rows = $pdo->query($sql)->fetchAll();
        if (!$rows) {
            return $catalogo;
        }

        $resultado = [];
        foreach ($rows as $row) {
            $nome = (string) $row['nome'];
            $fallback = $catalogo[$nome] ?? [];
            $resultado[$nome] = [
                'preco' => (float) $row['preco'],
                'duracao' => (int) $row['duracao'],
                'descricao' => (string) ($row['descricao'] ?? ($fallback['descricao'] ?? '')),
                'imagem' => (string) ($row['imagem'] ?? ($fallback['imagem'] ?? 'img/Corte.jpg')),
                'icone' => (string) ($row['icone'] ?? ($fallback['icone'] ?? 'fa-scissors')),
                'ativo' => (bool) $row['ativo'],
            ];
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log('Catálogo usando fallback local: ' . $e->getMessage());
        return $catalogo;
    }
}

<?php

declare(strict_types=1);

function getServiceCatalog(PDO $pdo, bool $somenteAtivos = true): array
{
    $defaults = require __DIR__ . '/services.php';

    try {
        $sql = 'SELECT nome, preco, duracao, ativo FROM servicos';
        if ($somenteAtivos) {
            $sql .= ' WHERE ativo = 1';
        }
        $sql .= ' ORDER BY nome ASC';
        $rows = $pdo->query($sql)->fetchAll();

        if (!$rows) {
            return $defaults;
        }

        $resultado = [];
        foreach ($rows as $row) {
            $nome = (string) $row['nome'];
            $fallback = $defaults[$nome] ?? [
                'descricao' => 'Serviço MS Barbearia.',
                'imagem' => 'img/Corte.jpg',
                'icone' => 'fa-scissors',
            ];
            $resultado[$nome] = [
                'preco' => (float) $row['preco'],
                'duracao' => (int) $row['duracao'],
                'descricao' => (string) ($fallback['descricao'] ?? ''),
                'imagem' => (string) ($fallback['imagem'] ?? 'img/Corte.jpg'),
                'icone' => (string) ($fallback['icone'] ?? 'fa-scissors'),
                'ativo' => (bool) $row['ativo'],
            ];
        }
        return $resultado;
    } catch (PDOException $e) {
        error_log('Catálogo usando fallback local: ' . $e->getMessage());
        return $defaults;
    }
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/database.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

requireValidCsrf();

$nome = trim((string) ($_POST['nome'] ?? ''));
$precoRaw = str_replace(',', '.', trim((string) ($_POST['preco'] ?? '')));
$preco = filter_var($precoRaw, FILTER_VALIDATE_FLOAT);

if ($nome === '' || $preco === false || $preco < 0 || $preco > 9999.99) {
    header('Location: index.php?erro=' . rawurlencode('Informe um preço válido.'));
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('UPDATE servicos SET preco = ? WHERE nome = ? AND ativo = 1');
    $stmt->execute([(float) $preco, $nome]);

    if ($stmt->rowCount() === 0) {
        $existe = $pdo->prepare('SELECT COUNT(*) FROM servicos WHERE nome = ? AND ativo = 1');
        $existe->execute([$nome]);
        if ((int) $existe->fetchColumn() === 0) {
            header('Location: index.php?erro=' . rawurlencode('Serviço não encontrado.'));
            exit;
        }
    }

    header('Location: index.php?sucesso=' . rawurlencode('Preço de ' . $nome . ' atualizado para R$ ' . number_format((float)$preco, 2, ',', '.') . '.'));
    exit;
} catch (PDOException $e) {
    error_log('Falha ao atualizar preço: ' . $e->getMessage());
    header('Location: index.php?erro=' . rawurlencode('Não foi possível atualizar o preço.'));
    exit;
}

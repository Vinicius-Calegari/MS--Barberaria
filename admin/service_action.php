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
$duracao = filter_input(INPUT_POST, 'duracao', FILTER_VALIDATE_INT);
$ativo = isset($_POST['ativo']) ? 1 : 0;

if ($nome === '' || $preco === false || $preco < 0 || $preco > 9999.99 || !$duracao || $duracao < 15 || $duracao > 480) {
    header('Location: servicos.php?erro=' . rawurlencode('Dados do serviço inválidos.'));
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('UPDATE servicos SET preco = ?, duracao = ?, ativo = ? WHERE nome = ?');
    $stmt->execute([(float)$preco, (int)$duracao, $ativo, $nome]);

    $check = $pdo->prepare('SELECT COUNT(*) FROM servicos WHERE nome = ?');
    $check->execute([$nome]);
    if ((int)$check->fetchColumn() === 0) {
        header('Location: servicos.php?erro=' . rawurlencode('Serviço não encontrado.'));
        exit;
    }

    header('Location: servicos.php?sucesso=' . rawurlencode('Serviço atualizado. O novo valor já vale para novas reservas.'));
    exit;
} catch (PDOException $e) {
    error_log('Falha ao atualizar serviço: ' . $e->getMessage());
    header('Location: servicos.php?erro=' . rawurlencode('Não foi possível atualizar o serviço.'));
    exit;
}

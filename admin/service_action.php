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
    header('Location: servicos.php?erro=' . rawurlencode('Informe um preço válido.'));
    exit;
}

try {
    $pdo = getDBConnection();
    $atual = $pdo->prepare('SELECT duracao, ativo FROM servicos WHERE nome = ? LIMIT 1');
    $atual->execute([$nome]);
    $existente = $atual->fetch();
    if (!$existente) {
        header('Location: servicos.php?erro=' . rawurlencode('Serviço não encontrado.'));
        exit;
    }

    $duracaoRecebida = filter_input(INPUT_POST, 'duracao', FILTER_VALIDATE_INT);
    $duracao = $duracaoRecebida && $duracaoRecebida >= 15 && $duracaoRecebida <= 480
        ? (int) $duracaoRecebida
        : (int) $existente['duracao'];
    $ativo = array_key_exists('duracao', $_POST)
        ? (isset($_POST['ativo']) ? 1 : 0)
        : (int) $existente['ativo'];

    $stmt = $pdo->prepare('UPDATE servicos SET preco = ?, duracao = ?, ativo = ? WHERE nome = ?');
    $stmt->execute([(float)$preco, $duracao, $ativo, $nome]);

    $destino = array_key_exists('duracao', $_POST) ? 'servicos.php' : 'index.php#servicos';
    header('Location: ' . $destino . '?sucesso=' . rawurlencode('Serviço atualizado. O novo valor já vale para novas reservas.'));
    exit;
} catch (PDOException $e) {
    error_log('Falha ao atualizar serviço: ' . $e->getMessage());
    header('Location: servicos.php?erro=' . rawurlencode('Não foi possível atualizar o serviço.'));
    exit;
}

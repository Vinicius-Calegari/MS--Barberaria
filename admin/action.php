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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$acao = (string) ($_POST['acao'] ?? '');
$permitidas = [
    'confirmar' => 'confirmado',
    'cancelar' => 'cancelado',
    'reabrir' => 'pendente',
];

if (!$id || !isset($permitidas[$acao])) {
    header('Location: index.php?erro=' . rawurlencode('Ação inválida.'));
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('UPDATE agendamentos SET status = ? WHERE id = ?');
    $stmt->execute([$permitidas[$acao], $id]);
    header('Location: index.php?sucesso=' . rawurlencode('Agendamento atualizado.'));
    exit;
} catch (PDOException $e) {
    error_log('Falha administrativa no agendamento: ' . $e->getMessage());
    header('Location: index.php?erro=' . rawurlencode('Não foi possível atualizar o agendamento.'));
    exit;
}

<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/database.php';

startSecureSession();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

requireValidCsrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: agendamento.php?erro=Agendamento inválido');
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ? AND usuario_id = ? AND status <> 'cancelado'");
    $stmt->execute([$id, (int) $_SESSION['usuario']['id']]);

    if ($stmt->rowCount() !== 1) {
        header('Location: agendamento.php?erro=Agendamento não encontrado');
        exit;
    }

    header('Location: agendamento.php?sucesso=Agendamento cancelado com sucesso');
    exit;
} catch (PDOException $e) {
    error_log('Falha ao cancelar agendamento: ' . $e->getMessage());
    header('Location: agendamento.php?erro=Erro ao cancelar agendamento');
    exit;
}

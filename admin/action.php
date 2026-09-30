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

if (!$id) {
    header('Location: index.php?erro=' . rawurlencode('Agendamento inválido.'));
    exit;
}

try {
    $pdo = getDBConnection();

    if (in_array($acao, ['compareceu', 'faltou', 'limpar_comparecimento'], true)) {
        $consulta = $pdo->prepare('SELECT data_agendamento, status FROM agendamentos WHERE id = ? LIMIT 1');
        $consulta->execute([$id]);
        $agendamento = $consulta->fetch();
        if (!$agendamento) {
            header('Location: index.php?erro=' . rawurlencode('Agendamento não encontrado.'));
            exit;
        }

        if ($acao !== 'limpar_comparecimento') {
            if ((string) $agendamento['status'] === 'cancelado') {
                header('Location: index.php?erro=' . rawurlencode('Agendamento cancelado não pode registrar comparecimento.'));
                exit;
            }
            $tz = new DateTimeZone('America/Sao_Paulo');
            $horario = new DateTimeImmutable((string) $agendamento['data_agendamento'], $tz);
            $agora = new DateTimeImmutable('now', $tz);
            if ($horario > $agora) {
                header('Location: index.php?erro=' . rawurlencode('O comparecimento só pode ser registrado após o horário marcado.'));
                exit;
            }
        }

        if ($acao === 'compareceu') {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status='confirmado', comparecimento='compareceu', compareceu_em=NOW() WHERE id=?");
            $mensagem = 'Presença registrada com sucesso.';
        } elseif ($acao === 'faltou') {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status='confirmado', comparecimento='faltou', compareceu_em=NULL WHERE id=?");
            $mensagem = 'Falta registrada com sucesso.';
        } else {
            $stmt = $pdo->prepare("UPDATE agendamentos SET comparecimento='aguardando', compareceu_em=NULL WHERE id=?");
            $mensagem = 'Registro de comparecimento removido.';
        }
        $stmt->execute([$id]);
        header('Location: index.php?sucesso=' . rawurlencode($mensagem));
        exit;
    }

    $permitidas = [
        'confirmar' => 'confirmado',
        'cancelar' => 'cancelado',
        'reabrir' => 'pendente',
    ];

    if (!isset($permitidas[$acao])) {
        header('Location: index.php?erro=' . rawurlencode('Ação inválida.'));
        exit;
    }

    $stmt = $pdo->prepare("UPDATE agendamentos SET status=?, comparecimento=IF(?='cancelado','aguardando',comparecimento), compareceu_em=IF(?='cancelado',NULL,compareceu_em) WHERE id=?");
    $stmt->execute([$permitidas[$acao], $permitidas[$acao], $permitidas[$acao], $id]);
    header('Location: index.php?sucesso=' . rawurlencode('Agendamento atualizado.'));
    exit;
} catch (PDOException $e) {
    error_log('Falha administrativa no agendamento: ' . $e->getMessage());
    header('Location: index.php?erro=' . rawurlencode('Não foi possível atualizar o agendamento.'));
    exit;
}

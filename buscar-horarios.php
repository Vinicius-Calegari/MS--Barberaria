<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=UTF-8');

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    exit;
}

$tz = new DateTimeZone('America/Sao_Paulo');
$data = trim((string) ($_REQUEST['data'] ?? ''));
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $data, $tz);

if (!$date || $date->format('Y-m-d') !== $data) {
    http_response_code(400);
    echo '<option value="">Data inválida</option>';
    exit;
}

$hoje = new DateTimeImmutable('today', $tz);
if ($date < $hoje) {
    echo '<option value="">Escolha uma data futura</option>';
    exit;
}

$barbeiro = 'MS Barbearia';
$inicio = $date->format('Y-m-d 00:00:00');
$fim = $date->modify('+1 day')->format('Y-m-d 00:00:00');

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT DATE_FORMAT(data_agendamento, '%H:%i') AS horario
         FROM agendamentos
         WHERE barbeiro = ?
           AND data_agendamento >= ?
           AND data_agendamento < ?
           AND status <> 'cancelado'"
    );
    $stmt->execute([$barbeiro, $inicio, $fim]);
    $ocupados = array_fill_keys(array_column($stmt->fetchAll(), 'horario'), true);
} catch (PDOException $e) {
    error_log('Falha ao consultar horários: ' . $e->getMessage());
    http_response_code(500);
    echo '<option value="">Não foi possível consultar os horários</option>';
    exit;
}

$options = '<option value="">Selecione um horário</option>';
$agora = new DateTimeImmutable('now', $tz);

for ($h = 9; $h < 19; $h++) {
    $horario = sprintf('%02d:00', $h);
    $instante = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $data . ' ' . $horario, $tz);
    $passou = $instante !== false && $instante <= $agora;
    $ocupado = isset($ocupados[$horario]);
    $disabled = $passou || $ocupado;

    $label = $horario;
    if ($ocupado) {
        $label .= ' (Ocupado)';
    } elseif ($passou) {
        $label .= ' (Indisponível)';
    }

    $options .= sprintf(
        '<option value="%s"%s>%s</option>',
        htmlspecialchars($horario, ENT_QUOTES, 'UTF-8'),
        $disabled ? ' disabled' : '',
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
    );
}

echo $options;

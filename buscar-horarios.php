<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = trim($_POST['data'] ?? '');
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
if (!$date || $date->format('Y-m-d') !== $data) {
    http_response_code(400);
    echo '<option value="">Data inválida</option>';
    exit;
}

$arquivo = __DIR__ . '/dados/horarios.json';
$conteudo = is_file($arquivo) ? file_get_contents($arquivo) : '{}';
$horariosOcupados = json_decode($conteudo ?: '{}', true);
if (!is_array($horariosOcupados)) $horariosOcupados = [];

$options = '<option value="">Selecione um horário</option>';
for ($h = 9; $h < 19; $h++) {
    $horario = sprintf('%02d:00', $h);
    $ocupado = isset($horariosOcupados[$data . ' ' . $horario]);
    $options .= sprintf(
        '<option value="%s"%s>%s%s</option>',
        htmlspecialchars($horario, ENT_QUOTES, 'UTF-8'),
        $ocupado ? ' disabled' : '',
        htmlspecialchars($horario, ENT_QUOTES, 'UTF-8'),
        $ocupado ? ' (Ocupado)' : ''
    );
}

echo $options;

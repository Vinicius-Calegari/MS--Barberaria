<?php
require_once 'config/database.php';

$data = $_POST['data'];
$horariosOcupados = json_decode(file_get_contents('dados/horarios.json'), true);

// Horários de funcionamento (9h às 19h, de hora em hora)
$horariosDisponiveis = [];
for ($h = 9; $h < 19; $h++) {
    $horario = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    $chaveHorario = $data . ' ' . $horario;
    
    $horariosDisponiveis[] = [
        'horario' => $horario,
        'ocupado' => isset($horariosOcupados[$chaveHorario])
    ];
}

// Gerar opções para o select
$options = '<option value="">Selecione um horário</option>';
foreach ($horariosDisponiveis as $hd) {
    if (!$hd['ocupado']) {
        $options .= '<option value="' . $hd['horario'] . '">' . $hd['horario'] . '</option>';
    } else {
        $options .= '<option value="' . $hd['horario'] . '" disabled>' . $hd['horario'] . ' (Ocupado)</option>';
    }
}

echo $options;
?>
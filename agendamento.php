<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

startSecureSession();
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: login.php?erro=' . rawurlencode('Faça login para agendar.'));
    exit;
}

$servicos = require __DIR__ . '/config/services.php';
$tz = new DateTimeZone('America/Sao_Paulo');
$barbeiro = 'MS Barbearia';
$erro = trim((string) ($_GET['erro'] ?? ''));
$sucesso = trim((string) ($_GET['sucesso'] ?? ''));
$servicoSelecionado = trim((string) ($_GET['servico'] ?? ''));
if (!isset($servicos[$servicoSelecionado])) { $servicoSelecionado = ''; }

function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }

$pdo = getDBConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    $servico = trim((string) ($_POST['servico'] ?? ''));
    $data = trim((string) ($_POST['data'] ?? ''));
    $horario = trim((string) ($_POST['horario'] ?? ''));
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $data . ' ' . $horario, $tz);
    $formatoValido = $date && $date->format('Y-m-d H:i') === $data . ' ' . $horario;
    $hora = $formatoValido ? (int)$date->format('H') : -1;
    $minuto = $formatoValido ? (int)$date->format('i') : -1;
    $agora = new DateTimeImmutable('now', $tz);

    if (!isset($servicos[$servico])) {
        $erro = 'Selecione um serviço válido.';
    } elseif (!$formatoValido || $hora < 9 || $hora >= 19 || $minuto !== 0) {
        $erro = 'Selecione uma data e um horário válidos.';
    } elseif ($date <= $agora) {
        $erro = 'Escolha um horário futuro.';
    } else {
        $dataSql = $date->format('Y-m-d H:i:s');
        $precoVigente = (float) $servicos[$servico]['preco'];
        try {
            $pdo->beginTransaction();
            $reutilizar = $pdo->prepare("UPDATE agendamentos SET usuario_id=?, servico=?, preco_cobrado=?, status='pendente', comparecimento='aguardando', compareceu_em=NULL, observacoes=NULL WHERE barbeiro=? AND data_agendamento=? AND status='cancelado'");
            $reutilizar->execute([(int)$_SESSION['usuario']['id'], $servico, $precoVigente, $barbeiro, $dataSql]);
            if ($reutilizar->rowCount() === 0) {
                $inserir = $pdo->prepare("INSERT INTO agendamentos (usuario_id, servico, barbeiro, data_agendamento, preco_cobrado, status, comparecimento) VALUES (?, ?, ?, ?, ?, 'pendente', 'aguardando')");
                $inserir->execute([(int)$_SESSION['usuario']['id'], $servico, $barbeiro, $dataSql, $precoVigente]);
            }
            $pdo->commit();
            header('Location: agendamento.php?sucesso=' . rawurlencode('Horário reservado. Aguarde a confirmação da barbearia.'));
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $mysqlCode = (int)($e->errorInfo[1] ?? 0);
            if ($e->getCode() === '23000' || $mysqlCode === 1062) {
                $erro = 'Esse horário acabou de ser reservado. Escolha outro.';
            } else {
                error_log('Falha ao criar agendamento: ' . $e->getMessage());
                $erro = 'Não foi possível concluir o agendamento agora.';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT id, servico, barbeiro, data_agendamento, preco_cobrado, status, comparecimento FROM agendamentos WHERE usuario_id=? AND data_agendamento>=NOW() AND status<>'cancelado' ORDER BY data_agendamento ASC LIMIT 20");
$stmt->execute([(int)$_SESSION['usuario']['id']]);
$meusAgendamentos = $stmt->fetchAll();
$proximo = $meusAgendamentos[0] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Minha agenda | MS Barbearia</title>
    <link rel="icon" href="img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/rework.css">
</head>
<body class="dashboard-page">
<header class="dash-top">
    <div class="container-ms dash-top-inner">
        <a class="ms-brand" href="index.php"><img src="img/Logo.png" alt="MS"><div><strong>MS BARBEARIA</strong><span>Área do cliente</span></div></a>
        <div class="nav-actions">
            <a class="btn-ms ghost" href="index.php"><i class="fa-solid fa-house"></i> Site</a>
            <form action="logout.php" method="POST"><?= csrfField() ?><button class="btn-ms ghost" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Sair</button></form>
        </div>
    </div>
</header>

<main class="dash-main">
    <div class="container-ms">
        <div class="dash-heading">
            <div><span class="eyebrow">Olá, <?= h((string)$_SESSION['usuario']['nome']) ?></span><h1>Minha agenda</h1><p class="muted" style="margin:6px 0 0">Reserve um novo horário e acompanhe suas próximas visitas.</p></div>
            <?php if ($proximo): ?><div class="status <?= h((string)$proximo['status']) ?>">Próximo: <?= h((new DateTimeImmutable((string)$proximo['data_agendamento']))->format('d/m · H:i')) ?></div><?php endif; ?>
        </div>

        <?php if ($erro !== ''): ?><div class="alert-ms error"><?= h($erro) ?></div><?php endif; ?>
        <?php if ($sucesso !== ''): ?><div class="alert-ms success"><?= h($sucesso) ?></div><?php endif; ?>

        <div class="dash-grid">
            <section class="panel-ms">
                <span class="eyebrow">Novo atendimento</span>
                <h2 class="section-title" style="font-size:2.7rem;margin:7px 0 10px">Reservar horário</h2>
                <p class="muted">Escolha o serviço, a data e um dos horários realmente disponíveis.</p>
                <form method="POST" id="formAgendamento" style="margin-top:24px">
                    <?= csrfField() ?>
                    <div class="field-ms">
                        <label for="servico">Serviço</label>
                        <select class="control-ms" id="servico" name="servico" required>
                            <option value="">Selecione o serviço</option>
                            <?php foreach ($servicos as $nome => $dados): ?>
                                <option value="<?= h($nome) ?>" <?= $servicoSelecionado === $nome ? 'selected' : '' ?>><?= h($nome) ?> · R$ <?= number_format((float)$dados['preco'],2,',','.') ?> · ~<?= (int)$dados['duracao'] ?> min</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-grid">
                        <div class="field-ms"><label for="data">Data</label><input class="control-ms" type="date" id="data" name="data" min="<?= h((new DateTimeImmutable('today',$tz))->format('Y-m-d')) ?>" required></div>
                        <div class="field-ms"><label for="horario">Horário</label><select class="control-ms" id="horario" name="horario" required disabled><option value="">Selecione a data primeiro</option></select></div>
                    </div>
                    <button class="btn-ms primary" style="width:100%;margin-top:10px" type="submit"><i class="fa-solid fa-calendar-check"></i> Confirmar reserva</button>
                </form>
            </section>

            <aside class="panel-ms">
                <span class="eyebrow">Próximas visitas</span>
                <h2 class="section-title" style="font-size:2.7rem;margin:7px 0 18px">Agendamentos</h2>
                <?php if (!$meusAgendamentos): ?>
                    <div class="appointment"><strong>Nenhum horário futuro.</strong><span class="muted">Sua próxima reserva aparecerá aqui.</span></div>
                <?php else: ?>
                    <div class="appointment-list">
                    <?php foreach ($meusAgendamentos as $item): ?>
                        <article class="appointment">
                            <div class="appointment-head"><div><strong><?= h((string)$item['servico']) ?></strong><span class="muted"><?= h((new DateTimeImmutable((string)$item['data_agendamento']))->format('d/m/Y · H:i')) ?> · R$ <?= number_format((float)$item['preco_cobrado'],2,',','.') ?></span></div><span class="status <?= h((string)$item['status']) ?>"><?= h((string)$item['status']) ?></span></div>
                            <form action="cancelar_agendamento.php" method="POST" style="margin-top:13px" onsubmit="return confirm('Cancelar este agendamento?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="btn-ms danger" type="submit"><i class="fa-regular fa-circle-xmark"></i> Cancelar</button></form>
                        </article>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</main>
<script>
const dataInput=document.getElementById('data');const horarioSelect=document.getElementById('horario');
dataInput.addEventListener('change',async()=>{const data=dataInput.value;horarioSelect.disabled=true;horarioSelect.innerHTML='<option>Consultando...</option>';if(!data){horarioSelect.innerHTML='<option value="">Selecione a data primeiro</option>';return;}try{const r=await fetch(`buscar-horarios.php?data=${encodeURIComponent(data)}`,{headers:{'X-Requested-With':'XMLHttpRequest'}});horarioSelect.innerHTML=await r.text();horarioSelect.disabled=!r.ok;}catch(e){horarioSelect.innerHTML='<option value="">Erro ao consultar horários</option>';}});
</script>
</body>
</html>

<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

startSecureSession();

if (!isset($_SESSION['usuario']['id'])) {
    header('Location: login.php?erro=Faça login para agendar');
    exit;
}

$tz = new DateTimeZone('America/Sao_Paulo');
$barbeiro = 'MS Barbearia';
$servicos = [
    'Degradê' => 50,
    'Social' => 45,
    'Barba' => 35,
    'Pigmentação' => 120,
    'Luzes' => 90,
    'Nevou' => 80,
    'Corte+Barba' => 70,
];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$erro = trim((string) ($_GET['erro'] ?? ''));
$sucesso = trim((string) ($_GET['sucesso'] ?? ''));
$servicoSelecionado = trim((string) ($_GET['servico'] ?? ''));
if (!array_key_exists($servicoSelecionado, $servicos)) {
    $servicoSelecionado = '';
}

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();

    $servico = trim((string) ($_POST['servico'] ?? ''));
    $data = trim((string) ($_POST['data'] ?? ''));
    $horario = trim((string) ($_POST['horario'] ?? ''));

    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $data . ' ' . $horario, $tz);
    $formatoValido = $date && $date->format('Y-m-d H:i') === $data . ' ' . $horario;
    $hora = $formatoValido ? (int) $date->format('H') : -1;
    $minuto = $formatoValido ? (int) $date->format('i') : -1;
    $agora = new DateTimeImmutable('now', $tz);

    if (!isset($servicos[$servico])) {
        $erro = 'Selecione um serviço válido.';
    } elseif (!$formatoValido || $hora < 9 || $hora >= 19 || $minuto !== 0) {
        $erro = 'Selecione uma data e um horário válidos.';
    } elseif ($date <= $agora) {
        $erro = 'Escolha um horário futuro.';
    } else {
        $dataSql = $date->format('Y-m-d H:i:s');

        try {
            $pdo->beginTransaction();

            // Se o mesmo horário já existiu e foi cancelado, reaproveitamos a linha.
            // Isso mantém a restrição UNIQUE útil e libera o slot sem acumular duplicatas.
            $reutilizar = $pdo->prepare(
                "UPDATE agendamentos
                 SET usuario_id = ?, servico = ?, status = 'pendente', observacoes = NULL
                 WHERE barbeiro = ? AND data_agendamento = ? AND status = 'cancelado'"
            );
            $reutilizar->execute([
                (int) $_SESSION['usuario']['id'],
                $servico,
                $barbeiro,
                $dataSql,
            ]);

            if ($reutilizar->rowCount() === 0) {
                $inserir = $pdo->prepare(
                    "INSERT INTO agendamentos
                     (usuario_id, servico, barbeiro, data_agendamento, status)
                     VALUES (?, ?, ?, ?, 'pendente')"
                );
                $inserir->execute([
                    (int) $_SESSION['usuario']['id'],
                    $servico,
                    $barbeiro,
                    $dataSql,
                ]);
            }

            $pdo->commit();
            header('Location: agendamento.php?sucesso=' . rawurlencode('Agendamento realizado com sucesso.'));
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $mysqlCode = (int) ($e->errorInfo[1] ?? 0);
            if ($e->getCode() === '23000' || $mysqlCode === 1062) {
                $erro = 'Esse horário acabou de ser reservado. Escolha outro.';
            } else {
                error_log('Falha ao criar agendamento: ' . $e->getMessage());
                $erro = 'Não foi possível concluir o agendamento agora.';
            }
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT id, servico, barbeiro, data_agendamento, status
     FROM agendamentos
     WHERE usuario_id = ?
       AND data_agendamento >= NOW()
       AND status <> 'cancelado'
     ORDER BY data_agendamento ASC
     LIMIT 20"
);
$stmt->execute([(int) $_SESSION['usuario']['id']]);
$meusAgendamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agendamento online da MS Barbearia.">
    <title>Agendamento | MS Barbearia</title>
    <link rel="icon" type="image/x-icon" href="img/Logo.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body{background:#090909;color:#fff;min-height:100vh}.booking-shell{padding:110px 0 70px}.booking-card{background:#151515;border:1px solid rgba(151,15,15,.55);border-radius:18px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.35)}.booking-card h1,.booking-card h2{color:#fff}.text-brand{color:#d43131}.form-control,.form-select{background:#0e0e0e;border-color:#333;color:#fff}.form-control:focus,.form-select:focus{background:#0e0e0e;color:#fff;border-color:#970f0f;box-shadow:0 0 0 .2rem rgba(151,15,15,.2)}.form-select option{background:#111;color:#fff}.appointment-item{border:1px solid #2c2c2c;border-radius:14px;padding:16px;background:#0d0d0d}.muted{color:#aaa}.btn-brand{background:#970f0f;border-color:#970f0f;color:#fff}.btn-brand:hover{background:#bf1717;border-color:#bf1717;color:#fff}.topbar{position:fixed;z-index:20;top:0;left:0;right:0;background:rgba(0,0,0,.9);border-bottom:1px solid #222;backdrop-filter:blur(10px)}.brand{font-weight:800;letter-spacing:.04em}.logout-form{display:inline}.logout-form button{background:transparent;border:1px solid #555;color:#fff;border-radius:10px;padding:8px 13px}.logout-form button:hover{border-color:#970f0f;color:#ff8b8b}
    </style>
</head>
<body>
<header class="topbar">
    <div class="container py-3 d-flex align-items-center justify-content-between gap-3">
        <a href="index.php" class="brand text-white text-decoration-none"><i class="fas fa-cut text-brand me-2"></i>MS BARBEARIA</a>
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-md-inline muted">Olá, <?= h((string) $_SESSION['usuario']['nome']) ?></span>
            <form class="logout-form" action="logout.php" method="POST">
                <?= csrfField() ?>
                <button type="submit"><i class="fas fa-sign-out-alt me-1"></i>Sair</button>
            </form>
        </div>
    </div>
</header>

<main class="booking-shell">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="booking-card">
                    <div class="mb-4">
                        <span class="text-brand fw-bold text-uppercase small">Reserva online</span>
                        <h1 class="mt-2">Escolha seu horário</h1>
                        <p class="muted mb-0">Os horários são consultados diretamente no banco e o sistema impede duas reservas para o mesmo slot.</p>
                    </div>

                    <?php if ($erro !== ''): ?>
                        <div class="alert alert-danger"><?= h($erro) ?></div>
                    <?php endif; ?>
                    <?php if ($sucesso !== ''): ?>
                        <div class="alert alert-success"><?= h($sucesso) ?></div>
                    <?php endif; ?>

                    <form method="POST" id="formAgendamento" novalidate>
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label for="servico" class="form-label">Serviço</label>
                            <select class="form-select" id="servico" name="servico" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($servicos as $nomeServico => $preco): ?>
                                    <option value="<?= h($nomeServico) ?>" <?= $servicoSelecionado === $nomeServico ? 'selected' : '' ?>>
                                        <?= h($nomeServico) ?> — R$ <?= number_format($preco, 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="data" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data" name="data" min="<?= h((new DateTimeImmutable('today', $tz))->format('Y-m-d')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="horario" class="form-label">Horário</label>
                                <select class="form-select" id="horario" name="horario" required disabled>
                                    <option value="">Selecione a data primeiro</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand btn-lg w-100 mt-4">
                            <i class="fas fa-calendar-check me-2"></i>Confirmar agendamento
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="booking-card h-100">
                    <h2 class="h4 mb-3">Próximos agendamentos</h2>
                    <?php if (!$meusAgendamentos): ?>
                        <p class="muted">Você ainda não possui reservas futuras.</p>
                    <?php else: ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($meusAgendamentos as $agendamento): ?>
                                <div class="appointment-item">
                                    <div class="fw-bold"><?= h((string) $agendamento['servico']) ?></div>
                                    <div class="muted small mt-1">
                                        <i class="far fa-calendar me-1"></i>
                                        <?= h((new DateTimeImmutable((string) $agendamento['data_agendamento']))->format('d/m/Y H:i')) ?>
                                    </div>
                                    <div class="muted small mt-1">Status: <?= h((string) $agendamento['status']) ?></div>
                                    <form action="cancelar_agendamento.php" method="POST" class="mt-3" onsubmit="return confirm('Cancelar este agendamento?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int) $agendamento['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Cancelar</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const dataInput = document.getElementById('data');
const horarioSelect = document.getElementById('horario');

dataInput.addEventListener('change', async () => {
    const data = dataInput.value;
    horarioSelect.disabled = true;
    horarioSelect.innerHTML = '<option value="">Carregando...</option>';

    if (!data) {
        horarioSelect.innerHTML = '<option value="">Selecione a data primeiro</option>';
        return;
    }

    try {
        const response = await fetch(`buscar-horarios.php?data=${encodeURIComponent(data)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const html = await response.text();
        horarioSelect.innerHTML = html;
        horarioSelect.disabled = !response.ok;
    } catch (error) {
        horarioSelect.innerHTML = '<option value="">Erro ao consultar horários</option>';
    }
});
</script>
</body>
</html>

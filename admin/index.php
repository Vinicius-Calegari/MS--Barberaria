<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/database.php';

requireAdmin();

$servicos = require __DIR__ . '/../config/services.php';
$pdo = getDBConnection();
$tz = new DateTimeZone('America/Sao_Paulo');
$hoje = new DateTimeImmutable('today', $tz);
$dataFiltro = trim((string) ($_GET['data'] ?? $hoje->format('Y-m-d')));
$statusFiltro = trim((string) ($_GET['status'] ?? ''));
$busca = trim((string) ($_GET['q'] ?? ''));
$erro = trim((string) ($_GET['erro'] ?? ''));
$sucesso = trim((string) ($_GET['sucesso'] ?? ''));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFiltro)) {
    $dataFiltro = $hoje->format('Y-m-d');
}
$statuses = ['pendente', 'confirmado', 'cancelado'];
if ($statusFiltro !== '' && !in_array($statusFiltro, $statuses, true)) {
    $statusFiltro = '';
}

$totalClientes = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();

$stmtHoje = $pdo->prepare("SELECT a.*, u.nome, u.email, u.telefone FROM agendamentos a JOIN usuarios u ON u.id = a.usuario_id WHERE DATE(a.data_agendamento) = ? ORDER BY a.data_agendamento ASC");
$stmtHoje->execute([$hoje->format('Y-m-d')]);
$agendaHoje = $stmtHoje->fetchAll();
$totalHoje = count(array_filter($agendaHoje, fn($a) => $a['status'] !== 'cancelado'));
$pendentesHoje = count(array_filter($agendaHoje, fn($a) => $a['status'] === 'pendente'));
$receitaHoje = 0.0;
foreach ($agendaHoje as $item) {
    if ($item['status'] !== 'cancelado' && isset($servicos[$item['servico']])) {
        $receitaHoje += (float) $servicos[$item['servico']]['preco'];
    }
}

$inicioMes = $hoje->modify('first day of this month')->format('Y-m-d 00:00:00');
$fimMes = $hoje->modify('first day of next month')->format('Y-m-d 00:00:00');
$stmtMes = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data_agendamento >= ? AND data_agendamento < ? AND status <> 'cancelado'");
$stmtMes->execute([$inicioMes, $fimMes]);
$totalMes = (int) $stmtMes->fetchColumn();

$sql = "SELECT a.id, a.servico, a.barbeiro, a.data_agendamento, a.status, a.observacoes,
               u.nome, u.email, u.telefone
        FROM agendamentos a
        JOIN usuarios u ON u.id = a.usuario_id
        WHERE DATE(a.data_agendamento) = ?";
$params = [$dataFiltro];
if ($statusFiltro !== '') {
    $sql .= ' AND a.status = ?';
    $params[] = $statusFiltro;
}
if ($busca !== '') {
    $sql .= ' AND (u.nome LIKE ? OR u.email LIKE ? OR u.telefone LIKE ? OR a.servico LIKE ?)';
    $like = '%' . $busca . '%';
    array_push($params, $like, $like, $like, $like);
}
$sql .= ' ORDER BY a.data_agendamento ASC LIMIT 100';
$stmtAgenda = $pdo->prepare($sql);
$stmtAgenda->execute($params);
$agenda = $stmtAgenda->fetchAll();

$clientes = $pdo->query("SELECT u.id, u.nome, u.email, u.telefone, u.data_cadastro,
    COUNT(a.id) AS total_agendamentos,
    MAX(a.data_agendamento) AS ultimo_agendamento
    FROM usuarios u
    LEFT JOIN agendamentos a ON a.usuario_id = u.id
    GROUP BY u.id
    ORDER BY u.data_cadastro DESC
    LIMIT 10")->fetchAll();

function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function statusClass(string $status): string { return in_array($status, ['pendente','confirmado','cancelado'], true) ? $status : 'pendente'; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Dashboard | MS Barbearia</title>
    <link rel="icon" href="../img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/rework.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand"><img src="../img/Logo.png" alt="MS"><div><strong>MS GESTÃO</strong><div class="muted" style="font-size:.7rem">Painel administrativo</div></div></div>
        <nav class="admin-nav">
            <a class="active" href="#dashboard"><i class="fa-solid fa-chart-line"></i>Dashboard</a>
            <a href="#agenda"><i class="fa-solid fa-calendar-days"></i>Agenda</a>
            <a href="#clientes"><i class="fa-solid fa-users"></i>Clientes</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i>Ver site</a>
        </nav>
        <form action="logout.php" method="POST" style="margin-top:28px">
            <?= csrfField() ?>
            <button type="submit" class="btn-ms ghost" style="width:100%"><i class="fa-solid fa-right-from-bracket"></i>Sair</button>
        </form>
    </aside>

    <main class="admin-content" id="dashboard">
        <header class="admin-head">
            <div><span class="eyebrow">Operação</span><h1>Visão geral</h1><p class="muted" style="margin:4px 0 0">Acompanhe a agenda e tome ações sem sair do painel.</p></div>
            <div class="status confirmado"><i class="fa-solid fa-circle" style="font-size:.45rem;margin-right:6px"></i> sistema online</div>
        </header>

        <?php if ($erro !== ''): ?><div class="alert-ms error"><?= h($erro) ?></div><?php endif; ?>
        <?php if ($sucesso !== ''): ?><div class="alert-ms success"><?= h($sucesso) ?></div><?php endif; ?>

        <section class="stats-grid">
            <article class="stat-card"><i class="fa-solid fa-calendar-check"></i><span>Atendimentos hoje</span><strong><?= $totalHoje ?></strong></article>
            <article class="stat-card"><i class="fa-solid fa-clock"></i><span>Pendentes hoje</span><strong><?= $pendentesHoje ?></strong></article>
            <article class="stat-card"><i class="fa-solid fa-brazilian-real-sign"></i><span>Receita prevista hoje</span><strong>R$ <?= number_format($receitaHoje, 2, ',', '.') ?></strong></article>
            <article class="stat-card"><i class="fa-solid fa-users"></i><span>Clientes cadastrados</span><strong><?= $totalClientes ?></strong><small class="muted"><?= $totalMes ?> reservas no mês</small></article>
        </section>

        <section class="admin-columns">
            <div class="panel-ms" id="agenda">
                <div class="section-head" style="margin-bottom:18px"><div><span class="eyebrow">Agenda</span><h2 class="section-title" style="font-size:2.2rem">Atendimentos</h2></div><span class="muted"><?= count($agenda) ?> resultado(s)</span></div>
                <form class="admin-filters" method="GET">
                    <input class="control-ms" type="date" name="data" value="<?= h($dataFiltro) ?>">
                    <select class="control-ms" name="status">
                        <option value="">Todos os status</option>
                        <?php foreach ($statuses as $st): ?><option value="<?= $st ?>" <?= $statusFiltro === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
                    </select>
                    <input class="control-ms" name="q" value="<?= h($busca) ?>" placeholder="Cliente, e-mail, telefone...">
                    <button class="btn-ms ghost" type="submit"><i class="fa-solid fa-filter"></i>Filtrar</button>
                </form>
                <div class="table-wrap">
                    <table class="table-ms">
                        <thead><tr><th>Horário</th><th>Cliente</th><th>Serviço</th><th>Status</th><th>Ações</th></tr></thead>
                        <tbody>
                        <?php if (!$agenda): ?><tr><td colspan="5" class="muted">Nenhum agendamento encontrado para os filtros selecionados.</td></tr><?php endif; ?>
                        <?php foreach ($agenda as $item): ?>
                            <tr>
                                <td><strong><?= h((new DateTimeImmutable((string)$item['data_agendamento']))->format('H:i')) ?></strong><div class="muted"><?= h((new DateTimeImmutable((string)$item['data_agendamento']))->format('d/m/Y')) ?></div></td>
                                <td><strong><?= h((string)$item['nome']) ?></strong><div class="muted"><?= h((string)$item['telefone']) ?><br><?= h((string)$item['email']) ?></div></td>
                                <td><?= h((string)$item['servico']) ?><?php if(isset($servicos[$item['servico']])): ?><div class="muted">R$ <?= number_format((float)$servicos[$item['servico']]['preco'],2,',','.') ?></div><?php endif; ?></td>
                                <td><span class="status <?= statusClass((string)$item['status']) ?>"><?= h((string)$item['status']) ?></span></td>
                                <td>
                                    <div class="row-actions">
                                    <?php if ($item['status'] !== 'confirmado'): ?><form method="POST" action="action.php"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><input type="hidden" name="acao" value="confirmar"><button class="btn-ms success">Confirmar</button></form><?php endif; ?>
                                    <?php if ($item['status'] !== 'cancelado'): ?><form method="POST" action="action.php" onsubmit="return confirm('Cancelar este agendamento?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><input type="hidden" name="acao" value="cancelar"><button class="btn-ms danger">Cancelar</button></form><?php else: ?><form method="POST" action="action.php"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><input type="hidden" name="acao" value="reabrir"><button class="btn-ms ghost">Reabrir</button></form><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="panel-ms" id="clientes">
                <div style="margin-bottom:18px"><span class="eyebrow">CRM básico</span><h2 class="section-title" style="font-size:2.2rem">Clientes recentes</h2></div>
                <div class="customer-list">
                    <?php foreach ($clientes as $cliente): ?>
                        <div class="customer"><strong><?= h((string)$cliente['nome']) ?></strong><span><?= h((string)$cliente['telefone']) ?> · <?= (int)$cliente['total_agendamentos'] ?> agendamento(s)</span><span><?= h((string)$cliente['email']) ?></span></div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </section>
    </main>
</div>
</body>
</html>

<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/database.php';

requireAdmin();
$pdo = getDBConnection();
$erro = trim((string) ($_GET['erro'] ?? ''));
$sucesso = trim((string) ($_GET['sucesso'] ?? ''));
$servicos = $pdo->query('SELECT nome, preco, duracao, ativo, atualizado_em FROM servicos ORDER BY nome ASC')->fetchAll();

function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Serviços e preços | MS Gestão</title>
    <link rel="icon" href="../img/Logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/rework.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand"><img src="../img/Logo.png" alt="MS"><div><strong>MS GESTÃO</strong><div class="muted" style="font-size:.7rem">Painel administrativo</div></div></div>
        <nav class="admin-nav">
            <a href="index.php"><i class="fa-solid fa-chart-line"></i>Dashboard</a>
            <a href="index.php#agenda"><i class="fa-solid fa-calendar-days"></i>Agenda</a>
            <a class="active" href="servicos.php"><i class="fa-solid fa-tags"></i>Preços</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i>Ver site</a>
        </nav>
        <form action="logout.php" method="POST" style="margin-top:28px"><?= csrfField() ?><button type="submit" class="btn-ms ghost" style="width:100%"><i class="fa-solid fa-right-from-bracket"></i>Sair</button></form>
    </aside>

    <main class="admin-content">
        <header class="admin-head">
            <div><span class="eyebrow">Catálogo</span><h1>Serviços e preços</h1><p class="muted" style="margin:4px 0 0">O preço salvo aqui aparece no site e nos novos agendamentos imediatamente.</p></div>
            <a class="btn-ms ghost" href="index.php"><i class="fa-solid fa-arrow-left"></i>Dashboard</a>
        </header>

        <?php if ($erro !== ''): ?><div class="alert-ms error"><?= h($erro) ?></div><?php endif; ?>
        <?php if ($sucesso !== ''): ?><div class="alert-ms success"><?= h($sucesso) ?></div><?php endif; ?>

        <section class="panel-ms">
            <div class="section-head" style="margin-bottom:20px">
                <div><span class="eyebrow">Tabela de preços</span><h2 class="section-title" style="font-size:2.3rem">Edite sem alterar código</h2></div>
                <p>Agendamentos já existentes mantêm o preço registrado no momento da reserva; alterações valem para novas reservas.</p>
            </div>
            <div class="table-wrap">
                <table class="table-ms">
                    <thead><tr><th>Serviço</th><th>Preço</th><th>Duração</th><th>Disponível</th><th>Ação</th></tr></thead>
                    <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td><strong><?= h((string)$servico['nome']) ?></strong><?php if (!empty($servico['atualizado_em'])): ?><div class="muted">Atualizado em <?= h((new DateTimeImmutable((string)$servico['atualizado_em']))->format('d/m/Y H:i')) ?></div><?php endif; ?></td>
                            <td colspan="4">
                                <form method="POST" action="service_action.php" class="price-form">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="nome" value="<?= h((string)$servico['nome']) ?>">
                                    <div class="price-form-grid">
                                        <label class="money-field"><span>R$</span><input class="control-ms" type="number" name="preco" min="0" max="9999.99" step="0.01" value="<?= number_format((float)$servico['preco'],2,'.','') ?>" required></label>
                                        <label><input class="control-ms" type="number" name="duracao" min="15" max="480" step="5" value="<?= (int)$servico['duracao'] ?>" required></label>
                                        <label class="toggle-row"><input type="checkbox" name="ativo" value="1" <?= (int)$servico['ativo'] === 1 ? 'checked' : '' ?>><span><?= (int)$servico['ativo'] === 1 ? 'Ativo' : 'Oculto' ?></span></label>
                                        <button class="btn-ms primary" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<style>
.price-form-grid{display:grid;grid-template-columns:180px 150px 120px auto;gap:10px;align-items:center}.money-field{position:relative}.money-field span{position:absolute;left:12px;top:13px;color:#8f8a84;z-index:2;font-size:.8rem}.money-field input{padding-left:38px}.toggle-row{display:flex;align-items:center;gap:8px;color:#d8d4cf;font-size:.82rem}.toggle-row input{accent-color:#a20f18}.price-form .control-ms{min-height:42px}@media(max-width:780px){.price-form-grid{grid-template-columns:1fr 1fr}.price-form-grid .btn-ms{width:100%}}
</style>
</body>
</html>

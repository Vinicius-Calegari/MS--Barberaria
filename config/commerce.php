<?php
declare(strict_types=1);

function msEscape(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function msMoney(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}
function msRedirect(string $page, string $type, string $message): never {
    header('Location: ' . $page . '?' . http_build_query([$type => $message]));
    exit;
}
function msValidatedMoney(mixed $value): ?string {
    if (!is_scalar($value)) return null;
    $v = str_replace(',', '.', trim((string)$value));
    if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $v)) return null;
    return number_format((float)$v, 2, '.', '');
}
function msShopCounts(PDO $pdo): array {
    return [
        'produtos' => (int)$pdo->query('SELECT COUNT(*) FROM ms_produtos WHERE ativo = 1')->fetchColumn(),
        'pedidos_pendentes' => (int)$pdo->query("SELECT COUNT(*) FROM ms_pedidos WHERE status='pendente'")->fetchColumn(),
        'estoque_baixo' => (int)$pdo->query('SELECT COUNT(*) FROM ms_produtos WHERE ativo=1 AND estoque<=3')->fetchColumn(),
    ];
}
function msReport(PDO $pdo, DateTimeImmutable $from, DateTimeImmutable $to): array {
    $start = $from->format('Y-m-d H:i:s');
    $end = $to->format('Y-m-d H:i:s');
    $serv = $pdo->prepare("SELECT COUNT(*) quantidade,COALESCE(SUM(preco_cobrado),0) valor FROM agendamentos WHERE comparecimento='compareceu' AND status <> 'cancelado' AND data_agendamento>=? AND data_agendamento<?");
    $serv->execute([$start,$end]);
    $servicos = $serv->fetch();
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT p.id) quantidade, COALESCE(SUM(i.subtotal),0) receita, COALESCE(SUM(i.custo_unitario*i.quantidade),0) custo FROM ms_pedidos p JOIN ms_pedido_itens i ON i.pedido_id=p.id WHERE p.status='concluido' AND p.finalizado_em>=? AND p.finalizado_em<?");
    $stmt->execute([$start,$end]);
    $vendas = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(valor),0) FROM ms_despesas WHERE data_despesa>=? AND data_despesa<?");
    $stmt->execute([$from->format('Y-m-d'),$to->format('Y-m-d')]);
    $despesas=(float)$stmt->fetchColumn();
    $receitaServicos=(float)$servicos['valor'];
    $receitaProdutos=(float)$vendas['receita'];
    $custoProdutos=(float)$vendas['custo'];
    return [
        'atendimentos'=>(int)$servicos['quantidade'],
        'vendas'=>(int)$vendas['quantidade'],
        'servicos'=>$receitaServicos,
        'produtos'=>$receitaProdutos,
        'receita'=>$receitaServicos+$receitaProdutos,
        'custo'=>$custoProdutos,
        'despesas'=>$despesas,
        'resultado'=>$receitaServicos+$receitaProdutos-$custoProdutos-$despesas,
    ];
}

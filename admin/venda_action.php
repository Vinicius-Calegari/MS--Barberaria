<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/commerce.php';
requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}requireValidCsrf();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);$acao=(string)($_POST['acao']??'');
if(!$id||!in_array($acao,['concluir','cancelar','estornar'],true))msRedirect('vendas.php','erro','Ação inválida.');
try{
 $pdo=getDBConnection();$pdo->beginTransaction();
 $st=$pdo->prepare('SELECT status FROM ms_pedidos WHERE id=? FOR UPDATE');$st->execute([$id]);$pedido=$st->fetch();
 if(!$pedido)throw new RuntimeException('Pedido não encontrado.');
 $status=(string)$pedido['status'];
 $items=$pdo->prepare('SELECT produto_id,quantidade FROM ms_pedido_itens WHERE pedido_id=? ORDER BY produto_id ASC');$items->execute([$id]);$linhas=$items->fetchAll();
 if($acao==='concluir'&&$status==='pendente'){
  $stock=$pdo->prepare('UPDATE ms_produtos SET estoque=estoque-? WHERE id=? AND estoque>=?');
  foreach($linhas as $l){$q=(int)$l['quantidade'];$stock->execute([$q,(int)$l['produto_id'],$q]);if($stock->rowCount()!==1)throw new RuntimeException('Estoque insuficiente. Ajuste a quantidade do produto antes de confirmar.');}
  $pdo->prepare("UPDATE ms_pedidos SET status='concluido',finalizado_em=NOW() WHERE id=?")->execute([$id]);$msg='Venda concluída e estoque atualizado.';
 }elseif($acao==='cancelar'&&$status==='pendente'){
  $pdo->prepare("UPDATE ms_pedidos SET status='cancelado' WHERE id=?")->execute([$id]);$msg='Pedido cancelado.';
 }elseif($acao==='estornar'&&$status==='concluido'){
  $stock=$pdo->prepare('UPDATE ms_produtos SET estoque=estoque+? WHERE id=?');
  foreach($linhas as $l)$stock->execute([(int)$l['quantidade'],(int)$l['produto_id']]);
  $pdo->prepare("UPDATE ms_pedidos SET status='cancelado',finalizado_em=NULL WHERE id=?")->execute([$id]);$msg='Venda estornada e itens devolvidos ao estoque.';
 }else throw new RuntimeException('Pedido já foi atualizado. Recarregue a página.');
 $pdo->commit();msRedirect('vendas.php','sucesso',$msg);
}catch(Throwable $e){if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();error_log('MS venda: '.$e->getMessage());$msg=str_contains($e->getMessage(),'Estoque insuficiente')?'Estoque insuficiente para finalizar a venda.':'Não foi possível atualizar o pedido.';msRedirect('vendas.php','erro',$msg);}

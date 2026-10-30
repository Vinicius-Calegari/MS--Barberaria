<?php
declare(strict_types=1);
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/database.php';
startSecureSession();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function respond(int $code,string $message,?string $url=null): never {
 http_response_code($code);echo json_encode(['ok'=>$code<400,'message'=>$message,'whatsapp'=>$url],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;
}
if($_SERVER['REQUEST_METHOD']!=='POST')respond(405,'Método inválido.');
$token=$_POST['csrf_token']??'';
if(!is_string($token)||!hash_equals($_SESSION['csrf_token']??'', $token))respond(403,'Atualize a página e tente novamente.');
if(($_SESSION['last_order_time']??0)>time()-25)respond(429,'Aguarde alguns segundos antes de criar outro pedido.');
$nome=trim((string)($_POST['nome']??''));
$telefone=preg_replace('/\D+/','',(string)($_POST['telefone']??''))??'';
$obs=mb_substr(trim((string)($_POST['observacao']??'')),0,600);
if(mb_strlen($nome)<2||mb_strlen($nome)>120||strlen($telefone)<10||strlen($telefone)>13)respond(422,'Confira seu nome e número de WhatsApp.');
$raw=json_decode((string)($_POST['itens']??'null'),true);
if(!is_array($raw)||!$raw||count($raw)>20)respond(422,'Sua sacola não contém produtos válidos.');
$itens=[];
foreach($raw as $item) {
 if(!is_array($item)||!isset($item['id'],$item['qtd']))respond(422,'Item inválido.');
$id=filter_var($item['id'],FILTER_VALIDATE_INT);$qtd=filter_var($item['qtd'],FILTER_VALIDATE_INT);
if(!$id||$id<1||!$qtd||$qtd<1||$qtd>10||isset($itens[$id]))respond(422,'Quantidade ou produto inválido.');
$itens[$id]=$qtd;
}
try {
 $pdo=getDBConnection();$pdo->beginTransaction();
 $placeholders=implode(',',array_fill(0,count($itens),'?'));
 $st=$pdo->prepare("SELECT id,nome,preco,custo,estoque FROM ms_produtos WHERE ativo=1 AND id IN ($placeholders) FOR UPDATE");$st->execute(array_keys($itens));$rows=$st->fetchAll();
 if(count($rows)!==count($itens)){$pdo->rollBack();respond(409,'Um produto não está mais disponível. Atualize a loja.');}
 $total=0.0;$linhas=[];
 foreach($rows as $row) {
  $id=(int)$row['id'];$qtd=$itens[$id];
  if((int)$row['estoque']<$qtd){$pdo->rollBack();respond(409,'Estoque insuficiente para '.$row['nome'].'.');}
  $subtotal=round((float)$row['preco']*$qtd,2);$total+=$subtotal;
  $linhas[]=[$id,(string)$row['nome'],(float)$row['preco'],(float)$row['custo'],$qtd,$subtotal];
 }
 $st=$pdo->prepare("INSERT INTO ms_pedidos(cliente_nome,telefone,observacao,total) VALUES (?,?,?,?)");
 $st->execute([$nome,$telefone,$obs,number_format($total,2,'.','')]);$order=(int)$pdo->lastInsertId();
 $st=$pdo->prepare('INSERT INTO ms_pedido_itens(pedido_id,produto_id,nome_snapshot,preco_unitario,custo_unitario,quantidade,subtotal) VALUES (?,?,?,?,?,?,?)');
 foreach($linhas as $line){$st->execute(array_merge([$order],$line));}
 $pdo->commit();$_SESSION['last_order_time']=time();
 $message="Olá, MS Barbearia! Gostaria de confirmar o pedido #$order:\n";
 foreach($linhas as $line){$message.="- {$line[4]}× {$line[1]} (R$ ".number_format($line[5],2,',','.').")\n";}
 $message.="Total: R$ ".number_format($total,2,',','.')."\nNome: $nome\nContato: $telefone";
 if($obs!=='')$message.="\nObservação: $obs";
 $message.="\n\nSei que o pedido está sujeito à confirmação de estoque e pagamento.";
 respond(200,'Pedido #'.$order.' criado! Vamos abrir o WhatsApp.','https://wa.me/5531995262066?text='.rawurlencode($message));
}catch(Throwable $e){if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();error_log('Falha em pedido: '.$e->getMessage());respond(500,'Não foi possível registrar seu pedido. Tente novamente.');}

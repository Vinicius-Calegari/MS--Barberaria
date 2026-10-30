<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/commerce.php';
requireAdmin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
requireValidCsrf();
$acao=(string)($_POST['acao']??'atualizar');
$nome=trim((string)($_POST['nome']??''));
$preco=msValidatedMoney($_POST['preco']??null);
if($nome===''||mb_strlen($nome)>50||$preco===null||(float)$preco>9999.99)msRedirect('servicos.php','erro','Nome ou preço inválido.');
try {
 $pdo=getDBConnection();
 if($acao==='criar'){
  $dur=filter_input(INPUT_POST,'duracao',FILTER_VALIDATE_INT);
  if(!$dur||$dur<15||$dur>480)msRedirect('servicos.php','erro','Duração inválida.');
  $st=$pdo->prepare('INSERT INTO servicos(nome,preco,duracao,ativo) VALUES (?,?,?,1)');
  $st->execute([$nome,$preco,$dur]);
  msRedirect('servicos.php','sucesso','Novo serviço criado e disponível no site.');
 }
 if($acao!=='atualizar')msRedirect('servicos.php','erro','Ação inválida.');
 $st=$pdo->prepare('SELECT duracao,ativo FROM servicos WHERE nome=? LIMIT 1');
 $st->execute([$nome]);$original=$st->fetch();
 if(!$original)msRedirect('servicos.php','erro','Serviço não encontrado.');
 $dur=filter_input(INPUT_POST,'duracao',FILTER_VALIDATE_INT);
 $duracao=$dur&&$dur>=15&&$dur<=480?(int)$dur:(int)$original['duracao'];
 $ativo=array_key_exists('duracao',$_POST)?(isset($_POST['ativo'])?1:0):(int)$original['ativo'];
 $st=$pdo->prepare('UPDATE servicos SET preco=?,duracao=?,ativo=? WHERE nome=?');
 $st->execute([$preco,$duracao,$ativo,$nome]);
 $destino=array_key_exists('duracao',$_POST)?'servicos.php':'index.php#servicos';
 msRedirect($destino,'sucesso','Serviço atualizado. O novo preço vale apenas para novas reservas.');
}catch(PDOException $e){
 error_log('Erro serviço MS: '.$e->getMessage());
 $msg=(int)($e->errorInfo[1]??0)===1062?'Já existe um serviço com esse nome.':'Não foi possível salvar o serviço.';
 msRedirect('servicos.php','erro',$msg);
}

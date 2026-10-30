<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/commerce.php';
requireAdmin();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}requireValidCsrf();
$mes=(string)($_POST['mes']??'');
if(!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$mes))$mes=date('Y-m');
$url='financeiro.php?mes='.rawurlencode($mes);
$acao=(string)($_POST['acao']??'');
try{
 $pdo=getDBConnection();
 if($acao==='criar'){
  $descricao=trim((string)($_POST['descricao']??''));$categoria=trim((string)($_POST['categoria']??'Operação'));
  $valor=msValidatedMoney($_POST['valor']??null);$data=trim((string)($_POST['data_despesa']??''));
  $date=DateTimeImmutable::createFromFormat('!Y-m-d',$data);
  if(mb_strlen($descricao)<2||mb_strlen($descricao)>150||mb_strlen($categoria)>70||$valor===null||(float)$valor<=0||!$date||$date->format('Y-m-d')!==$data)throw new RuntimeException('Dados inválidos.');
  $st=$pdo->prepare('INSERT INTO ms_despesas(descricao,categoria,valor,data_despesa) VALUES(?,?,?,?)');$st->execute([$descricao,$categoria,$valor,$data]);
 }elseif($acao==='excluir'){
  $id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);if(!$id)throw new RuntimeException('Registro inválido.');
  $pdo->prepare('DELETE FROM ms_despesas WHERE id=?')->execute([$id]);
 }else throw new RuntimeException('Ação inválida.');
 header('Location: '.$url.'&sucesso='.rawurlencode($acao==='criar'?'Despesa registrada.':'Despesa excluída.'));exit;
}catch(Throwable $e){error_log('MS despesa: '.$e->getMessage());header('Location: '.$url.'&erro='.rawurlencode('Não foi possível salvar a despesa.'));exit;}

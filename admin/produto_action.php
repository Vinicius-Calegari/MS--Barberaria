<?php
declare(strict_types=1);
require_once __DIR__.'/_auth.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/commerce.php';
requireAdmin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
requireValidCsrf();
$acao=(string)($_POST['acao']??'');
if(!in_array($acao,['criar','editar'],true))msRedirect('produtos.php','erro','Ação inválida.');
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if($acao==='editar'&&(!$id||$id<1))msRedirect('produtos.php','erro','Produto inválido.');
$nome=trim((string)($_POST['nome']??''));
$desc=trim((string)($_POST['descricao']??''));
$categoria=trim((string)($_POST['categoria']??''));$tamanho=trim((string)($_POST['tamanho']??''));
$preco=msValidatedMoney($_POST['preco']??null);$custo=msValidatedMoney($_POST['custo']??'0');
$estoque=filter_var($_POST['estoque']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>100000]]);
if(mb_strlen($nome)<2||mb_strlen($nome)>130||mb_strlen($categoria)<2||mb_strlen($categoria)>70||mb_strlen($tamanho)>80||mb_strlen($desc)>2000||$preco===null||(float)$preco<=0||$custo===null||$estoque===false)msRedirect('produtos.php','erro','Confira os dados do produto.');
$ativo=isset($_POST['ativo'])?1:0;$destaque=isset($_POST['destaque'])?1:0;
$blob=null;$mime=null;
$file=$_FILES['foto']??null;
if(is_array($file)&&($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){
 if($file['error']!==UPLOAD_ERR_OK||$file['size']>1048576||$file['size']<30||!is_uploaded_file($file['tmp_name']))msRedirect('produtos.php','erro','Foto inválida. Máximo de 1 MB.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
 if(!in_array($mime,['image/jpeg','image/png','image/webp'],true))msRedirect('produtos.php','erro','Use imagem JPEG, PNG ou WebP.');
 $dims=@getimagesize($file['tmp_name']);
 if(!$dims||$dims[0]<100||$dims[1]<100||$dims[0]>6000||$dims[1]>6000)msRedirect('produtos.php','erro','Imagem com tamanho ou dimensões inválidas.');
 $blob=file_get_contents($file['tmp_name']);
 if($blob===false)msRedirect('produtos.php','erro','Não foi possível ler a imagem.');
}
try{
 $pdo=getDBConnection();
 if($acao==='criar'){
  $st=$pdo->prepare('INSERT INTO ms_produtos(nome,descricao,categoria,tamanho,preco,custo,estoque,imagem,imagem_tipo,destaque,ativo) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
  $st->execute([$nome,$desc,$categoria,$tamanho?:'Único',$preco,$custo,$estoque,$blob,$mime,$destaque,$ativo]);
 }else{
  $st=$pdo->prepare('UPDATE ms_produtos SET nome=?,descricao=?,categoria=?,tamanho=?,preco=?,custo=?,estoque=?,destaque=?,ativo=? WHERE id=?');
  $st->execute([$nome,$desc,$categoria,$tamanho?:'Único',$preco,$custo,$estoque,$destaque,$ativo,$id]);
  if($blob!==null){$st=$pdo->prepare('UPDATE ms_produtos SET imagem=?,imagem_tipo=? WHERE id=?');$st->execute([$blob,$mime,$id]);}
 }
 msRedirect('produtos.php','sucesso',$acao==='criar'?'Produto cadastrado na MS Store.':'Produto atualizado com sucesso.');
}catch(Throwable $e){error_log('Erro ao salvar produto: '.$e->getMessage());msRedirect('produtos.php','erro','Não foi possível salvar. Confira os dados e tente novamente.');}

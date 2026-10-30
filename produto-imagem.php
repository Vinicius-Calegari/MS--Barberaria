<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(404);exit;}
$pdo=getDBConnection();
$q=$pdo->prepare('SELECT imagem,imagem_tipo,atualizado_em FROM ms_produtos WHERE id=? LIMIT 1');$q->execute([$id]);$p=$q->fetch();
if(!$p||$p['imagem']===null){http_response_code(404);exit;}
$allowed=['image/jpeg','image/png','image/webp'];
if(!in_array($p['imagem_tipo'],$allowed,true)){http_response_code(415);exit;}
header('Content-Type: '.$p['imagem_tipo']);header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');header('Content-Disposition: inline; filename="produto-'.$id.'"');
echo $p['imagem'];

<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
require __DIR__.'/../private/lib.php';
$dir=__DIR__.'/../private/storage';$target=$dir.'/backup-'.gmdate('Ymd-His').'.sql';
if(db()->getAttribute(PDO::ATTR_DRIVER_NAME)!=='mysql'){fwrite(STDERR,"Usa esta herramienta con MySQL en el servidor.\n");exit(1);}
$f=fopen($target,'x');if(!$f)throw new RuntimeException('No se pudo crear respaldo.');
fwrite($f,"-- Respaldo NexTech ".now()."\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n");
$tables=['users','categories','products','cart','favorites','addresses','orders','order_items','order_events','settings','reset_tokens','rate_limits','audit','payment_events'];
db()->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');db()->beginTransaction();
try{foreach($tables as $table){$schema=query('SHOW CREATE TABLE `'.$table.'`')->fetch(PDO::FETCH_NUM);fwrite($f,"\nDROP TABLE IF EXISTS `$table`;\n".$schema[1].";\n");foreach(query('SELECT * FROM `'.$table.'`') as $r){$values=array_map(fn($v)=>$v===null?'NULL':db()->quote((string)$v),array_values($r));fwrite($f,'INSERT INTO `'.$table.'` VALUES('.implode(',',$values).");\n");}}db()->commit();fwrite($f,"SET FOREIGN_KEY_CHECKS=1;\n");fclose($f);chmod($target,0600);echo 'Respaldo creado: '.$target.PHP_EOL;}catch(Throwable $e){if(db()->inTransaction())db()->rollBack();fclose($f);unlink($target);throw $e;}

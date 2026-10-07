<?php
// Local development migration only. Creates a backup and refuses overwriting one.
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit('Solo CLI');
require __DIR__.'/../private/lib.php';require __DIR__.'/../private/schema.php';
$path=config()['db']['path']??'';
if((config()['db']['driver']??'')!=='sqlite'||!is_file($path))exit("Selecciona una base SQLite existente mediante NEXTECH_CONFIG.\n");
$old=new PDO('sqlite:'.$path);$old->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
$cols=array_column($old->query('PRAGMA table_info(users)')->fetchAll(),'name');
if(in_array('active',$cols,true)){
    if(in_array('specs',array_column($old->query('PRAGMA table_info(products)')->fetchAll(),'name'),true)){
        $old->exec('PRAGMA wal_checkpoint(TRUNCATE)');if(!is_file($path.'.before-specs'))copy($path,$path.'.before-specs');
        $old->exec('ALTER TABLE products DROP COLUMN specs');
    }
    exit("La base ya tiene el esquema v2 normalizado.\n");
}
$backup=$path.'.before-v2';if(file_exists($backup))throw new RuntimeException('Ya existe un respaldo; revísalo antes de continuar.');
$old->exec('PRAGMA wal_checkpoint(TRUNCATE)');copy($path,$backup);
[$tables,$suffix]=schema_definitions($old);
$legacySpecs=$old->query('SELECT id,specs FROM products')->fetchAll();
$old->exec('PRAGMA foreign_keys=OFF');$old->beginTransaction();
try{
 foreach($tables as $definition){$name=strstr($definition,' ',true);$exists=$old->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$name'")->fetchColumn();
  if(!$exists){$old->exec('CREATE TABLE '.$definition);continue;}
  $new='v2_'.$name;$old->exec('CREATE TABLE '.$new.substr($definition,strlen($name)));
  $before=array_column($old->query("PRAGMA table_info($name)")->fetchAll(),'name');$after=array_column($old->query("PRAGMA table_info($new)")->fetchAll(),'name');$common=implode(',',array_intersect($before,$after));
  $old->exec("INSERT INTO $new($common) SELECT $common FROM $name");$old->exec("DROP TABLE $name");$old->exec("ALTER TABLE $new RENAME TO $name");
 }
 $old->exec("INSERT INTO roles(code,name) VALUES('admin','Administrador'),('customer','Cliente')");
 foreach($legacySpecs as $p){$stmt=$old->prepare('INSERT INTO product_specs(product_id,spec_key,spec_value) VALUES(?,?,?)');foreach(json_decode($p['specs'],true)?:[] as $k=>$v)$stmt->execute([$p['id'],$k,$v]);}
 $old->exec('UPDATE orders SET net_total=ROUND(total*10000.0/11600),tax=total-ROUND(total*10000.0/11600)');
 if($old->query('PRAGMA foreign_key_check')->fetchAll())throw new RuntimeException('Hay referencias huérfanas; se conservará la versión original.');
 $old->commit();$old->exec('PRAGMA foreign_keys=ON');migrate($old);echo "Migración v2 terminada; respaldo conservado.\n";
}catch(Throwable $e){if($old->inTransaction())$old->rollBack();throw $e;}

<?php
declare(strict_types=1);
function seed_catalog(PDO $db): void {
    if((int)$db->query('SELECT COUNT(*) FROM categories')->fetchColumn()>0)return;
    $categories=[['accesorios','Accesorios','mouse'],['audio','Audio','headphones'],['computadoras','Computadoras','laptop'],['componentes','Componentes','cpu'],['consumibles','Consumibles','droplet'],['almacenamiento','Almacenamiento','drive'],['electronica','Electrónica de consumo','camera'],['impresion','Impresión','printer'],['software','Software','code'],['monitores','Monitores','monitor'],['celulares','Celulares','phone'],['redes','Redes','wifi'],['apple','Apple','laptop'],['energia','Energía','zap'],['merchandise','Merchandise','bag']];
    $db->beginTransaction();$stmt=$db->prepare('INSERT INTO categories(slug,name,icon,position) VALUES(?,?,?,?)');foreach($categories as $i=>$cat)$stmt->execute([...$cat,$i]);
    $data=json_decode(file_get_contents(__DIR__.'/catalog.json'),true,512,JSON_THROW_ON_ERROR);
    $s=$db->prepare('INSERT INTO products(sku,category,name,brand,description,specs,image,source_url,source_checked,price,compare_price,stock,featured,verified,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach($data as $i=>$p){$price=(int)round(($p['suggested_price_mxn']??999)*100);$s->execute([$p['sku']??('NX-'.str_pad((string)($i+1),4,'0',STR_PAD_LEFT)),$p['category'],$p['name'],$p['brand'],$p['description'],json_encode($p['specs'],JSON_UNESCAPED_UNICODE),$p['image']??$p['image_url']??'',$p['source_url'],$p['source_checked']??'', $price,0,0,$p['featured']??0,0,gmdate('Y-m-d\TH:i:s\Z')]);$pid=(int)$db->lastInsertId();$sp=$db->prepare('INSERT INTO product_specs(product_id,spec_key,spec_value) VALUES(?,?,?)');foreach($p['specs'] as $k=>$v)$sp->execute([$pid,$k,$v]);}
    $db->commit();
}

<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit('Solo CLI');
require __DIR__.'/../private/lib.php';require __DIR__.'/../private/schema.php';require __DIR__.'/../private/seed.php';
if(config()['environment']!=='development')exit("Solo se permiten datos de prueba con APP_ENV=development.\n");
migrate(db());if((int)row('SELECT COUNT(*) n FROM users')['n']>0)exit("Se requiere una base sin usuarios. No se modificó ningún dato existente.\n");
seed_catalog(db());db()->beginTransaction();$accounts=[];
foreach(['admin','customer'] as $role){$pass=bin2hex(random_bytes(12));$email=$role.'@nextech.test';query('INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,?,?)',[$role==='admin'?'Administrador de prueba':'Cliente de prueba',$email,password_hash($pass,PASSWORD_DEFAULT),$role,now()]);$accounts[$role]=['id'=>(int)db()->lastInsertId(),'email'=>$email,'password'=>$pass];}
query('UPDATE products SET stock=12');
query('INSERT INTO suppliers(name,email,phone,created_at) VALUES(?,?,?,?)',['Proveedor de demostración','proveedor@example.test','5500000000',now()]);
query('UPDATE products SET supplier_id=? WHERE id<=20',[(int)db()->lastInsertId()]);
foreach([1,2,3] as $i){$p=row('SELECT * FROM products WHERE id=?',[$i]);$id='DEMO-'.gmdate('ymd').'-'.$i;$gross=(int)$p['price'];$net=(int)round($gross/1.16);$time=gmdate('Y-m-d\TH:i:s\Z',time()-($i-1)*86400);
 query('INSERT INTO orders(id,user_id,status,payment_status,provider,subtotal,shipping,total,tax_rate_bp,tax,net_total,address,idempotency_key,notes,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$id,$accounts['customer']['id'],'paid','paid','demo',$gross,0,$gross,1600,$gross-$net,$net,json_encode(['recipient'=>'Cliente de prueba','phone'=>'5500000000','street'=>'Domicilio ficticio 123','colony'=>'Centro','city'=>'Ciudad de México','state'=>'Ciudad de México','postal_code'=>'06000','notes'=>'Entrega simulada']),$id,'Datos sintéticos de demostración. No corresponde a una venta real.',$time,$time]);
 query('INSERT INTO order_items(order_id,product_id,sku,name,price,qty,image) VALUES(?,?,?,?,?,1,?)',[$id,$i,$p['sku'],$p['name'],$p['price'],$p['image']]);query('UPDATE products SET stock=stock-1 WHERE id=?',[$i]);inventory_movement($i,$accounts['admin']['id'],$id,-1,'Venta de demostración');order_event($id,'Registro sintético para demostrar reportes. No hubo cobro.');
}
audit($accounts['admin']['id'],'demo-fixture',['orders'=>3,'note'=>'Datos sintéticos, no historial real']);db()->commit();
file_put_contents(__DIR__.'/../private/storage/demo-accounts.json',json_encode($accounts,JSON_PRETTY_PRINT));
echo "Datos de demostración creados. Credenciales aleatorias en private/storage/demo-accounts.json; no publicar ni versionar ese archivo.\n";

<?php
declare(strict_types=1);
require __DIR__.'/../private/lib.php';
require __DIR__.'/../private/rest.php';
require __DIR__.'/../private/admin.php';
header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');
try {
    if(!is_file(__DIR__.'/../private/installed.lock') && (config()['environment']??'production')!=='development')fail('Completa la instalación de NexTech en /install.php.',503);
    $action=$_GET['action']??'bootstrap';$method=$_SERVER['REQUEST_METHOD'];$httpMethod=$method;$routeId=null;
    $rest=!empty($_SERVER['PATH_INFO']);
    if($rest){[$action,$routeId]=resolve_rest($_SERVER['PATH_INFO'],$method);if($routeId!==null)$_GET['id']=$routeId;if(in_array($method,['PUT','PATCH','DELETE'],true))$method='POST';}
    if($method==='POST' && str_starts_with($action,'webhook-')) {require __DIR__.'/../private/payments.php';handle_webhook(substr($action,8));}
    start_session();
    $d=[];
    if($method==='POST') {
        csrf();
        if($action!=='upload') {
            if((int)($_SERVER['CONTENT_LENGTH']??0)>100000)fail('La solicitud es demasiado grande.',413);
            $raw=file_get_contents('php://input',false,null,0,100001);if(strlen($raw)>100000)fail('La solicitud es demasiado grande.',413);
            $d=$raw===''&&$httpMethod==='DELETE'?[]:json_decode($raw,true);if(!is_array($d))fail('Solicitud inválida.');
        }
    } elseif($method!=='GET')fail('Método no permitido.',405);
    if($routeId!==null)$d['id']=$routeId;
    if($rest&&$action==='cart')$d['product_id']=$routeId;
    if($action==='cart-remove'){$action='cart';$d['product_id']=$routeId;$d['qty']=0;}
    admin_extension($action,$method,$d);
    if($method==='GET') switch($action) {
        case 'bootstrap': json_out(['user'=>current_user(),'csrf'=>$_SESSION['csrf'],'settings'=>public_settings(),'categories'=>query('SELECT * FROM categories WHERE active=1 ORDER BY position')->fetchAll()]);
        case 'products':
            $where=['active=1','category IN (SELECT slug FROM categories WHERE active=1)'];$args=[];
            if(!empty($_GET['category'])){$where[]='category=?';$args[]=text_field($_GET['category'],1,40);}
            if(!empty($_GET['q'])){$where[]='(name LIKE ? OR brand LIKE ? OR sku LIKE ? OR description LIKE ?)';$term='%'.text_field($_GET['q'],1,100).'%';array_push($args,$term,$term,$term,$term);}
            if(!empty($_GET['brand'])){$where[]='brand=?';$args[]=text_field($_GET['brand'],1,90);}
            if(!empty($_GET['stock']))$where[]='stock>0';
            if(!empty($_GET['featured']))$where[]='featured=1';
            if(!empty($_GET['offers']))$where[]='compare_price>price';
            $clause=implode(' AND ',$where);$count=(int)row("SELECT COUNT(*) n FROM products WHERE $clause",$args)['n'];
            $page=integer($_GET['page']??1,1,10000);$limit=integer($_GET['limit']??24,1,100);$offset=($page-1)*$limit;
            $sort=['price-asc'=>'price ASC,id ASC','price-desc'=>'price DESC,id ASC','name'=>'name ASC,id ASC','newest'=>'id DESC'][$_GET['sort']??'']??'featured DESC,id ASC';
            $items=query("SELECT * FROM products WHERE $clause ORDER BY $sort LIMIT $limit OFFSET $offset",$args)->fetchAll();
            json_out(['items'=>array_map('product',$items),'total'=>$count,'page'=>$page,'limit'=>$limit,'pages'=>(int)ceil($count/$limit),'brands'=>array_column(query('SELECT DISTINCT brand FROM products WHERE active=1 ORDER BY brand')->fetchAll(),'brand')]);
        case 'product':
            $p=row('SELECT * FROM products WHERE id=? AND active=1',[integer($_GET['id']??0,1,PHP_INT_MAX)]);if(!$p)fail('Producto no encontrado.',404);json_out(product($p));
        case 'cart':
            $u=require_user();$items=query('SELECT p.*,c.qty FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id=?',[$u['id']])->fetchAll();json_out(array_map('product',$items));
        case 'favorites':
            $u=require_user();json_out(array_map('product',query('SELECT p.* FROM favorites f JOIN products p ON p.id=f.product_id WHERE f.user_id=? AND p.active=1',[$u['id']])->fetchAll()));
        case 'addresses':$u=require_user();json_out(query('SELECT * FROM addresses WHERE user_id=? ORDER BY id DESC',[$u['id']])->fetchAll());
        case 'orders':$u=require_user();json_out(array_map('order_detail',query('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 100',[$u['id']])->fetchAll()));
        case 'order':$u=require_user();$o=row('SELECT * FROM orders WHERE id=? AND user_id=?',[$_GET['id']??'',$u['id']]);if(!$o)fail('Pedido no encontrado.',404);json_out(order_detail($o));
        case 'admin-dashboard':
            require_user(true);json_out(['products'=>(int)row('SELECT COUNT(*) n FROM products')['n'],'customers'=>(int)row("SELECT COUNT(*) n FROM users WHERE role='customer'")['n'],'orders'=>(int)row('SELECT COUNT(*) n FROM orders')['n'],'revenue'=>(int)row("SELECT COALESCE(SUM(total),0) n FROM orders WHERE payment_status='paid' AND provider!='demo'")['n'],'low_stock'=>array_map('product',query('SELECT * FROM products WHERE active=1 AND stock<=5 ORDER BY stock LIMIT 20')->fetchAll()),'settings'=>settings(),'integrations'=>public_settings()['payments'],'email_enabled'=>public_settings()['email_enabled']]);
        case 'admin-products':require_user(true);if($rest){[$w,$a]=list_filter(['name','sku','brand','description']);if(!empty($_GET['category'])){$w.=' AND category=?';$a[]=$_GET['category'];}$r=paginated('products','*',$w,$a,['name'=>'name ASC','name-desc'=>'name DESC','price-asc'=>'price ASC','price-desc'=>'price DESC','stock'=>'stock ASC','newest'=>'id DESC'],'id DESC');$r['items']=array_map('product',$r['items']);json_out($r);}json_out(array_map('product',query('SELECT * FROM products ORDER BY id DESC')->fetchAll()));
        case 'admin-orders':require_user(true);if($rest){$w='1=1';$a=[];if(!empty($_GET['q'])){$w.=' AND (o.id LIKE ? OR u.name LIKE ?)';$a=['%'.text_field($_GET['q'],1,100).'%','%'.$_GET['q'].'%'];}if(!empty($_GET['status'])){$w.=' AND o.status=?';$a[]=$_GET['status'];}$r=paginated('orders o JOIN users u ON u.id=o.user_id','o.*,u.name customer,u.email',$w,$a,['newest'=>'o.created_at DESC','oldest'=>'o.created_at ASC','total'=>'o.total DESC'],'o.created_at DESC');$r['items']=array_map('order_detail',$r['items']);json_out($r);}json_out(array_map('order_detail',query('SELECT o.*,u.name customer,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY created_at DESC LIMIT 250')->fetchAll()));
        case 'admin-customers':require_user(true);json_out(query("SELECT u.id,u.name,u.email,u.phone,u.created_at,COUNT(o.id) order_count FROM users u LEFT JOIN orders o ON o.user_id=u.id WHERE u.role='customer' GROUP BY u.id,u.name,u.email,u.phone,u.created_at ORDER BY u.id DESC LIMIT 500")->fetchAll());
        case 'admin-audit':require_user(true);if($rest)json_out(paginated('audit a JOIN users u ON u.id=a.user_id','a.*,u.name','1=1',[],['oldest'=>'a.id ASC','newest'=>'a.id DESC'],'a.id DESC'));json_out(query('SELECT a.*,u.name FROM audit a JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 100')->fetchAll());
        case 'admin-export':
            require_user(true);header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="pedidos-nextech.csv"');$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,['Pedido','Fecha','Estado','Pago','Método','Subtotal MXN','Envío MXN','Total MXN'],',','"','');foreach(query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll() as $o)fputcsv($out,[$o['id'],$o['created_at'],$o['status'],$o['payment_status'],$o['provider'],$o['subtotal']/100,$o['shipping']/100,$o['total']/100],',','"','');exit;
        default: fail('Ruta no encontrada.',404);
    }
    switch($action) {
        case 'register':
            rate_limit('register',5,3600);$name=text_field($d['name']??'',2,120);$email=email_field($d['email']??'');$pass=password_field($d['password']??'');
            if(empty($d['accept']))fail('Acepta el aviso de privacidad y los términos para crear tu cuenta.');
            if(row('SELECT id FROM users WHERE email=?',[$email]))fail('No se pudo crear la cuenta. Prueba iniciar sesión o recuperar tu contraseña.',409);
            query('INSERT INTO users(name,email,password,created_at) VALUES(?,?,?,?)',[$name,$email,password_hash($pass,PASSWORD_DEFAULT),now()]);$u=row('SELECT * FROM users WHERE email=?',[$email]);login_user($u);audit($u['id'],'registration',['terms_accepted_at'=>now()]);json_out(['user'=>current_user(),'csrf'=>$_SESSION['csrf']],201);
        case 'login':
            rate_limit('login',15,900);$email=email_field($d['email']??'');$pass=text_field($d['password']??'',1,128);$u=row('SELECT * FROM users WHERE email=?',[$email]);
            $dummy='$2y$10$kEWNNfxAVVXYeAgMsjgMue.eFkGeDvOuULrgokRbzJeKL.PvyNmTe';
            if(!password_verify($pass,$u['password']??$dummy)||!$u||!$u['active'])fail('Correo o contraseña incorrectos.',401);
            if(password_needs_rehash($u['password'],PASSWORD_DEFAULT))query('UPDATE users SET password=? WHERE id=?',[password_hash($pass,PASSWORD_DEFAULT),$u['id']]);login_user($u);json_out(['user'=>current_user(),'csrf'=>$_SESSION['csrf']]);
        case 'logout':$_SESSION=[];session_regenerate_id(true);$_SESSION['csrf']=bin2hex(random_bytes(32));json_out(['ok'=>true,'csrf'=>$_SESSION['csrf']]);
        case 'forgot':
            rate_limit('forgot',4,900);if(!public_settings()['email_enabled'])fail('La recuperación por correo aún no está habilitada. Contacta a la tienda.',503);
            $email=email_field($d['email']??'');$u=row('SELECT id FROM users WHERE email=?',[$email]);
            if($u){$token=bin2hex(random_bytes(32));query('DELETE FROM reset_tokens WHERE user_id=?',[$u['id']]);query('INSERT INTO reset_tokens(token_hash,user_id,expires_at) VALUES(?,?,?)',[hash('sha256',$token),$u['id'],time()+1800]);send_email($email,'Restablecer tu contraseña de NexTech','Abre este enlace durante los próximos 30 minutos: '.config()['base_url'].'/#restablecer?token='.$token."\nSi no solicitaste el cambio, ignora este mensaje.");}json_out(['message'=>'Si existe una cuenta con ese correo, recibirás un enlace de recuperación.']);
        case 'reset':
            rate_limit('reset',10,900);$token=text_field($d['token']??'',64,64);$pass=password_field($d['password']??'');db()->beginTransaction();$t=row('SELECT * FROM reset_tokens WHERE token_hash=? AND expires_at>?',[hash('sha256',$token),time()]);if(!$t)fail('El enlace venció o ya fue utilizado.');
            $deleted=query('DELETE FROM reset_tokens WHERE token_hash=?',[hash('sha256',$token)]);if(!$deleted->rowCount())fail('El enlace ya fue utilizado.');query('UPDATE users SET password=?,auth_version=auth_version+1 WHERE id=?',[password_hash($pass,PASSWORD_DEFAULT),$t['user_id']]);query('DELETE FROM reset_tokens WHERE user_id=?',[$t['user_id']]);db()->commit();json_out(['ok'=>true]);
        case 'profile':$u=require_user();query('UPDATE users SET name=?,phone=? WHERE id=?',[text_field($d['name']??'',2,120),text_field($d['phone']??'',0,30),$u['id']]);json_out(['user'=>current_user()]);
        case 'password':
            $u=require_user();rate_limit('password',5,900);$old=row('SELECT password FROM users WHERE id=?',[$u['id']]);if(!password_verify(text_field($d['current_password']??'',1,128),$old['password']))fail('La contraseña actual no coincide.');query('UPDATE users SET password=?,auth_version=auth_version+1 WHERE id=?',[password_hash(password_field($d['password']??''),PASSWORD_DEFAULT),$u['id']]);$u=row('SELECT * FROM users WHERE id=?',[$u['id']]);login_user($u);json_out(['csrf'=>$_SESSION['csrf']]);
        case 'address':
            $u=require_user();$a=address_fields($d);$label=text_field($d['label']??'Casa',1,50);$id=integer($d['id']??0,0,PHP_INT_MAX);
            if($id){if(!row('SELECT id FROM addresses WHERE id=? AND user_id=?',[$id,$u['id']]))fail('Dirección no encontrada.',404);query('UPDATE addresses SET label=?,recipient=?,phone=?,street=?,colony=?,city=?,state=?,postal_code=?,notes=? WHERE id=? AND user_id=?',[$label,...array_values($a),$id,$u['id']]);}
            else {if((int)row('SELECT COUNT(*) n FROM addresses WHERE user_id=?',[$u['id']])['n']>=10)fail('Puedes guardar hasta 10 direcciones.');query('INSERT INTO addresses(user_id,label,recipient,phone,street,colony,city,state,postal_code,notes) VALUES(?,?,?,?,?,?,?,?,?,?)',[$u['id'],$label,...array_values($a)]);}json_out(['ok'=>true]);
        case 'address-delete':$u=require_user();query('DELETE FROM addresses WHERE id=? AND user_id=?',[integer($d['id']??0,1,PHP_INT_MAX),$u['id']]);json_out(['ok'=>true]);
        case 'cart':
            $u=require_user();$id=integer($d['product_id']??0,1,PHP_INT_MAX);$qty=integer($d['qty']??0,0,99);$p=row('SELECT * FROM products WHERE id=? AND active=1',[$id]);
            if($qty&&(!$p||$p['stock']<$qty))fail('No hay suficientes unidades disponibles.',409);
            if(!$qty)query('DELETE FROM cart WHERE user_id=? AND product_id=?',[$u['id'],$id]);
            elseif(row('SELECT qty FROM cart WHERE user_id=? AND product_id=?',[$u['id'],$id]))query('UPDATE cart SET qty=? WHERE user_id=? AND product_id=?',[$qty,$u['id'],$id]);
            else query('INSERT INTO cart(user_id,product_id,qty) VALUES(?,?,?)',[$u['id'],$id,$qty]);json_out(['ok'=>true]);
        case 'favorite':$u=require_user();$id=integer($d['id']??0,1,PHP_INT_MAX);if(!row('SELECT id FROM products WHERE id=? AND active=1',[$id]))fail('Producto no encontrado.',404);if(row('SELECT product_id FROM favorites WHERE user_id=? AND product_id=?',[$u['id'],$id])){query('DELETE FROM favorites WHERE user_id=? AND product_id=?',[$u['id'],$id]);$saved=false;}else{query('INSERT INTO favorites(user_id,product_id) VALUES(?,?)',[$u['id'],$id]);$saved=true;}json_out(['saved'=>$saved]);
        case 'checkout':
            $u=require_user();rate_limit('checkout-'.$u['id'],10,600);$s=settings();$provider=text_field($d['provider']??'',1,30);$key=hash('sha256',$u['id'].':'.text_field($d['key']??'',16,80));
            if($existing=row('SELECT * FROM orders WHERE idempotency_key=?',[$key]))json_out(order_detail($existing));
            if(empty(public_settings()['payments'][$provider]))fail('Este método de pago no está disponible.');
            if($s['demo_mode']&&$provider!=='demo')fail('La tienda está en demostración. No se permiten cobros reales.');
            if(!$s['demo_mode']&&$provider==='demo')fail('El pago de demostración está deshabilitado.');
            if(empty($d['accept']))fail('Acepta los términos y la política de privacidad para continuar.');
            $a=address_fields($d['address']??[]);$id='NX-'.gmdate('ymd').'-'.strtoupper(bin2hex(random_bytes(4)));
            db()->beginTransaction();
            $items=query('SELECT p.*,c.qty FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id=? ORDER BY p.id',[$u['id']])->fetchAll();if(!$items)fail('Tu carrito está vacío.');if(count($items)>50)fail('Máximo 50 productos distintos por pedido.');
            $subtotal=0;
            foreach($items as $p){if(!$p['active']||$p['price']<=0||(!$s['demo_mode']&&!$p['verified']))fail('Un producto aún no está habilitado para venta.',409);$qty=integer($p['qty'],1,99);$r=query('UPDATE products SET stock=stock-? WHERE id=? AND stock>=? AND active=1',[$qty,$p['id'],$qty]);if(!$r->rowCount())fail('Se agotó '.$p['name'].'. Revisa tu carrito.',409);$subtotal+=(int)$p['price']*$qty;}
            if($subtotal>100000000)fail('El total supera el límite de compra.');$shipping=$subtotal>=(int)$s['free_shipping']?0:(int)$s['shipping_price'];
            query('INSERT INTO orders(id,user_id,status,payment_status,provider,subtotal,shipping,total,address,idempotency_key,notes,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)',[$id,$u['id'],'pending','pending',$provider,$subtotal,$shipping,$subtotal+$shipping,json_encode($a,JSON_UNESCAPED_UNICODE),$key,'Términos aceptados: '.now(),now(),now()]);
            $taxRate=(int)$s['tax_rate_bp'];$gross=$subtotal+$shipping;$net=(int)round($gross*10000/(10000+$taxRate));
            query('UPDATE orders SET tax_rate_bp=?,tax=?,net_total=? WHERE id=?',[$taxRate,$gross-$net,$net,$id]);
            foreach($items as $p)inventory_movement((int)$p['id'],(int)$u['id'],$id,-(int)$p['qty'],'Reserva de pedido');
            audit($u['id'],'create-order',['id'=>$id,'total'=>$gross,'tax'=>$gross-$net,'status'=>'pending']);
            foreach($items as $p)query('INSERT INTO order_items(order_id,product_id,sku,name,price,qty,image) VALUES(?,?,?,?,?,?,?)',[$id,$p['id'],$p['sku'],$p['name'],$p['price'],$p['qty'],$p['image']]);
            query('DELETE FROM cart WHERE user_id=?',[$u['id']]);order_event($id,$provider==='demo'?'Pedido de demostración creado. No se realizó ningún cobro.':'Pedido creado. Pendiente de confirmación del pago.');db()->commit();
            $o=row('SELECT * FROM orders WHERE id=?',[$id]);
            if(in_array($provider,['stripe','mercadopago'],true)){require_once __DIR__.'/../private/payments.php';try{create_payment($o,$u);}catch(Throwable $e){error_log('NexTech: no se pudo crear checkout para '.$id);order_event($id,'No se pudo abrir la pasarela. Puedes reintentar desde tu pedido.');}}
            send_email($u['email'],'Recibimos tu pedido '.$id,'Consulta el estado de tu pedido en '.config()['base_url'].'/#pedido?id='.$id.'. El pago está pendiente de confirmación.');json_out(order_detail(row('SELECT * FROM orders WHERE id=?',[$id])),201);
        case 'retry-payment':
            $u=require_user();rate_limit('retry-'.$u['id'],5,600);$o=row('SELECT * FROM orders WHERE id=? AND user_id=?',[$d['id']??'',$u['id']]);if(!$o||$o['status']!=='pending'||$o['payment_status']!=='pending'||!in_array($o['provider'],['stripe','mercadopago'],true))fail('No se puede reintentar este pago.');if($o['checkout_url'])json_out(['url'=>$o['checkout_url']]);require_once __DIR__.'/../private/payments.php';create_payment($o,$u);json_out(['url'=>row('SELECT checkout_url FROM orders WHERE id=?',[$o['id']])['checkout_url']]);
        case 'cancel-order':
            $u=require_user();db()->beginTransaction();$o=row('SELECT * FROM orders WHERE id=? AND user_id=?',[$d['id']??'',$u['id']]);if(!$o||!in_array($o['provider'],['demo','transfer'],true))fail('Contacta a soporte para cancelar este pedido.');$r=query("UPDATE orders SET status='cancelled',payment_status='cancelled',updated_at=? WHERE id=? AND status='pending' AND payment_status='pending'",[now(),$o['id']]);if(!$r->rowCount())fail('Este pedido ya no puede cancelarse.',409);return_inventory($o,(int)$u['id']);audit($u['id'],'cancel-order',['id'=>$o['id'],'before'=>$o['status'],'after'=>'cancelled']);order_event($o['id'],'Pedido cancelado por el cliente.');db()->commit();json_out(['ok'=>true]);
        case 'admin-product':
            $u=require_user(true);$id=integer($d['id']??0,0,PHP_INT_MAX);$category=text_field($d['category']??'',1,40);if(!row('SELECT slug FROM categories WHERE slug=? AND active=1',[$category]))fail('Categoría inválida.');
            $image=text_field($d['image']??'',0,1500);if($image&&!preg_match('~^(?:/uploads/[a-f0-9]+\.(?:png|jpg|webp)|/assets/[a-zA-Z0-9_./-]+|https://[^\s]+)$~',$image))fail('Usa una imagen subida o una URL HTTPS.');
            $source=text_field($d['source_url']??'',0,1500);if($source&&!filter_var($source,FILTER_VALIDATE_URL))fail('URL de fuente inválida.');if($source&&!str_starts_with($source,'https://'))fail('La fuente debe usar HTTPS.');
            $specs=$d['specs']??[];if(!is_array($specs)||count($specs)>30)fail('Especificaciones inválidas.');foreach($specs as $k=>$v){text_field((string)$k,1,80);text_field($v,0,500);}
            $values=[text_field($d['sku']??'',2,80),$category,text_field($d['name']??'',3,220),text_field($d['brand']??'',1,90),text_field($d['description']??'',5,10000),$image,$source,integer($d['price']??0,0,100000000),integer($d['compare_price']??0,0,100000000),integer($d['stock']??0,0,1000000),!empty($d['active'])?1:0,!empty($d['featured'])?1:0,!empty($d['verified'])?1:0,now()];
            db()->beginTransaction();if($id){$before=row('SELECT * FROM products WHERE id=?',[$id]);if(!$before)fail('Producto no encontrado.',404);query('UPDATE products SET sku=?,category=?,name=?,brand=?,description=?,image=?,source_url=?,price=?,compare_price=?,stock=?,active=?,featured=?,verified=?,updated_at=? WHERE id=?',[...$values,$id]);audit($u['id'],'update-product',['id'=>$id,'before_stock'=>$before['stock'],'after_stock'=>$values[9]]);}else{query('INSERT INTO products(sku,category,name,brand,description,image,source_url,price,compare_price,stock,active,featured,verified,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',$values);$id=(int)db()->lastInsertId();audit($u['id'],'create-product',['id'=>$id]);}
            $supplier=empty($d['supplier_id'])?null:integer($d['supplier_id'],1,PHP_INT_MAX);if($supplier&&!row('SELECT id FROM suppliers WHERE id=? AND active=1',[$supplier]))fail('Proveedor no disponible.',422);
            query('UPDATE products SET supplier_id=? WHERE id=?',[$supplier,$id]);save_specs($id,$specs);
            $change=$values[9]-(int)($before['stock']??0);if($change)inventory_movement($id,(int)$u['id'],null,$change,'Ajuste administrativo');
            db()->commit();json_out(['id'=>$id],$rest&&!isset($before)?201:200);
        case 'upload':
            require_user(true);$f=$_FILES['image']??null;if(!$f||$f['error']!==UPLOAD_ERR_OK||$f['size']>4*1024*1024)fail('Sube una imagen JPG, PNG o WebP de hasta 4 MB.');$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;$size=getimagesize($f['tmp_name']);if(!$ext||!$size||$size[0]>8000||$size[1]>8000)fail('Formato de imagen no válido.');$name=bin2hex(random_bytes(16)).'.'.$ext;if(!move_uploaded_file($f['tmp_name'],__DIR__.'/uploads/'.$name))fail('No se pudo guardar la imagen.',500);json_out(['url'=>'/uploads/'.$name]);
        case 'admin-order':
            $u=require_user(true);$id=text_field($d['id']??'',5,40);$status=text_field($d['status']??'',1,30);db()->beginTransaction();$lock=db()->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'?' FOR UPDATE':'';$o=row('SELECT * FROM orders WHERE id=?'.$lock,[$id]);if(!$o)fail('Pedido no encontrado.',404);
            $allowed=['pending'=>['paid','cancelled'],'paid'=>['processing','cancelled'],'processing'=>['shipped','cancelled'],'shipped'=>['delivered']];if(!in_array($status,$allowed[$o['status']]??[],true))fail('Ese cambio de estado no está permitido.');
            if(in_array($status,['paid','cancelled'],true)&&!in_array($o['provider'],['demo','transfer'],true))fail('El pago debe confirmarse por la pasarela. Gestiona cancelaciones externas con soporte.');
            if($status==='cancelled'&&$o['payment_status']==='paid'&&($o['provider']!=='demo'||empty($d['reason'])))fail('Solo se anulan ventas de demostración pagadas, con motivo. Los reembolsos reales requieren la pasarela.',409);
            $tracking=text_field($d['tracking']??$o['tracking'],0,200);if($status==='shipped'&&strlen($tracking)<3)fail('Agrega la paquetería y el número de guía.');$pay=$status==='paid'?'paid':($status==='cancelled'?'cancelled':$o['payment_status']);
            query('UPDATE orders SET status=?,payment_status=?,tracking=?,updated_at=? WHERE id=?',[$status,$pay,$tracking,now(),$id]);if($status==='cancelled')return_inventory($o,(int)$u['id']);
            $labels=['paid'=>'Pago confirmado','processing'=>'Preparando tu pedido','shipped'=>'Pedido enviado: '.$tracking,'delivered'=>'Pedido entregado','cancelled'=>'Pedido cancelado'];order_event($id,$labels[$status]);audit($u['id'],'order-status',['id'=>$id,'before'=>$o['status'],'after'=>$status,'reason'=>text_field($d['reason']??'',0,500)]);db()->commit();$customer=row('SELECT email FROM users WHERE id=?',[$o['user_id']]);send_email($customer['email'],'Actualización de tu pedido '.$id,$labels[$status]."\n".config()['base_url'].'/#pedido?id='.$id);json_out(['ok'=>true]);
        case 'admin-settings':
            $u=require_user(true);$allowed=['store_name'=>80,'support_email'=>190,'support_phone'=>30,'bank_instructions'=>2000,'legal_name'=>200,'legal_address'=>500,'privacy_text'=>15000,'terms_text'=>15000,'returns_text'=>15000];$next=settings();
            foreach($allowed as $k=>$max)if(array_key_exists($k,$d))$next[$k]=text_field($d[$k],0,$max);
            if($next['support_email']&&!filter_var($next['support_email'],FILTER_VALIDATE_EMAIL))fail('Correo de soporte inválido.');
            if(isset($d['tax_rate_bp']))$next['tax_rate_bp']=integer($d['tax_rate_bp'],0,10000);
            foreach(['shipping_price','free_shipping'] as $k)if(isset($d[$k]))$next[$k]=integer($d[$k],0,100000000);
            if(isset($d['demo_mode']))$next['demo_mode']=(bool)$d['demo_mode'];
            if(!$next['demo_mode']) {foreach(['legal_name','legal_address','support_email','privacy_text','terms_text','returns_text'] as $k)if(!$next[$k])fail('Completa los datos comerciales y las políticas antes de activar ventas.');if(!$next['bank_instructions']&&!public_settings()['payments']['stripe']&&!public_settings()['payments']['mercadopago'])fail('Configura al menos una forma de pago.');}
            db()->beginTransaction();foreach($next as $k=>$v)put_setting($k,$v);audit($u['id'],'settings',['demo_mode'=>$next['demo_mode']]);db()->commit();json_out(['ok'=>true]);
        default:fail('Ruta no encontrada.',404);
    }
} catch (HttpError $e) { if(isset($GLOBALS['unused'])){} try{if(db()->inTransaction())db()->rollBack();}catch(Throwable $ignored){} json_out(['error'=>$e->getMessage()],($rest??false)&&$e->getCode()===400&&$e->getMessage()!=='Solicitud inválida.'?422:($e->getCode()?:400)); }
catch(PDOException $e){try{if(db()->inTransaction())db()->rollBack();}catch(Throwable $ignored){}$conflict=in_array((string)$e->getCode(),['23000','23505','19'],true);error_log('NexTech DB: '.$e->getCode());json_out(['error'=>$conflict?'El registro ya existe o está relacionado con otros datos.':'No se pudo completar la operación. Intenta de nuevo.'],$conflict?409:500);}
catch(Throwable $e) {try{if(db()->inTransaction())db()->rollBack();}catch(Throwable $ignored){}error_log('NexTech '.$e->getMessage());json_out(['error'=>'No se pudo completar la operación. Intenta de nuevo.'],500);}

<?php
declare(strict_types=1);
function paginated(string $from,string $columns,string $where,array $args,array $sorts,string $default): array {
    $page=integer($_GET['page']??1,1,100000);$limit=integer($_GET['limit']??20,1,100);
    $sort=$sorts[$_GET['sort']??'']??$default;$offset=($page-1)*$limit;
    $total=(int)row("SELECT COUNT(*) n FROM $from WHERE $where",$args)['n'];
    $items=query("SELECT $columns FROM $from WHERE $where ORDER BY $sort LIMIT $limit OFFSET $offset",$args)->fetchAll();
    return ['items'=>$items,'total'=>$total,'page'=>$page,'limit'=>$limit,'pages'=>(int)ceil($total/$limit)];
}
function list_filter(array $columns): array {
    $where=['1=1'];$args=[];
    if(isset($_GET['active'])&&$_GET['active']!==''){$where[]='active=?';$args[]=integer($_GET['active'],0,1);}
    if(!empty($_GET['q'])){$where[]='('.implode(' OR ',array_map(fn($c)=>"$c LIKE ?",$columns)).')';$term='%'.text_field($_GET['q'],1,100).'%';$args=array_fill(0,count($columns),$term)+[];if(isset($_GET['active'])&&$_GET['active']!=='')array_unshift($args,(int)$_GET['active']);}
    return [implode(' AND ',$where),$args];
}
function admin_extension(string $action,string $method,array $d): void {
    $known=['admin-categories','admin-category','admin-category-save','admin-category-delete','admin-suppliers','admin-supplier','admin-supplier-save','admin-supplier-delete','admin-users','admin-user','admin-user-save','admin-user-delete','admin-roles','admin-movements','admin-reports','admin-product-delete'];
    if(!in_array($action,$known,true))return;
    $u=require_user(true);
    if($method==='GET'){
        if($action==='admin-roles')json_out(query('SELECT * FROM roles ORDER BY code')->fetchAll());
        if($action==='admin-reports')json_out(reports());
        if($action==='admin-movements')json_out(paginated('inventory_movements m JOIN products p ON p.id=m.product_id JOIN users u ON u.id=m.user_id','m.*,p.name product,u.name actor','1=1',[],['oldest'=>'m.id ASC','newest'=>'m.id DESC'],'m.id DESC'));
        $resources=['admin-categories'=>['categories','slug,name,icon,position,active',['name','slug'],'position,slug'], 'admin-suppliers'=>['suppliers','*',['name','email'],'id DESC'], 'admin-users'=>['users','id,name,email,phone,role,active,created_at',['name','email'],'id DESC']];
        if(isset($resources[$action])){[$table,$cols,$search,$sort]=$resources[$action];[$where,$args]=list_filter($search);json_out(paginated($table,$cols,$where,$args,['name'=>'name ASC','name-desc'=>'name DESC','newest'=>$sort],$sort));}
        $single=['admin-category'=>['categories','slug'],'admin-supplier'=>['suppliers','id'],'admin-user'=>['users','id']];
        if(isset($single[$action])){[$table,$key]=$single[$action];$r=row("SELECT * FROM $table WHERE $key=?",[$_GET['id']??'']);if(!$r)fail('Registro no encontrado.',404);unset($r['password'],$r['auth_version']);json_out($r);}
        fail('Método no permitido.',405);
    }
    db()->beginTransaction();
    if($action==='admin-product-delete'){
        $id=integer($d['id']??0,1,PHP_INT_MAX);$before=row('SELECT id,active FROM products WHERE id=?',[$id]);if(!$before)fail('Producto no encontrado.',404);
        query('UPDATE products SET active=0,updated_at=? WHERE id=?',[now(),$id]);audit($u['id'],'disable-product',['id'=>$id,'before'=>$before,'after'=>['active'=>0]]);db()->commit();json_out(['ok'=>true]);
    }
    $type=str_contains($action,'category')?'category':(str_contains($action,'supplier')?'supplier':'user');
    $table=['category'=>'categories','supplier'=>'suppliers','user'=>'users'][$type];$key=$type==='category'?'slug':'id';
    $id=$d['id']??null;$before=$id?row("SELECT * FROM $table WHERE $key=?",[$id]):null;
    if($id&&!$before)fail('Registro no encontrado.',404);
    if(str_ends_with($action,'-delete')){if(!$before)fail('Registro no encontrado.',404);$values=['active'=>0];}
    else {
        $values=['name'=>text_field($d['name']??'',2,$type==='category'?90:120),'active'=>integer($d['active']??1,0,1)];
        if($type==='category'){
            $slug=$before['slug']??text_field($d['slug']??'',2,40);if(!preg_match('/^[a-z0-9-]+$/',$slug))fail('Usa letras minúsculas, números y guiones para la clave.',422);
            $values+=['slug'=>$slug,'icon'=>text_field($d['icon']??'box',1,30),'position'=>integer($d['position']??0,0,1000)];
        } else {$values+=['email'=>email_field($d['email']??''),'phone'=>text_field($d['phone']??'',0,30)];}
        if($type==='user'){
            $role=text_field($d['role']??'customer',1,20);if(!row('SELECT code FROM roles WHERE code=?',[$role]))fail('Rol inválido.',422);$values['role']=$role;
            if(!$before||!empty($d['password']))$values['password']=password_hash(password_field($d['password']??''),PASSWORD_DEFAULT);
        }
    }
    if($type==='category'&&!$values['active']&&$before&&row('SELECT id FROM products WHERE category=? AND active=1',[$id]))fail('Mueve u oculta los productos activos antes de desactivar la categoría.',409);
    if($type==='supplier'&&!$values['active']&&$before&&row('SELECT id FROM products WHERE supplier_id=? AND active=1',[$id]))fail('Reasigna los productos activos antes de desactivar el proveedor.',409);
    if($type==='user'&&$before){
        if((int)$id===(int)$u['id']&&(!$values['active']||($values['role']??$before['role'])!=='admin'))fail('No puedes retirar tu propio acceso administrativo.',409);
        // Serialize changes to administrators in MySQL; SQLite already holds a write transaction.
        if(db()->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql')query("SELECT id FROM users WHERE role='admin' AND active=1 FOR UPDATE")->fetchAll();
        if($before['role']==='admin'&&(!$values['active']||($values['role']??'admin')!=='admin')&&(int)row("SELECT COUNT(*) n FROM users WHERE role='admin' AND active=1")['n']<=1)fail('Debe conservarse al menos un administrador activo.',409);
        $values['auth_version']=(int)$before['auth_version']+1;
    }
    if($before){$set=implode(',',array_map(fn($k)=>"$k=?",array_keys($values)));query("UPDATE $table SET $set WHERE $key=?",[...array_values($values),$id]);}
    else {if($type!=='category')$values['created_at']=now();$cols=implode(',',array_keys($values));$marks=implode(',',array_fill(0,count($values),'?'));query("INSERT INTO $table($cols) VALUES($marks)",array_values($values));$id=$type==='category'?$values['slug']:(int)db()->lastInsertId();}
    unset($before['password'],$before['auth_version'],$values['password'],$values['auth_version']);
    audit($u['id'],$action,['id'=>$id,'before'=>$before,'after'=>$values]);db()->commit();
    json_out(['id'=>$id,'ok'=>true],$before?200:201);
}
function reports(): array {
    $start=$_GET['start']??gmdate('Y-m-01');$end=$_GET['end']??gmdate('Y-m-d');
    foreach([$start,$end] as $date){$v=DateTimeImmutable::createFromFormat('!Y-m-d',$date);if(!$v||$v->format('Y-m-d')!==$date)fail('Selecciona fechas válidas.',422);}
    if($start>$end)fail('La fecha inicial no puede superar la final.',422);
    $scope=$_GET['scope']??'real';if(!in_array($scope,['real','demo'],true))fail('Selecciona ventas reales o demostración.',422);
    $condition=$scope==='demo'?"provider='demo'":"provider!='demo'";
    $where="created_at>=? AND created_at<=? AND payment_status='paid' AND status!='cancelled' AND $condition";$args=[$start.'T00:00:00Z',$end.'T23:59:59Z'];
    $sales=query("SELECT SUBSTR(created_at,1,10) day,COUNT(*) orders,SUM(total) total,SUM(tax) tax FROM orders WHERE $where GROUP BY SUBSTR(created_at,1,10) ORDER BY day",$args)->fetchAll();
    $stock=query('SELECT c.name category,COUNT(p.id) products,COALESCE(SUM(p.stock),0) units,COALESCE(SUM(p.price*p.stock),0) value,SUM(CASE WHEN p.stock<=5 THEN 1 ELSE 0 END) low_stock FROM categories c LEFT JOIN products p ON p.category=c.slug AND p.active=1 WHERE c.active=1 GROUP BY c.slug,c.name ORDER BY c.name')->fetchAll();
    $totals=row("SELECT COUNT(*) orders,COALESCE(SUM(total),0) total,COALESCE(SUM(tax),0) tax FROM orders WHERE $where",$args);
    return ['start'=>$start,'end'=>$end,'scope'=>$scope,'sales'=>$sales,'inventory'=>$stock,'totals'=>$totals,'generated_at'=>now(),'date_basis'=>'UTC','inventory_note'=>'Existencias actuales, sin filtro de fechas. Valor a precio de venta, no costo.'];
}

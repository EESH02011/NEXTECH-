<?php
declare(strict_types=1);
date_default_timezone_set('UTC');
function config(): array {
    static $c; if ($c === null) {
        $path = getenv('NEXTECH_CONFIG') ?: __DIR__.'/config.php';
        if (!is_file($path)) fail('Primero configura e instala NexTech.',503);
        $c = require $path;
    } return $c;
}
function db(): PDO {
    static $db; if ($db) return $db;
    $d=config()['db'];
    if ($d['driver']==='sqlite') {
        $db=new PDO('sqlite:'.($d['path']??__DIR__.'/storage/nextech.db'));
        $db->exec('PRAGMA foreign_keys=ON; PRAGMA journal_mode=WAL; PRAGMA busy_timeout=5000;');
    } else {
        $db=new PDO('mysql:host='.$d['host'].';port='.($d['port']??3306).';dbname='.$d['database'].';charset=utf8mb4',$d['username'],$d['password']);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
    return $db;
}
function query(string $sql,array $args=[]): PDOStatement { $s=db()->prepare($sql); $s->execute($args); return $s; }
function row(string $sql,array $args=[]): ?array { return query($sql,$args)->fetch() ?: null; }
function now(): string { return gmdate('Y-m-d\TH:i:s\Z'); }
function json_out(mixed $value,int $code=200): never { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode($value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function fail(string $message,int $code=400): never { throw new HttpError($message,$code); }
class HttpError extends RuntimeException {}
function text_field(mixed $v,int $min=0,int $max=500): string { if(!is_string($v))fail('Dato de texto inválido.'); $v=trim($v); if(strlen($v)<$min||strlen($v)>$max)fail("Revisa el texto: entre $min y $max caracteres."); return $v; }
function integer(mixed $v,int $min,int $max): int { if(filter_var($v,FILTER_VALIDATE_INT)===false||$v<$min||$v>$max)fail('Cantidad fuera del rango permitido.'); return (int)$v; }
function email_field(mixed $v): string { $v=strtolower(text_field($v,3,190)); if(!filter_var($v,FILTER_VALIDATE_EMAIL))fail('Escribe un correo electrónico válido.'); return $v; }
function password_field(mixed $p): string { $p=text_field($p,10,128); return $p; }
function start_session(): void {
    if(session_status()===PHP_SESSION_ACTIVE)return;
    ini_set('session.use_strict_mode','1'); ini_set('session.use_only_cookies','1');
    session_name('nextech_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>str_starts_with(config()['base_url'],'https://'),'httponly'=>true,'samesite'=>'Lax']);
    session_start();
    if(isset($_SESSION['last_seen']) && time()-$_SESSION['last_seen']>7200) { $_SESSION=[];session_regenerate_id(true); }
    $_SESSION['last_seen']=time();
    $_SESSION['csrf']??=bin2hex(random_bytes(32));
}
function csrf(): void { if(!hash_equals($_SESSION['csrf']??'',$_SERVER['HTTP_X_CSRF_TOKEN']??'')||empty($_SESSION['csrf']))fail('Tu sesión cambió. Recarga la página.',403); }
function current_user(): ?array {
    if(empty($_SESSION['uid']))return null;
    $u=row('SELECT id,name,email,role,phone,auth_version,created_at FROM users WHERE id=?',[$_SESSION['uid']]);
    if(!$u || $u['auth_version']!==($_SESSION['auth_version']??null)){unset($_SESSION['uid']);return null;}
    unset($u['auth_version']);return $u;
}
function require_user(bool $admin=false): array { $u=current_user();if(!$u)fail('Inicia sesión para continuar.',401);if($admin&&$u['role']!=='admin')fail('Acceso restringido.',403);return $u; }
function login_user(array $u): void { session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['auth_version']=$u['auth_version'];$_SESSION['csrf']=bin2hex(random_bytes(32)); }
function rate_limit(string $label,int $max=10,int $seconds=900): void {
    $key=hash('sha256',$label.'|'.($_SERVER['REMOTE_ADDR']??'cli'));
    query('DELETE FROM rate_limits WHERE expires_at < ?',[time()]);
    try{query('INSERT INTO rate_limits(bucket,attempts,expires_at) VALUES(?,0,?)',[$key,time()+$seconds]);}catch(PDOException $e){if(!str_contains($e->getMessage(),'UNIQUE')&&!str_contains($e->getMessage(),'Duplicate'))throw $e;}
    $s=query('UPDATE rate_limits SET attempts=attempts+1 WHERE bucket=? AND attempts<?',[$key,$max]);
    if(!$s->rowCount())fail('Demasiados intentos. Intenta de nuevo en unos minutos.',429);
}
function settings(): array {
    $s=['store_name'=>'NexTech','currency'=>'MXN','demo_mode'=>true,'shipping_price'=>14900,'free_shipping'=>299900,'support_email'=>'','support_phone'=>'','bank_instructions'=>'','legal_name'=>'','legal_address'=>'','privacy_text'=>'','terms_text'=>'','returns_text'=>''];
    foreach(query('SELECT * FROM settings')->fetchAll() as $r)$s[$r['setting_key']]=json_decode($r['setting_value'],true);
    return $s;
}
function put_setting(string $k,mixed $v): void {
    if(row('SELECT setting_key FROM settings WHERE setting_key=?',[$k]))query('UPDATE settings SET setting_value=? WHERE setting_key=?',[json_encode($v,JSON_UNESCAPED_UNICODE),$k]);
    else query('INSERT INTO settings(setting_key,setting_value) VALUES(?,?)',[$k,json_encode($v,JSON_UNESCAPED_UNICODE)]);
}
function public_settings(): array {
    $s=settings();unset($s['bank_instructions']);
    $c=config();$s['payments']=['demo'=>(bool)$s['demo_mode'],'transfer'=>!empty(settings()['bank_instructions']),'mercadopago'=>!empty($c['mercadopago']['access_token'])&&!empty($c['mercadopago']['webhook_secret']),'stripe'=>!empty($c['stripe']['secret_key'])&&!empty($c['stripe']['webhook_secret'])];
    $s['email_enabled']=!empty($c['smtp']['username'])||($c['environment']??'')==='development';
    return $s;
}
function product(array $p): array { foreach(['id','price','compare_price','stock','active','featured','verified'] as $k)$p[$k]=(int)$p[$k];$p['specs']=json_decode($p['specs'],true)?:[];return $p; }
function audit(int $uid,string $action,mixed $detail): void { query('INSERT INTO audit(user_id,action,detail,created_at) VALUES(?,?,?,?)',[$uid,$action,json_encode($detail,JSON_UNESCAPED_UNICODE),now()]); }
function order_event(string $id,string $message): void { query('INSERT INTO order_events(order_id,message,created_at) VALUES(?,?,?)',[$id,$message,now()]); }
function order_detail(array $o): array {
    $o['address']=json_decode($o['address'],true);$o['items']=query('SELECT * FROM order_items WHERE order_id=?',[$o['id']])->fetchAll();$o['events']=query('SELECT message,created_at FROM order_events WHERE order_id=? ORDER BY id',[$o['id']])->fetchAll();
    foreach(['subtotal','shipping','total'] as $k)$o[$k]=(int)$o[$k];
    if($o['provider']==='transfer'&&$o['payment_status']==='pending')$o['bank_instructions']=settings()['bank_instructions'];
    unset($o['idempotency_key']);return $o;
}
function address_fields(array $d): array {
    $a=[];foreach(['recipient'=>120,'phone'=>30,'street'=>250,'colony'=>120,'city'=>120,'state'=>120,'postal_code'=>5] as $k=>$max)$a[$k]=text_field($d[$k]??'',2,$max);
    if(!preg_match('/^\d{5}$/',$a['postal_code']))fail('El código postal debe tener 5 dígitos.');
    if(!preg_match('/^[+\d\s()\-]{10,30}$/',$a['phone']))fail('Revisa el teléfono de entrega.');
    $a['notes']=text_field($d['notes']??'',0,500);return $a;
}
function send_email(string $to,string $subject,string $body): bool {
    $c=config();
    if(($c['environment']??'')==='development') {file_put_contents($c['mail_log']??__DIR__.'/storage/mail.log',json_encode(['to'=>$to,'subject'=>$subject,'body'=>$body],JSON_UNESCAPED_UNICODE)."\n",FILE_APPEND|LOCK_EX);return true;}
    if(empty($c['smtp']['username']))return false;
    require_once __DIR__.'/vendor/PHPMailer/Exception.php';require_once __DIR__.'/vendor/PHPMailer/PHPMailer.php';require_once __DIR__.'/vendor/PHPMailer/SMTP.php';
    try{$m=new PHPMailer\PHPMailer\PHPMailer(true);$m->isSMTP();$m->Host=$c['smtp']['host'];$m->Port=(int)$c['smtp']['port'];$m->SMTPAuth=true;$m->Username=$c['smtp']['username'];$m->Password=$c['smtp']['password'];$m->SMTPSecure=$c['smtp']['encryption'];$m->Timeout=10;$m->CharSet='UTF-8';$m->setFrom($c['smtp']['from_email'],$c['smtp']['from_name']);$m->addAddress($to);$m->Subject=$subject;$m->Body=$body;$m->send();return true;}catch(Throwable $e){error_log('No se pudo enviar correo de NexTech.');return false;}
}

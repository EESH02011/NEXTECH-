<?php
declare(strict_types=1);
require __DIR__.'/../private/lib.php';
$error='';$success=false;
if(is_file(__DIR__.'/../private/installed.lock')){http_response_code(403);exit('NexTech ya está instalado. El instalador está bloqueado.');}
try {
    config();start_session();
    if($_SERVER['REQUEST_METHOD']==='POST'){
        if(!hash_equals($_SESSION['csrf'],$_POST['csrf']??''))fail('Recarga la página e inténtalo de nuevo.');
        $keyPath=__DIR__.'/../private/setup.key';if(!is_file($keyPath)||!hash_equals(trim(file_get_contents($keyPath)),trim($_POST['key']??'')))fail('Clave de instalación incorrecta.');
        $email=email_field($_POST['email']??'');$name=text_field($_POST['name']??'',2,120);$password=password_field($_POST['password']??'');
        if(($_POST['password_confirm']??'')!==$password)fail('Las contraseñas no coinciden.');
        require __DIR__.'/../private/schema.php';require __DIR__.'/../private/seed.php';migrate(db());
        if(row("SELECT id FROM users WHERE role='admin'"))fail('Ya existe un administrador. Bloquea el instalador creando private/installed.lock.');
        seed_catalog(db());query("INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,'admin',?)",[$name,$email,password_hash($password,PASSWORD_DEFAULT),now()]);
        if(file_put_contents(__DIR__.'/../private/installed.lock',now(),LOCK_EX)===false)fail('No se pudo bloquear el instalador. Revisa los permisos de private.',500);$success=true;
    }
} catch(Throwable $e){$error=$e instanceof HttpError?$e->getMessage():'Revisa la configuración de MySQL y los permisos de la carpeta private.';}
function esc(string $s):string{return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?><!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalar NexTech</title><link rel="stylesheet" href="/style.css"></head><body><main class="install panel"><a class="wordmark" href="/">NEXTECH</a><h1>Tu tienda empieza aquí.</h1><?php if($success):?><p>La tienda está instalada. Inicia sesión con la cuenta que acabas de crear.</p><a class="button" href="/#cuenta">Entrar a NexTech</a><?php else:?><p>Configura primero <strong>private/config.php</strong> con los datos de MySQL de Hostinger. Encontrarás la clave de instalación en <strong>private/setup.key</strong>.</p><?php if($error):?><p role="alert" class="notice error"><?=esc($error)?></p><?php endif;?><form method="post" class="stack"><input type="hidden" name="csrf" value="<?=esc($_SESSION['csrf']??'')?>"><label>Clave de instalación<input name="key" type="password" required autocomplete="off"></label><label>Nombre del administrador<input name="name" required minlength="2" maxlength="120" autocomplete="name"></label><label>Correo del administrador<input name="email" type="email" required autocomplete="email"></label><label>Contraseña (10 caracteres mínimo)<input name="password" type="password" required minlength="10" maxlength="128" autocomplete="new-password"></label><label>Confirmar contraseña<input name="password_confirm" type="password" required minlength="10" autocomplete="new-password"></label><button type="submit" class="button">Instalar tienda y crear administrador</button></form><p class="muted">El catálogo se instala con existencias en cero y ventas de demostración. Confirma tus precios, stock y políticas desde Administración antes de activar cobros.</p><?php endif;?></main></body></html>

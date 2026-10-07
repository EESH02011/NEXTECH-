<?php
declare(strict_types=1);
// Only read data; never execute .env content. Process environment takes precedence.
function environment_config(): array {
    $file=dirname(__DIR__).'/.env';
    if(!is_file($file))$file=__DIR__.'/.env';
    $values=[];
    if(is_file($file))foreach(file($file,FILE_IGNORE_NEW_LINES) as $line){
        $line=trim($line);if($line===''||str_starts_with($line,'#'))continue;
        if(!preg_match('/^([A-Z][A-Z0-9_]*)=(.*)$/',$line,$m))throw new RuntimeException('Formato de entorno inválido.');
        $value=trim($m[2]);if(strlen($value)>=2&&in_array($value[0],["'",'"'],true)&&substr($value,-1)===$value[0])$value=substr($value,1,-1);
        $values[$m[1]]=$value;
    }
    $get=static fn($k,$fallback='')=>getenv($k)!==false?getenv($k):($values[$k]??$fallback);
    return ['environment'=>$get('APP_ENV','production'),'base_url'=>$get('APP_URL','https://tudominio.com'),
        'db'=>['driver'=>$get('DB_DRIVER','mysql'),'host'=>$get('DB_HOST','localhost'),'port'=>(int)$get('DB_PORT','3306'),'database'=>$get('DB_NAME'),'username'=>$get('DB_USER'),'password'=>$get('DB_PASSWORD'),'path'=>$get('DB_PATH',__DIR__.'/storage/nextech.db')],
        'smtp'=>['host'=>$get('SMTP_HOST','smtp.hostinger.com'),'port'=>(int)$get('SMTP_PORT','465'),'encryption'=>$get('SMTP_ENCRYPTION','ssl'),'username'=>$get('SMTP_USER'),'password'=>$get('SMTP_PASSWORD'),'from_email'=>$get('SMTP_FROM'),'from_name'=>$get('SMTP_NAME','NexTech')],
        'stripe'=>['secret_key'=>$get('STRIPE_SECRET_KEY'),'webhook_secret'=>$get('STRIPE_WEBHOOK_SECRET')],
        'mercadopago'=>['access_token'=>$get('MP_ACCESS_TOKEN'),'webhook_secret'=>$get('MP_WEBHOOK_SECRET')]];
}

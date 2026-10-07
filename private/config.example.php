<?php
// Copiar como config.php. Nunca colocar esta carpeta dentro de public_html.
return [
    'environment' => 'production',
    'base_url' => 'https://tudominio.com',
    'db' => ['driver' => 'mysql', 'host' => 'localhost', 'port' => 3306,
        'database' => 'u123456789_nextech', 'username' => 'u123456789_nextech', 'password' => 'CAMBIAR'],
    'smtp' => ['host' => 'smtp.hostinger.com', 'port' => 465, 'encryption' => 'ssl',
        'username' => '', 'password' => '', 'from_email' => '', 'from_name' => 'NexTech'],
    'mercadopago' => ['access_token' => '', 'webhook_secret' => ''],
    'stripe' => ['secret_key' => '', 'webhook_secret' => ''],
];

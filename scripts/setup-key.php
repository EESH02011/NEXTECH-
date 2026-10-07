<?php
if(PHP_SAPI!=='cli')exit('Solo CLI');
$path=__DIR__.'/../private/setup.key';
if(!is_file($path))file_put_contents($path,bin2hex(random_bytes(24)),LOCK_EX);
echo "Clave del instalador disponible en private/setup.key.\n";

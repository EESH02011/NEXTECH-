<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit('Solo CLI');
require __DIR__.'/../private/schema.php';
class SchemaDialect extends PDO {
    public function __construct(){}
    public function getAttribute(int $attribute):mixed{return 'mysql';}
}
[$tables,$suffix]=schema_definitions(new SchemaDialect());
$sql="-- NexTech v2. MySQL 8.0.16+ / MariaDB 10.6+\nSET NAMES utf8mb4;\n\n";
foreach($tables as $table)$sql.='CREATE TABLE IF NOT EXISTS '.$table.$suffix.";\n\n";
$sql.="CREATE INDEX idx_products_category ON products(category,active);\nCREATE INDEX idx_orders_user ON orders(user_id,created_at);\nCREATE INDEX idx_orders_date ON orders(created_at,status);\nCREATE INDEX idx_movements_product ON inventory_movements(product_id,created_at);\n";
if(!is_dir(__DIR__.'/../database'))mkdir(__DIR__.'/../database');
file_put_contents(__DIR__.'/../database/schema.sql',$sql);file_put_contents(__DIR__.'/../database.sql',$sql);
echo "DDL exportado.\n";

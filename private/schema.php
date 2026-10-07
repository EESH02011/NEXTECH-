<?php
declare(strict_types=1);
function migrate(PDO $db): void {
    $mysql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
    $id = $mysql ? 'INTEGER PRIMARY KEY AUTO_INCREMENT' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $suffix = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';
    $tables = [
      "users (id $id, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL DEFAULT 'customer', phone VARCHAR(30) NOT NULL DEFAULT '', auth_version INTEGER NOT NULL DEFAULT 1, created_at VARCHAR(30) NOT NULL)",
      "categories (slug VARCHAR(40) PRIMARY KEY, name VARCHAR(90) NOT NULL, icon VARCHAR(30) NOT NULL, position INTEGER NOT NULL)",
      "products (id $id, sku VARCHAR(80) NOT NULL UNIQUE, category VARCHAR(40) NOT NULL, name VARCHAR(220) NOT NULL, brand VARCHAR(90) NOT NULL, description TEXT NOT NULL, specs TEXT NOT NULL, image VARCHAR(1500) NOT NULL DEFAULT '', source_url VARCHAR(1500) NOT NULL DEFAULT '', source_checked VARCHAR(20) NOT NULL DEFAULT '', price INTEGER NOT NULL DEFAULT 0, compare_price INTEGER NOT NULL DEFAULT 0, stock INTEGER NOT NULL DEFAULT 0, active INTEGER NOT NULL DEFAULT 1, featured INTEGER NOT NULL DEFAULT 0, verified INTEGER NOT NULL DEFAULT 0, updated_at VARCHAR(30) NOT NULL)",
      "cart (user_id INTEGER NOT NULL, product_id INTEGER NOT NULL, qty INTEGER NOT NULL, PRIMARY KEY(user_id,product_id))",
      "favorites (user_id INTEGER NOT NULL, product_id INTEGER NOT NULL, PRIMARY KEY(user_id,product_id))",
      "addresses (id $id, user_id INTEGER NOT NULL, label VARCHAR(50) NOT NULL, recipient VARCHAR(120) NOT NULL, phone VARCHAR(30) NOT NULL, street VARCHAR(250) NOT NULL, colony VARCHAR(120) NOT NULL, city VARCHAR(120) NOT NULL, state VARCHAR(120) NOT NULL, postal_code VARCHAR(10) NOT NULL, notes VARCHAR(500) NOT NULL DEFAULT '')",
      "orders (id VARCHAR(40) PRIMARY KEY, user_id INTEGER NOT NULL, status VARCHAR(30) NOT NULL, payment_status VARCHAR(30) NOT NULL, provider VARCHAR(30) NOT NULL, payment_id VARCHAR(190) UNIQUE, checkout_url TEXT, subtotal INTEGER NOT NULL, shipping INTEGER NOT NULL, total INTEGER NOT NULL, address TEXT NOT NULL, idempotency_key VARCHAR(100) NOT NULL UNIQUE, tracking VARCHAR(200) NOT NULL DEFAULT '', notes TEXT NOT NULL, created_at VARCHAR(30) NOT NULL, updated_at VARCHAR(30) NOT NULL)",
      "order_items (id $id, order_id VARCHAR(40) NOT NULL, product_id INTEGER NOT NULL, sku VARCHAR(80) NOT NULL, name VARCHAR(220) NOT NULL, price INTEGER NOT NULL, qty INTEGER NOT NULL, image VARCHAR(1500) NOT NULL DEFAULT '')",
      "order_events (id $id, order_id VARCHAR(40) NOT NULL, message VARCHAR(500) NOT NULL, created_at VARCHAR(30) NOT NULL)",
      "settings (setting_key VARCHAR(80) PRIMARY KEY, setting_value TEXT NOT NULL)",
      "reset_tokens (token_hash VARCHAR(64) PRIMARY KEY, user_id INTEGER NOT NULL, expires_at INTEGER NOT NULL)",
      "rate_limits (bucket VARCHAR(64) PRIMARY KEY, attempts INTEGER NOT NULL, expires_at INTEGER NOT NULL)",
      "audit (id $id, user_id INTEGER NOT NULL, action VARCHAR(100) NOT NULL, detail TEXT NOT NULL, created_at VARCHAR(30) NOT NULL)",
      "payment_events (event_id VARCHAR(190) PRIMARY KEY, created_at VARCHAR(30) NOT NULL)"
    ];
    foreach ($tables as $table) $db->exec('CREATE TABLE IF NOT EXISTS '.$table.$suffix);
    foreach (['products_category'=>'products(category,active)', 'orders_user'=>'orders(user_id,created_at)', 'items_order'=>'order_items(order_id)', 'addresses_user'=>'addresses(user_id)', 'events_order'=>'order_events(order_id)', 'resets_user'=>'reset_tokens(user_id)'] as $name=>$target) {
        try { $db->exec("CREATE INDEX idx_$name ON $target"); } catch (PDOException $e) {
            if (!str_contains($e->getMessage(),'already exists') && !str_contains($e->getMessage(),'Duplicate key name')) throw $e;
        }
    }
}

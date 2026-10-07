# Base de datos y diccionario de datos

18 tablas. MySQL 8.0.16+ / MariaDB 10.6+ en producción; SQLite en desarrollo y pruebas.

## Normalización
Los atributos de cuentas, categorías y proveedores dependen de su propia clave. Las relaciones N:M usan claves compuestas. Las especificaciones se separan en product_specs, con una propiedad por fila. No se guardan listas de especificaciones en products. Los nombres, precios y dirección de un pedido son instantáneas históricas: describen la operación original y deben conservarse aunque cambie el catálogo o el perfil. Esta desnormalización deliberada evita alterar ventas anteriores. Los JSON de settings/audit contienen configuración y eventos, no sustituyen las tablas del negocio.

Todos los importes se almacenan como enteros en centavos de MXN. Las fechas son ISO 8601 UTC en VARCHAR(30), lo que permite compararlas lexicográficamente; la API solo las genera con now(). Los periodos del reporte son días UTC, indicados en la interfaz.

## Integridad e índices
FK en cuentas/roles, productos/categorías/proveedores, líneas/ventas/productos y todas las relaciones de clientes. No hay borrado en cascada de ventas: usuarios, proveedores, categorías y productos se desactivan. UNIQUE en correo, SKU, referencia de pago e idempotencia. CHECK protege cantidades, stock no negativo, total y balances. Índices en categoría/activo, usuario/fecha, fecha/estado y producto/fecha en movimientos.

## addresses
Direcciones editables del cliente.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| user_id | INTEGER | Sí |  FK → users.id |
| label | VARCHAR(50) | Sí | - |
| recipient | VARCHAR(120) | Sí | - |
| phone | VARCHAR(30) | Sí | - |
| street | VARCHAR(250) | Sí | - |
| colony | VARCHAR(120) | Sí | - |
| city | VARCHAR(120) | Sí | - |
| state | VARCHAR(120) | Sí | - |
| postal_code | VARCHAR(10) | Sí | - |
| notes | VARCHAR(500) | Sí |  Default '' |

## audit
Trazabilidad administrativa con detalle anterior/posterior.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| user_id | INTEGER | Sí |  FK → users.id |
| action | VARCHAR(100) | Sí | - |
| detail | TEXT | Sí | - |
| created_at | VARCHAR(30) | Sí | - |

## cart
Unidades por producto y cliente; clave compuesta.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| user_id | INTEGER | Sí | PK 1 FK → users.id |
| product_id | INTEGER | Sí | PK 2 FK → products.id |
| qty | INTEGER | Sí | - |

## categories
Agrupación comercial; slug estable y baja lógica.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| slug | VARCHAR(40) | Sí | PK 1 |
| name | VARCHAR(90) | Sí | - |
| icon | VARCHAR(30) | Sí | - |
| position | INTEGER | Sí | - |
| active | INTEGER | Sí |  Default 1 |

## favorites
Relación muchos a muchos de clientes y productos.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| user_id | INTEGER | Sí | PK 1 FK → users.id |
| product_id | INTEGER | Sí | PK 2 FK → products.id |

## inventory_movements
Registro inmutable de entradas/salidas, saldo y responsable.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| product_id | INTEGER | Sí |  FK → products.id |
| user_id | INTEGER | Sí |  FK → users.id |
| order_id | VARCHAR(40) | No |  FK → orders.id |
| quantity | INTEGER | Sí | - |
| before_stock | INTEGER | Sí | - |
| after_stock | INTEGER | Sí | - |
| reason | VARCHAR(120) | Sí | - |
| created_at | VARCHAR(30) | Sí | - |

## order_events
Seguimiento visible del pedido.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| order_id | VARCHAR(40) | Sí |  FK → orders.id |
| message | VARCHAR(500) | Sí | - |
| created_at | VARCHAR(30) | Sí | - |

## order_items
Detalle histórico de precio, nombre y SKU al comprar.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| order_id | VARCHAR(40) | Sí |  FK → orders.id |
| product_id | INTEGER | Sí |  FK → products.id |
| sku | VARCHAR(80) | Sí | - |
| name | VARCHAR(220) | Sí | - |
| price | INTEGER | Sí | - |
| qty | INTEGER | Sí | - |
| image | VARCHAR(1500) | Sí |  Default '' |

## orders
Cabecera de venta y datos fiscales/entrega congelados al comprar.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | VARCHAR(40) | Sí | PK 1 |
| user_id | INTEGER | Sí |  FK → users.id |
| status | VARCHAR(30) | Sí | - |
| payment_status | VARCHAR(30) | Sí | - |
| provider | VARCHAR(30) | Sí | - |
| payment_id | VARCHAR(190) | No | - |
| checkout_url | TEXT | No | - |
| tax_rate_bp | INTEGER | Sí |  Default 1600 |
| tax | INTEGER | Sí |  Default 0 |
| net_total | INTEGER | Sí |  Default 0 |
| subtotal | INTEGER | Sí | - |
| shipping | INTEGER | Sí | - |
| total | INTEGER | Sí | - |
| address | TEXT | Sí | - |
| idempotency_key | VARCHAR(100) | Sí | - |
| tracking | VARCHAR(200) | Sí |  Default '' |
| notes | TEXT | Sí | - |
| created_at | VARCHAR(30) | Sí | - |
| updated_at | VARCHAR(30) | Sí | - |

## payment_events
Identificadores de notificaciones procesadas para evitar duplicados.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| event_id | VARCHAR(190) | Sí | PK 1 |
| created_at | VARCHAR(30) | Sí | - |

## product_specs
Una propiedad por fila; clave compuesta producto/propiedad.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| product_id | INTEGER | Sí | PK 1 FK → products.id |
| spec_key | VARCHAR(80) | Sí | PK 2 |
| spec_value | VARCHAR(500) | Sí | - |

## products
SKU, categoría, proveedor, precios en centavos y stock.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| sku | VARCHAR(80) | Sí | - |
| category | VARCHAR(40) | Sí |  FK → categories.slug |
| name | VARCHAR(220) | Sí | - |
| brand | VARCHAR(90) | Sí | - |
| description | TEXT | Sí | - |
| image | VARCHAR(1500) | Sí |  Default '' |
| source_url | VARCHAR(1500) | Sí |  Default '' |
| source_checked | VARCHAR(20) | Sí |  Default '' |
| price | INTEGER | Sí |  Default 0 |
| compare_price | INTEGER | Sí |  Default 0 |
| stock | INTEGER | Sí |  Default 0 |
| active | INTEGER | Sí |  Default 1 |
| featured | INTEGER | Sí |  Default 0 |
| verified | INTEGER | Sí |  Default 0 |
| supplier_id | INTEGER | No |  FK → suppliers.id |
| updated_at | VARCHAR(30) | Sí | - |

## rate_limits
Contadores de límite de intentos por IP y operación.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| bucket | VARCHAR(64) | Sí | PK 1 |
| attempts | INTEGER | Sí | - |
| expires_at | INTEGER | Sí | - |

## reset_tokens
Hash y vencimiento de recuperación de contraseña.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| token_hash | VARCHAR(64) | Sí | PK 1 |
| user_id | INTEGER | Sí |  FK → users.id |
| expires_at | INTEGER | Sí | - |

## roles
Catálogo de permisos: administrador y cliente.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| code | VARCHAR(20) | Sí | PK 1 |
| name | VARCHAR(60) | Sí | - |

## settings
Preferencias de la tienda; valores tipados serializados.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| setting_key | VARCHAR(80) | Sí | PK 1 |
| setting_value | TEXT | Sí | - |

## suppliers
Directorio de proveedores, vínculo opcional con productos.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| name | VARCHAR(120) | Sí | - |
| email | VARCHAR(190) | Sí | - |
| phone | VARCHAR(30) | Sí |  Default '' |
| active | INTEGER | Sí |  Default 1 |
| created_at | VARCHAR(30) | Sí | - |

## users
Cuentas con hash de contraseña, rol, estado y versión de sesión.

| Campo | Tipo | Obligatorio | Clave / valor inicial |
|---|---|---|---|
| id | INTEGER | Sí | PK 1 |
| name | VARCHAR(120) | Sí | - |
| email | VARCHAR(190) | Sí | - |
| password | VARCHAR(255) | Sí | - |
| role | VARCHAR(20) | Sí |  FK → roles.code Default 'customer' |
| phone | VARCHAR(30) | Sí |  Default '' |
| auth_version | INTEGER | Sí |  Default 1 |
| active | INTEGER | Sí |  Default 1 |
| created_at | VARCHAR(30) | Sí | - |

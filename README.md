# NexTech · Tienda para Hostinger

Tienda en español con diseño oscuro, morado y cian, catálogo de **300 productos reales (20 por cada una de 15 categorías)** y datos persistentes. PHP 8.2 o superior, MySQL 8 / MariaDB 10.6 o superior. No requiere Node.js, Docker ni Composer en Hostinger.

## Qué incluye

- Portada, menú de categorías, buscador por nombre, marca, SKU y descripción; filtros, ordenación y paginación.
- Fichas con especificaciones, imágenes locales y enlaces al fabricante.
- Registro e inicio de sesión, perfil, contraseña, recuperación por correo, direcciones y favoritos.
- Carrito de visitante y carrito persistente del cliente; fusión al iniciar sesión.
- Checkout con cálculo de precios en servidor, reserva de existencias y protección contra pedidos duplicados.
- Historial y detalle de pedidos, cancelación de pedidos pendientes por transferencia o demostración, seguimiento con guía.
- Administración: productos, fotos, precios, inventario, pedidos, clientes, configuración, auditoría y exportación CSV.
- Conectores de Checkout Pro de Mercado Pago y Stripe Checkout con validación de firma de notificaciones y comprobación de importe y moneda. Transferencia con confirmación manual.
- Instalador de un solo uso, esquema MySQL y herramienta de respaldo.

## Instalar en Hostinger

1. Crea un sitio PHP en tu plan de hosting y activa su certificado SSL. Esta entrega necesita un plan con **PHP y bases de datos MySQL**. El producto independiente “Creador de sitios web” no ejecuta este código; en ese caso crea un sitio PHP dentro del hosting compatible.
2. En hPanel crea una base de datos MySQL y un usuario con acceso a ella. Guarda host, nombre completo de la base, usuario y contraseña.
3. Descomprime el paquete. En el administrador de archivos coloca el contenido de `public_html/` dentro de la carpeta pública de tu dominio. Coloca `private/` a su mismo nivel, **fuera de la carpeta pública**:

   ```text
   carpeta-del-dominio/
   ├── private/
   │   ├── config.php
   │   ├── catalog.json
   │   └── ...
   └── public_html/
       ├── index.html
       ├── api.php
       ├── install.php
       ├── assets/
       └── ...
   ```

4. Copia `private/config.example.php` como `private/config.php`. Cambia `base_url` por `https://tudominio.com` **sin barra final**, y rellena los datos MySQL. Mantén `environment` en `production`. El sitio está preparado para la raíz del dominio o subdominio, no para una subcarpeta.
5. En la configuración de PHP habilita PDO MySQL, cURL, OpenSSL, mbstring y fileinfo. Permite escritura al usuario del sitio en `private/`, `private/storage/` y `public_html/uploads/`; evita permisos globales 777. Usa PHP 8.2+ y límite de subida de al menos 4 MB.
6. Abre `https://tudominio.com/install.php`. Usa la clave del archivo `private/setup.key` y elige tu correo y contraseña de administrador. El instalador crea las tablas e importa el catálogo; no necesitas importar `database.sql` por separado.
7. Entra por “Mi cuenta”. Se mostrará el acceso a Administración. El instalador queda bloqueado al crear `private/installed.lock`. Después de instalar, elimina `public_html/install.php` y `private/setup.key` del servidor.
8. Verifica que `https://tudominio.com/private/config.php` no entrega ningún archivo. La carpeta `private` debe estar fuera de `public_html`; su `.htaccess` es una protección adicional.

## Preparar la venta real

El catálogo contiene modelos y referencias oficiales. **Los importes son ejemplos, no cotizaciones de proveedores. El paquete instala todas las existencias en cero.** La vista local de demostración utiliza 12 unidades ficticias para probar las compras y esa base de datos no se incluye en el paquete.

En Administración → Productos, confirma cada variante, presentación, compatibilidad, precio final e inventario. Marca “Precio, stock y datos verificados para venta real”. Los productos sin verificar no pueden comprarse en modo real. Puedes ocultar productos o crear otros.

Las fichas son un catálogo inicial, no un acuerdo de suministro. Algunos modelos son de otros mercados. La familia del modelo y las opciones de memoria, color, talla y licencia deben corresponder al SKU que realmente vendas. Los productos de software no incluyen claves o suscripciones compradas: define licencia, duración y entrega antes de activarlos. Los distintivos de Intel y algunas imágenes de familia se identifican como ilustrativos en las especificaciones. Las imágenes y marcas pertenecen a sus titulares; confirma el uso comercial autorizado con tus proveedores.

En Administración → Configuración, completa razón social, domicilio, atención a clientes, tarifas de envío y tus políticas de privacidad, términos y garantías. No se han inventado datos legales del negocio. Configura los pagos y el correo. Después de probar las integraciones en sus entornos de prueba, desactiva “Mantener el modo de demostración”. No se activan cobros reales mientras la demostración está encendida.

## Pagos

### Mercado Pago

En `private/config.php` rellena `mercadopago.access_token` y `mercadopago.webhook_secret`. Configura el webhook de **pagos** en tu integración de Mercado Pago:

```text
https://tudominio.com/api.php?action=webhook-mercadopago
```

Checkout Pro muestra los métodos disponibles en tu cuenta y país, que pueden incluir tarjeta, efectivo y otros medios. La tienda no promete que todos los medios estén aprobados para tu cuenta. Haz compras con credenciales de prueba antes de usar las de producción. No mezcles cuentas/credenciales de prueba con las de producción.

### Stripe

Rellena `stripe.secret_key` y `stripe.webhook_secret`. Crea el endpoint:

```text
https://tudominio.com/api.php?action=webhook-stripe
```

Suscríbelo a `checkout.session.completed` y `checkout.session.async_payment_succeeded`. Habilita los métodos deseados en Stripe. El servidor consulta la sesión, comprueba estado pagado, referencia, importe y MXN antes de confirmar el pedido. Prueba con claves y medios de prueba; después utiliza las claves de producción correspondientes.

### Transferencia

En la configuración de la tienda escribe banco, titular, CLABE e instrucciones. Los datos se muestran al comprador después de crear su pedido. Marca el pago como confirmado **solo después de verificarlo en tu banco**.

No se guardan números de tarjeta. PayPal directo no está integrado. Los reembolsos y controversias de pasarelas se tramitan en el panel del proveedor y con soporte; esta versión no automatiza reembolsos, facturación CFDI, generación de guías, compras a proveedores ni entrega de licencias digitales. Las guías se capturan manualmente en administración.

Los pedidos pendientes reservan inventario hasta su resolución. No hay vencimiento automático, para evitar liberar existencias de pagos en efectivo que todavía puedan acreditarse. Revisa periódicamente los pendientes. Los pedidos externos no se cancelan desde el cliente: resuélvelos con soporte y la pasarela. Un fallo al abrir la pasarela permite reintentar desde el detalle del pedido.

## Correo

Configura `smtp.host`, `port`, `encryption`, `username`, `password`, `from_email` y `from_name` en el archivo privado. Para Hostinger suele utilizarse el servidor SMTP asignado a tu servicio de correo; confirma los valores en hPanel. El remitente debe pertenecer a tu dominio autorizado. Sin SMTP no se enviarán recuperaciones ni avisos de pedidos. La interfaz de recuperación informa cuando el correo no está habilitado.

## Respaldo y mantenimiento

Usa las copias de seguridad de Hostinger para archivos y MySQL. Conserva también `public_html/uploads/` y el archivo privado de configuración en un lugar protegido. Si tu plan incluye terminal, puedes ejecutar `php scripts/backup.php`; genera una exportación SQL dentro de `private/storage/`. Nunca publiques los respaldos. Prueba una restauración antes de depender de ella.

Para futuras actualizaciones conserva `private/config.php`, `private/installed.lock`, `private/storage/`, `public_html/uploads/` y la base de datos. No vuelvas a sembrar el catálogo sobre productos ya editados.

## Validación incluida y límites

La batería `test/integration.mjs` usa Node.js 22 y PHP con PDO SQLite **solo para pruebas locales** y crea una base aislada temporal. Ejecuta `node test/integration.mjs` con `PHP_BINARY` apuntando a PHP si no está en PATH. No requiere Node.js en producción.

Se probaron 14 grupos funcionales: catálogo y categorías, búsqueda de laptops y filtros, sesiones/CSRF, permisos, direcciones, favoritos/perfil, stock, totales e idempotencia, privacidad de pedidos y cancelaciones, administración, estados de envío, bloqueo de ventas sin configuración, recuperación de contraseña y logout. Se revisó la interfaz en navegador y tamaño móvil.

No se ha instalado en tu cuenta de Hostinger ni se han realizado cobros reales. Las pruebas de base de datos se realizaron con SQLite; debes ejecutar el instalador y una compra de prueba sobre MySQL de Hostinger antes de abrir al público. Las integraciones externas requieren tus claves y una validación en su entorno de prueba. No sería correcto garantizar “100 %” de operación comercial antes de esos pasos.

## Referencias

Cada ficha enlaza a su fabricante; el detalle completo está en `CATALOGO-FUENTES.csv` y `private/catalog.json`. Documentación utilizada: [Hostinger: bases de datos](https://www.hostinger.com/support/mysql-databases/importing-and-exporting-databases/), [Mercado Pago: webhooks](https://www.mercadopago.com.mx/developers/es/docs/checkout-pro-preferences/additional-content/notifications/webhooks), [Stripe: verificación de firmas](https://docs.stripe.com/webhooks/signature), [PHPMailer](https://github.com/PHPMailer/PHPMailer).

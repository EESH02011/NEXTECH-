# Validación de NexTech

Fecha: 28 de septiembre de 2026. Entorno local con PHP 8.4 y PDO SQLite; aplicación preparada para PHP 8.2+ y MySQL en Hostinger.

Se aprobaron los 14 grupos de integración de `test/integration.mjs`. Cada ejecución crea su base temporal, pruebas de clientes y administrador, y la elimina al finalizar.

1. 300 productos, 15 categorías, 20 productos por categoría.
2. Búsqueda por nombre y descripción (incluidas las 12 laptops), filtros y precios ordenados. Una cadena de inyección SQL no cambia el alcance de la consulta.
3. Registro, inicio de sesión, rechazo de peticiones sin CSRF y administración restringida.
4. Las direcciones de un cliente no pueden modificarse desde otra cuenta.
5. Perfil y favoritos persistentes; el cliente no puede promover su rol.
6. Cantidades negativas y stock insuficiente rechazados.
7. Totales calculados desde precios del servidor, reserva de inventario y reintento idempotente sin doble descuento de stock.
8. Aislamiento de pedidos y cancelación que devuelve inventario una sola vez.
9. Alta/edición/ocultación de productos y registro en auditoría.
10. Estados pago → preparación → envío con guía → entrega, bloqueando saltos inválidos.
11. Activación comercial rechazada cuando faltan datos y métodos de pago; ventas de demostración excluidas de ingresos reales.
12. Recuperación de contraseña mediante token de un solo uso e invalidación de sesiones anteriores.
13. Los webhooks no configurados rechazan intentos de confirmar pagos.
14. Cerrar sesión revoca el acceso a los pedidos.

En navegador se comprobó el enlace superior Laptops (12 resultados), la imagen de laptop en portada, el carrito, inicio de sesión, checkout y creación de un pedido de demostración, y acceso al panel administrador. En 390 px se comprobó que carrito y checkout no desbordan el ancho de la pantalla. No se registraron errores de JavaScript en el flujo revisado.

Las 300 fichas tienen imágenes locales. Varias variantes comparten la imagen oficial de su familia; las imágenes de familia de Intel y AMD están identificadas en sus especificaciones. Las imágenes descargadas se optimizaron para web.

Pendiente al instalar: validación sobre MySQL de Hostinger, HTTPS, permisos de archivos, correo SMTP y notificaciones/cobros de prueba con las credenciales del dueño. No se ejecutaron cobros, reembolsos ni envíos reales. No se realizó una auditoría externa de seguridad ni una certificación comercial.

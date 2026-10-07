# Validación NexTech v2

Ejecutada el 6 de octubre de 2026 (México); el registro técnico almacena UTC.

Motor: SQLite / PHP 8.4.26. 23 grupos aprobados.

- CP-01: 300 productos y 20 por cada categoría. Aprobado.
- CP-02: Búsqueda, filtros y orden de precios. Aprobado.
- CP-03: Sesiones, CSRF y registro seguro. Aprobado.
- CP-04: Direcciones y aislamiento entre clientes. Aprobado.
- CP-05: Perfil y favoritos persistentes. Aprobado.
- CP-06: Stock insuficiente y validación de cantidades. Aprobado.
- CP-07: Checkout calcula total, reserva stock e impide doble pedido. Aprobado.
- CP-08: Pedidos privados y cancelación devuelve stock una sola vez. Aprobado.
- CP-09: Alta, edición y ocultación de producto con auditoría. Aprobado.
- CP-10: Flujo del pedido: pago, preparación, guía y entrega. Aprobado.
- CP-11: Ventas reales bloqueadas sin configuración comercial. Aprobado.
- CP-12: Recuperación de contraseña con token único e invalidación de sesión. Aprobado.
- CP-13: Webhooks sin configurar no aceptan pagos. Aprobado.
- CP-14: REST: métodos, validación 422, rutas 404 y permisos 403. Aprobado.
- CP-15: CRUD categorías y proveedores con duplicados y baja lógica. Aprobado.
- CP-16: CRUD usuarios, roles e invalidación de sesiones al dar baja. Aprobado.
- CP-17: REST productos: creación, edición, proveedor e integridad de categorías. Aprobado.
- CP-18: Paginación, búsqueda y orden administrativos consultan la BD. Aprobado.
- CP-19: Impuesto incluido, movimientos y cancelación administrativa con motivo. Aprobado.
- CP-20: Dos reportes: fechas, exclusión de anuladas y separación real/demo. Aprobado.
- CP-21: Integridad referencial, restricción stock y rollback completo ante fallo de movimiento. Aprobado.
- CP-22: JSON inválido 400 y error interno controlado 500. Aprobado.
- CP-23: Logout revoca acceso a la cuenta. Aprobado.

Interfaz: creación y desactivación de categoría; guardado de producto; búsqueda con 12 laptops de Computadoras; reportes con tres ventas sintéticas; vistas de escritorio, tableta 768 px y teléfono 390 px, sin desbordamiento de página. Se corrigió la colisión del campo HTML name=id con form.id usando getAttribute.

No se ejecutaron pruebas contra MySQL/Hostinger, pasarelas reales ni SMTP real. Capturas y JSON en docs/evidencias. No hay afirmación de certificación de seguridad ni pruebas de carga.

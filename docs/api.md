# API REST NexTech v2

Base: `https://tu-dominio/api.php`. Se usa PATH_INFO para funcionar en hosting PHP sin un framework. Ejemplo real: `GET /api.php/products?page=1&limit=20`. El cliente utiliza esta API; las rutas antiguas `?action=` se conservan por compatibilidad.

## Autenticación y errores
Sesión en cookie HttpOnly + SameSite=Lax. Secure cuando APP_URL es HTTPS. Primero consultar GET /bootstrap; enviar su csrf como X-CSRF-Token en toda escritura salvo webhooks firmados. Login/regeneración cambia el csrf. Cliente solo accede a sus recursos. Toda ruta /admin exige administrador activo en servidor. JSON UTF-8. Content-Type application/json en escrituras; multipart solo uploads.

Códigos: 200 éxito/actualización; 201 creación; 400 JSON mal formado; 401 sin sesión/credenciales inválidas; 403 CSRF o rol incorrecto; 404 recurso/ruta inexistente; 405 verbo no permitido; 409 duplicado, relaciones o conflicto de stock/estado; 422 validación; 429 límite de intentos; 500 error interno genérico; 503 instalación/integración no disponible. Respuesta de error: {"error":"Mensaje comprensible"}. Nunca devuelve SQL o trazas.

Paginación: page>=1, limit=1..100 (20 por defecto en administración; 24 en catálogo). Respuesta: {"items":[],"total":0,"page":1,"limit":20,"pages":0}. Búsqueda y orden son consultas parametrizadas con columnas de orden permitidas.

## 1. GET /api.php/bootstrap
- Función: bootstrap.
- Permiso: Público.
- Parámetros / cuerpo: Sin parámetros.
- Respuesta: user, csrf, settings, categories.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 2. GET /api.php/products
- Función: products.
- Permiso: Público.
- Parámetros / cuerpo: q, category, brand, stock, featured, offers, sort, page, limit (query).
- Respuesta: items, total, page, limit, pages, brands.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 3. GET /api.php/products/{id}
- Función: product.
- Permiso: Público.
- Parámetros / cuerpo: id entero positivo en ruta.
- Respuesta: Producto con specs, stock, precio y fuente.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 4. POST /api.php/auth/register
- Función: register.
- Permiso: Público.
- Parámetros / cuerpo: name (2-120), email, password (10-128), accept=true.
- Respuesta: user, csrf; 201.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 5. POST /api.php/auth/login
- Función: login.
- Permiso: Público.
- Parámetros / cuerpo: email, password.
- Respuesta: user, csrf; cookie de sesión.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 6. DELETE /api.php/auth/session
- Función: logout.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: Sin cuerpo requerido.
- Respuesta: user=null, csrf.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 7. POST /api.php/auth/forgot
- Función: forgot.
- Permiso: Público.
- Parámetros / cuerpo: email.
- Respuesta: message genérico; token se envía por correo.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 8. POST /api.php/auth/reset
- Función: reset.
- Permiso: Público.
- Parámetros / cuerpo: token (64), password (10-128).
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 9. PATCH /api.php/profile
- Función: profile.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: name, phone.
- Respuesta: user actualizado.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 10. PUT /api.php/profile/password
- Función: password.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: current_password, password.
- Respuesta: csrf renovado.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 11. GET /api.php/cart
- Función: cart.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: GET: sin cuerpo. PUT: id en ruta y qty (1-99).
- Respuesta: GET: productos y qty. PUT: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 12. PUT /api.php/cart/items/{id}
- Función: cart.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: GET: sin cuerpo. PUT: id en ruta y qty (1-99).
- Respuesta: GET: productos y qty. PUT: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 13. DELETE /api.php/cart/items/{id}
- Función: cart-remove.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id del producto en ruta.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 14. GET /api.php/favorites
- Función: favorites.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: Sin cuerpo.
- Respuesta: Array de productos guardados.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 15. POST /api.php/favorites
- Función: favorite.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id del producto.
- Respuesta: saved booleano; alterna favorito.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 16. GET /api.php/addresses
- Función: addresses.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: Sin cuerpo.
- Respuesta: Array de direcciones propias.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 17. POST /api.php/addresses
- Función: address.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: label, recipient, phone, street, colony, city, state, postal_code (5 dígitos), notes; id para PUT.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 18. PUT /api.php/addresses/{id}
- Función: address.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: label, recipient, phone, street, colony, city, state, postal_code (5 dígitos), notes; id para PUT.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 19. DELETE /api.php/addresses/{id}
- Función: address-delete.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id en ruta, solo propietario.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 20. GET /api.php/orders
- Función: orders.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: Sin cuerpo; historial personal hasta 100 pedidos.
- Respuesta: Array de pedidos con items y events.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 21. POST /api.php/orders
- Función: checkout.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: key (16-80), provider, accept=true, address={recipient,phone,street,colony,city,state,postal_code,notes}.
- Respuesta: Pedido, folio, total, net_total, tax, tax_rate_bp, items, events; 201 (200 en reintento idéntico).
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 22. GET /api.php/orders/{id}
- Función: order.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id del pedido en ruta, solo propietario.
- Respuesta: Pedido con detalle y dirección histórica.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 23. PATCH /api.php/orders/{id}/cancellation
- Función: cancel-order.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id en ruta; pendiente de transferencia/demo.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 24. POST /api.php/orders/{id}/payment
- Función: retry-payment.
- Permiso: Cliente autenticado.
- Parámetros / cuerpo: id del pedido en ruta.
- Respuesta: url de pago.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 25. GET /api.php/admin/dashboard
- Función: admin-dashboard.
- Permiso: Administrador.
- Parámetros / cuerpo: Sin parámetros.
- Respuesta: products, customers, orders, revenue, low_stock, settings.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 26. GET /api.php/admin/products
- Función: admin-products.
- Permiso: Administrador.
- Parámetros / cuerpo: q, category, active (0/1), sort (newest/name/name-desc/price-asc/price-desc/stock), page, limit.
- Respuesta: Listado paginado de productos.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 27. POST /api.php/admin/products
- Función: admin-product.
- Permiso: Administrador.
- Parámetros / cuerpo: sku,category,name,brand,description,specs(objeto),image,source_url,price,compare_price,stock,active,featured,verified,supplier_id(opcional); id en PUT.
- Respuesta: id; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 28. PUT /api.php/admin/products/{id}
- Función: admin-product.
- Permiso: Administrador.
- Parámetros / cuerpo: sku,category,name,brand,description,specs(objeto),image,source_url,price,compare_price,stock,active,featured,verified,supplier_id(opcional); id en PUT.
- Respuesta: id; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 29. DELETE /api.php/admin/products/{id}
- Función: admin-product-delete.
- Permiso: Administrador.
- Parámetros / cuerpo: id en ruta.
- Respuesta: ok=true; baja lógica.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 30. GET /api.php/admin/orders
- Función: admin-orders.
- Permiso: Administrador.
- Parámetros / cuerpo: q, status, sort(newest/oldest/total), page, limit.
- Respuesta: Listado paginado con cliente, items y events.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 31. PATCH /api.php/admin/orders/{id}
- Función: admin-order.
- Permiso: Administrador.
- Parámetros / cuerpo: id en ruta; status, tracking, reason (requerido para anular demo pagada).
- Respuesta: ok=true; guarda evento y movimiento si cancela.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 32. GET /api.php/admin/customers
- Función: admin-customers.
- Permiso: Administrador.
- Parámetros / cuerpo: Sin parámetros.
- Respuesta: Hasta 500 clientes con contador de pedidos; compatibilidad, usar /admin/users para administración paginada.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 33. GET /api.php/admin/audit
- Función: admin-audit.
- Permiso: Administrador.
- Parámetros / cuerpo: page, limit, sort(newest/oldest).
- Respuesta: Listado paginado de eventos y responsable.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 34. GET /api.php/admin/export
- Función: admin-export.
- Permiso: Administrador.
- Parámetros / cuerpo: Sin parámetros.
- Respuesta: CSV de pedidos; excepción al formato JSON.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 35. PATCH /api.php/admin/settings
- Función: admin-settings.
- Permiso: Administrador.
- Parámetros / cuerpo: Campos editables: store_name,support_email,support_phone,legal_name,legal_address,bank_instructions,privacy_text,terms_text,returns_text,shipping_price,free_shipping,tax_rate_bp,demo_mode.
- Respuesta: ok=true.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 36. POST /api.php/admin/uploads
- Función: upload.
- Permiso: Administrador.
- Parámetros / cuerpo: multipart/form-data; image JPG/PNG/WebP hasta 4 MB.
- Respuesta: url del archivo.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 37. GET /api.php/admin/categories
- Función: admin-categories.
- Permiso: Administrador.
- Parámetros / cuerpo: q,active (0/1),sort(newest/name/name-desc),page,limit.
- Respuesta: Listado paginado; sin hashes ni secretos.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 38. GET /api.php/admin/categories/{id}
- Función: admin-category.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: Recurso individual; 404 si no existe.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 39. POST /api.php/admin/categories
- Función: admin-category-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,slug (solo creación),icon,position (0-1000),active (0/1).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 40. PUT /api.php/admin/categories/{id}
- Función: admin-category-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,slug (solo creación),icon,position (0-1000),active (0/1).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 41. DELETE /api.php/admin/categories/{id}
- Función: admin-category-delete.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: ok=true; baja lógica con protección de dependencias y último administrador.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 42. GET /api.php/admin/suppliers
- Función: admin-suppliers.
- Permiso: Administrador.
- Parámetros / cuerpo: q,active (0/1),sort(newest/name/name-desc),page,limit.
- Respuesta: Listado paginado; sin hashes ni secretos.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 43. GET /api.php/admin/suppliers/{id}
- Función: admin-supplier.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: Recurso individual; 404 si no existe.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 44. POST /api.php/admin/suppliers
- Función: admin-supplier-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,email,phone,active (0/1).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 45. PUT /api.php/admin/suppliers/{id}
- Función: admin-supplier-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,email,phone,active (0/1).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 46. DELETE /api.php/admin/suppliers/{id}
- Función: admin-supplier-delete.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: ok=true; baja lógica con protección de dependencias y último administrador.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 47. GET /api.php/admin/users
- Función: admin-users.
- Permiso: Administrador.
- Parámetros / cuerpo: q,active (0/1),sort(newest/name/name-desc),page,limit.
- Respuesta: Listado paginado; sin hashes ni secretos.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 48. GET /api.php/admin/users/{id}
- Función: admin-user.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: Recurso individual; 404 si no existe.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 49. POST /api.php/admin/users
- Función: admin-user-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,email,phone,role (admin/customer),active (0/1),password (requerida al crear; opcional al editar).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 50. PUT /api.php/admin/users/{id}
- Función: admin-user-save.
- Permiso: Administrador.
- Parámetros / cuerpo: name,email,phone,role (admin/customer),active (0/1),password (requerida al crear; opcional al editar).
- Respuesta: id,ok=true; 201 al crear, 200 al actualizar.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 51. DELETE /api.php/admin/users/{id}
- Función: admin-user-delete.
- Permiso: Administrador.
- Parámetros / cuerpo: id o slug en ruta.
- Respuesta: ok=true; baja lógica con protección de dependencias y último administrador.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 52. GET /api.php/admin/roles
- Función: admin-roles.
- Permiso: Administrador.
- Parámetros / cuerpo: Sin parámetros.
- Respuesta: Array code,name; roles fijos del sistema.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 53. GET /api.php/admin/movements
- Función: admin-movements.
- Permiso: Administrador.
- Parámetros / cuerpo: page, limit, sort(newest/oldest).
- Respuesta: Listado paginado: producto, responsable, folio, cambio, antes/después.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 54. GET /api.php/admin/reports
- Función: admin-reports.
- Permiso: Administrador.
- Parámetros / cuerpo: start,end (YYYY-MM-DD); scope real/demo.
- Respuesta: sales, inventory, totals, fechas y notas; alimenta las 2 gráficas.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 55. POST /api.php/webhooks/stripe
- Función: webhook-stripe.
- Permiso: Proveedor con firma.
- Parámetros / cuerpo: JSON del proveedor + Stripe-Signature.
- Respuesta: Acuse tras verificar firma y pago contra Stripe.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## 56. POST /api.php/webhooks/mercadopago
- Función: webhook-mercadopago.
- Permiso: Proveedor con firma.
- Parámetros / cuerpo: Evento del proveedor + x-signature + x-request-id.
- Respuesta: Acuse tras verificar firma y consultar Mercado Pago.
- Códigos: 200, 201 cuando se indica; 400/401/403/404/405/409/422/429/500/503 según las reglas anteriores.

## Ejemplo de compra
```http
POST /api.php/orders
Content-Type: application/json
X-CSRF-Token: <csrf de bootstrap/login>

{"key":"pedido-cliente-unico-123","provider":"demo","accept":true,"address":{"recipient":"Cliente de prueba","phone":"5500000000","street":"Domicilio ficticio 123","colony":"Centro","city":"Ciudad de México","state":"Ciudad de México","postal_code":"06000","notes":"Prueba"}}
```
El carrito del usuario debe existir previamente. El servidor ignora totales aportados por el navegador. Consulta precios/stock y escribe venta, líneas, reserva, movimiento y auditoría en una sola transacción. El contacto con la pasarela ocurre después del commit y admite reintentos.
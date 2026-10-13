# Auditoría de requisitos

Alcance: requisitos generales y POS (proyectos 1/4); proveedores también cubre un diferenciador del POS personalizado.

| N.º | Requisito | Estado | Evidencia |
|---|---|---|---|
| 1 | Arquitectura mínima | Cumple localmente | public_html → API → private → PDO |
| 2 | Cliente-servidor | Cumple localmente | Capas y diagrama de sección 7 |
| 3 | Base de datos e integridad | Cumple localmente | 18 tablas / FK / CHECK / specs normalizadas |
| 4 | Modelo / SQL / datos | Cumple localmente | SQL, diccionario, ER y demo.php |
| 5 | API REST | Cumple localmente | rest.php: 56 rutas; frontend las consume |
| 6 | Formato JSON | Cumple localmente | JSON; CSV/multipart como excepciones |
| 7 | Códigos HTTP | Cumple localmente | CP-03/14/15/22; errores diferenciados |
| 8 | Tres CRUD | Cumple localmente | Productos, categorías, proveedores, usuarios |
| 9 | Autenticación | Cumple localmente | CP-03/12/23 |
| 10 | Roles y permisos | Cumple localmente | roles + require_user(true); CP-16 |
| 11 | Autorización de endpoints | Cumple localmente | Cliente aislado; CP-04/08/14 |
| 12 | Dashboard | Cumple localmente | Resumen con BD, gráficas y stock bajo |
| 13 | Buscador | Cumple localmente | CP-02/14; laptops: 12 |
| 14 | Filtros | Cumple localmente | Categoría, marca, estado y fechas |
| 15 | Ordenamiento | Cumple localmente | Catálogo y tablas administrativas |
| 16 | Paginación | Cumple localmente | Limit/page/total/pages; CP-18 |
| 17 | Validaciones | Cumple localmente | Servidor + formularios; CP-06/14 |
| 18 | Errores comprensibles | Cumple localmente | 400/422/409/500 sin SQL; CP-22 |
| 19 | Reglas de negocio | Cumple localmente | 12 RN de sección 6 |
| 20 | Transacciones | Cumple localmente | Checkout/rollback; CP-07/21 |
| 21 | Historial | Cumple localmente | audit/order_events/inventory_movements |
| 22 | Baja lógica | Cumple localmente | active en cuatro módulos |
| 23 | Dos reportes | Cumple localmente | Ventas por periodo e inventario actual |
| 24 | Dos gráficas | Cumple localmente | Ventas diarias / unidades por categoría |
| 25 | Rangos de fechas | Cumple localmente | start/end y validación; CP-20 |
| 26 | Diseño responsivo | Cumple localmente | UI-05: 390/768/escritorio |
| 27 | Navegación | Cumple localmente | Cliente y administrador con menús |
| 28 | Estados de interfaz | Cumple localmente | Carga, error, vacío y éxito |
| 29 | Confirmaciones | Cumple localmente | Baja y cambio de estado confirmados |
| 30 | Seguridad básica | Cumple localmente | Hash, sesiones, CSRF, PDO, RBAC |
| 31 | Variables .env | Cumple localmente | .env.example / environment.php / .gitignore |
| 32 | Documentación API | Cumple localmente | docs/api.md y 56 contratos |
| 33 | Git progresivo y colaborativo | Pendiente de evidencia | Sin evidencia anterior; commits de esta revisión |
| 34 | README | Documentado localmente | README técnico, datos académicos y enlaces proporcionados |
| 35 | Estructura del proyecto | Cumple localmente | public_html / private / database / docs / test |

No se acredita Git previo/colaborativo ni planeación original. Portada completada con datos y enlaces proporcionados por el equipo; dominio previsto, con despliegue pendiente. MySQL/Hostinger, SMTP y pasarelas reales requieren validación en destino.

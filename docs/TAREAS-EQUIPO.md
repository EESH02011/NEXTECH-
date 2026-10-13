# Trabajo propuesto para los seis integrantes

Estado inicial de todas las tareas: **por realizar y revisar**. Reparto propuesto, ajustable según el acuerdo del equipo. Cada persona realiza su tarea desde `main` y documenta resultados reales; la lista no supone que todos estos puntos tengan errores.

| ID | Responsable propuesto | Rama sugerida | Trabajo verificable | Criterio de cierre y documentación | Revisión propuesta |
|---|---|---|---|---|---|
| EQ01 | Soto Saavedra Erwin Emmanuel | `erwin/sesiones-permisos` | Revisar login/logout, sesión expirada, cliente que consulta pedido ajeno y acceso directo a administración. Reforzar las pruebas o corregir fallos encontrados. | Resultados HTTP esperados/obtenidos, prueba reproducible y explicación del control servidor en `docs/evidencias/sesiones-permisos.md`. | Itzel Daniela |
| EQ02 | Aguirre Sierra Juan Damián | `juan/mysql-transacciones` | Instalar SQL en una base MySQL de ensayo y comprobar venta, rollback y última unidad con dos clientes. Corregir incompatibilidades reales. | Script o prueba reproducible, comparación de stock/pedidos y versiones del motor en `docs/evidencias/mysql-transacciones.md`. | Kevin Yahir |
| EQ03 | Crisanto Vázquez Sofía | `sofia/revision-movil` | Revisar catálogo, carrito, formularios y tablas a 390, 768 y 1440 px, además de navegación con teclado. Corregir problemas observados. | Capturas antes/después, pasos y resultado en `docs/evidencias/revision-movil.md`; cambios CSS/JS solo cuando hagan falta. | Amairani |
| EQ04 | Rosales Alarcón Amairani | `amairani/reportes-fechas` | Comprobar reportes y dos gráficas con ventas de fechas conocidas, rango vacío e invertido. Añadir casos faltantes o corregir discrepancias. | Totales API/BD cotejados y explicación de límites de fechas en `docs/evidencias/reportes-fechas.md`; actualizar documentación de reportes. | Juan Damián |
| EQ05 | Gutiérrez Rocha Kevin Yahir | `kevin/instalacion-hostinger` | Ensayar instalación en hosting o entorno equivalente autorizado, configuración fuera de la carpeta pública y restauración en una base separada. Automatizar o corregir pasos repetibles. | Instrucciones reproducibles, entorno usado y resultado en `docs/evidencias/instalacion-hostinger.md`, sin claves o datos privados. | Erwin Emmanuel |
| EQ06 | Rivera Alvarado Itzel Daniela | `itzel/regresion-trazabilidad` | Ejecutar la batería disponible, agregar un caso significativo que falte y reproducir un recorrido completo cliente/API/BD. Registrar y volver a probar defectos. | Caso automatizado útil, resultado fechado y relación requisito/prueba/commit en `docs/evidencias/regresion-trazabilidad.md`. | Sofía |

## Al cerrar cada tarea

1. Enlazar issue, commit y pull request.
2. Identificar quién realizó el cambio y quién lo revisó.
3. Registrar qué se probó, cuándo y sobre qué versión; no copiar resultados de otra instalación como propios.
4. Documentar limitaciones y pendientes si no se pudo completar el ensayo.
5. Actualizar la planeación y la tabla de contribuciones solamente con evidencia ya generada.

No basta cambiar un nombre, mover archivos o volver a agregar una función terminada. Una revisión documentada, una prueba útil o una corrección comprobable sí dejan evidencia concreta del trabajo nuevo.

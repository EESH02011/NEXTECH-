# Crear la base de datos
Recomendado: crear una base vacía en Hostinger, configurar .env y abrir install.php. El instalador crea tablas, roles y catálogo, y permite elegir el primer administrador.

Alternativa SQL: importar schema.sql y luego seed.sql en una base VACÍA de MySQL 8.0.16+ / MariaDB 10.6+. No importar dos veces. Después abrir install.php para crear el administrador; el catálogo existente se conserva. El SQL no crea cuentas con contraseñas predeterminadas.

Para un escenario académico local: DB_DRIVER=sqlite, APP_ENV=development, DB_PATH=ruta_absoluta_nueva; ejecutar php scripts/demo.php. Crea dos cuentas con contraseñas aleatorias y tres pedidos sintéticos. Se niega a modificar una base con usuarios. Las credenciales se guardan fuera de la carpeta pública. No ejecutar estos datos en producción.

El diccionario, relaciones y normalización están en docs/database.md y docs/diagrams/entidad-relacion.mmd. No se incluye una base de clientes reales. scripts/upgrade-sqlite.php migra la versión local anterior con respaldo; no es una migración MySQL de una tienda ya operativa.

# Versiones y comparación de NexTech

Organización preparada el 13 de octubre de 2026.

| Rama | Contenido | Origen |
|---|---|---|
| `version-preliminar` | Primera versión que quedó registrada: tienda, catálogo, cuentas, carrito, pedidos y administración inicial. | Commit `efa5eaa`, registrado el 6 de octubre de 2026. |
| `main` | Tienda actual con mejoras académicas, documentos y organización del equipo. | Conserva la secuencia real de commits anteriores y añade la preparación actual. |

La versión preliminar ya es una tienda avanzada. No representa un boceto del 24 de septiembre ni una reconstrucción artificial de todo el desarrollo. Su fecha de registro y autor original se conservan.

## Cambios principales ya registrados entre ambas versiones

- Rutas REST con métodos y respuestas diferenciados.
- CRUD administrativos de categorías, proveedores y usuarios, además de productos.
- Integridad relacional y normalización de especificaciones.
- Movimientos de inventario, reportes y gráficas.
- Configuración mediante variables de entorno.
- Ampliación de pruebas, contratos de API, modelo de datos y evidencias.
- Datos académicos, documentación y enlaces proporcionados por el equipo.

Los commits existentes están atribuidos a NexTech automation. Las ramas no cambian esa autoría ni acreditan contribuciones previas de los seis integrantes. Sus nuevas aportaciones deberán quedar registradas a partir del trabajo que efectivamente realicen.

## Comparar

Después de publicar ambas ramas, abrir:

https://github.com/EESH02011/NEXTECH-/compare/version-preliminar...main

O, desde una copia local con ambas referencias disponibles:

```sh
git log --oneline --graph --all
git diff --stat origin/version-preliminar origin/main
git diff origin/version-preliminar origin/main -- private/admin.php
```

## Abrir la preliminar por separado

Desde una carpeta que no contenga ya `nextech-preliminar`:

```sh
git clone --branch version-preliminar https://github.com/EESH02011/NEXTECH-.git nextech-preliminar
```

Leer el README de esa versión y usar una base independiente. Para revisar diferencias no hace falta ejecutar ni instalar la preliminar. Los ZIP de código no incluyen el historial: el repositorio Git y el archivo bundle conservan los commits.

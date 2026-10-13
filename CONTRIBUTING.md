# Trabajar en equipo en NexTech

Repositorio: https://github.com/EESH02011/NEXTECH-

La rama `main` contiene la versión actual. `version-preliminar` conserva la primera versión registrada, sin alterar sus fechas ni autores. Las nuevas tareas se realizan desde `main`.

## Una sola vez por integrante

El propietario agrega las cuentas del equipo como colaboradores del repositorio. Cada integrante acepta su invitación y configura su propia identidad de Git. Sustituir los textos de ejemplo antes de ejecutar:

```sh
git clone https://github.com/EESH02011/NEXTECH-.git
cd NEXTECH-
git config user.name "Tu nombre completo"
git config user.email "Tu correo verificado en GitHub o tu correo noreply de GitHub"
```

No usar la identidad de otra persona ni compartir contraseñas o tokens. La identidad se configura solo para esta copia del proyecto. El inicio de sesión en GitHub es independiente del nombre y correo de los commits.

## Para cada tarea

1. Elegir una tarea de `docs/TAREAS-EQUIPO.md` y acordar quién la revisará. Abrir un issue con el resultado esperado.
2. Con el árbol de trabajo limpio, actualizar la rama principal y crear una rama propia. Este ejemplo corresponde a una tarea de diseño móvil:

```sh
git switch main
git pull --ff-only origin main
git switch -c sofia/revision-movil
```

3. Realizar el trabajo: revisar, reproducir, implementar o corregir; documentar las pruebas. No cambiar código solo para aumentar el número de commits.
4. Revisar qué se va a guardar, añadir únicamente archivos relacionados y crear un commit con un mensaje que describa el cambio real:

```sh
git status
git diff
git add public_html/style.css docs/evidencias/revision-movil.md
git diff --cached
git commit -m "fix: corrige el desbordamiento observado en la tabla móvil"
git push -u origin sofia/revision-movil
```

Los nombres de archivos, rama y mensaje anteriores son ejemplos: deben coincidir con el trabajo realizado. Si no se encontró un error, documentar el resultado observado y añadir una prueba útil cuando corresponda.

5. Abrir en GitHub una pull request desde la rama de trabajo hacia `main`. Completar la plantilla y relacionar el issue. Otro integrante revisa cambios y evidencia antes de integrar.
6. Preferir un merge que conserve los commits individuales para facilitar la revisión académica. Nunca usar push forzado para resolver un conflicto; revisar el conflicto y pedir apoyo.

## Evidencia mínima por tarea

- Problema o necesidad y criterio de aceptación.
- Archivos y comportamiento modificados, con explicación del autor.
- Fecha de prueba, versión probada, resultado esperado y observado.
- Captura o log sin datos privados y enlace al commit/pull request.
- Documentación del módulo actualizada y revisión de otra persona.

## Ejecutar y probar

Seguir el README para PHP y una base local independiente. La batería existente se ejecuta con `node test/integration.mjs` teniendo PHP disponible o configurando `PHP_BINARY`. La prueba crea su propia base temporal; no debe apuntarse a una base comercial.

Para GitHub no se incluyen `.env`, `private/config.php`, `private/setup.key`, datos de `private/storage/`, respaldos ni archivos de clientes. Si se instala desde Git o desde el ZIP de código, generar la clave privada mediante `php scripts/setup-key.php`; nunca agregarla a un commit.

## Versión preliminar

Consultar `docs/VERSIONES.md`. No usar la versión anterior sobre una base de datos actual o comercial. Para explorarla, usar una carpeta y una base distintas; no es necesario bajar de versión para colaborar.

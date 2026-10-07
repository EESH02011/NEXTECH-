<?php
// Compatibilidad con herramientas que cargan config.php explícitamente.
// Las credenciales se leen de .env, fuera de public_html.
require_once __DIR__.'/environment.php';
return environment_config();

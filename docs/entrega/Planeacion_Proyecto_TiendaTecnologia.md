# Planeación del proyecto final - NexTech
Instituto Tecnológico Superior de Irapuato
Materia: Desarrollo de Apps Web
Docente: Francisco Hunahpu Sahagun González
Grupo: Ing. Informática
Inicio: 24/09/2026
Entrega: 06/11/2026
Versión 1.0: 10/10/2026
Repositorio: https://github.com/EESH02011/NEXTECH-
Sitio previsto: https://mamawevo.com/ (pendiente de despliegue)

Integrantes: Soto Saavedra Erwin Emmanuel; Aguirre Sierra Juan Damián; Crisanto Vázquez Sofía; Rosales Alarcón Amairani; Gutiérrez Rocha Kevin Yahir; Rivera Alvarado Itzel Daniela

# Índice y control del documento

## Uso y control de esta planeación

Este plan organiza la revisión, los ajustes, la integración y la entrega académica de NexTech. El periodo solicitado inicia el 24/09/2026 y termina el 06/11/2026. La versión 1.0 se elabora el 10/10/2026: las fechas anteriores constituyen una reconstrucción del plan, no evidencia de ejecución ni de acuerdos históricos del equipo.

Las responsabilidades son una propuesta de reparto para validación del equipo. Las evidencias descritas son esperadas; no se presentan como actividades terminadas ni como contribuciones personales ya comprobadas. La documentación técnica existente servirá de base y deberá contrastarse con el sistema al realizar cada revisión.

Tabla 1. Registro inicial de versiones
| Versión / fecha | Contenido | Revisión prevista |
| --- | --- | --- |
| 1.0 / 10/10/2026 | Plan integrado conforme a la actividad, requisitos generales, POS y lineamientos; repositorio y dominio proporcionados por el equipo. | Equipo completo; E coordina la validación. |
| Siguiente versión / al aprobar cambios | Conservar fechas originales y registrar fecha revisada, motivo, responsable e impacto. | Revisor de la actividad y coordinación. |

# 1. Presentación y alcance del proyecto

## Problema y objetivo

NexTech aborda el escenario académico de una tienda de tecnología que necesita centralizar catálogo, clientes, ventas e inventario. Los registros dispersos pueden provocar ventas sin existencias, totales incorrectos y pérdida del historial. Los usuarios principales son clientes que consultan y compran, y administradores que gestionan productos, cuentas, inventario y reportes.

Objetivo general: consolidar y entregar, el 6 de noviembre de 2026, un punto de venta web con persistencia relacional, API REST, permisos y venta transaccional, acompañado de documentación, pruebas reproducibles e historial de participación auténtico del equipo.

Tabla 2. Alcance y resultados verificables
| Área | Compromiso de la versión final |
| --- | --- |
| Catálogo y navegación | Conservar el catálogo inicial de 300 modelos en 15 categorías; verificar búsquedas por nombre, marca y SKU, acceso a laptops, filtros, ordenamiento y paginación. |
| Cuentas y administración | Registro, login/logout, perfil, clientes y roles administrador/cliente. Cuatro CRUD: productos, categorías, proveedores y usuarios; baja lógica. |
| Proceso de venta | Carrito, cantidades, subtotal, impuesto configurado, total, folio, detalle, descuento transaccional de stock y movimiento; historial y cancelación autorizada. |
| Información del negocio | Dashboard, alerta de stock bajo, reportes de ventas por fechas e inventario; dos gráficas alimentadas por API/BD. |
| Entrega académica | SQL y datos de prueba, README, API documentada, PDF de 16 apartados, evidencia Git por integrante y demostración. |

## Exclusiones y restricciones

Quedan fuera CFDI, múltiples sucursales, arqueo de caja, logística automatizada y reembolsos externos automáticos. Estas exclusiones no eliminan ningún requisito obligatorio del POS: se mantiene la cancelación autorizada y la compensación del inventario. El cobro real requiere cuentas y configuración de proveedores; la demostración utilizará pagos de prueba claramente identificados.

Restricciones: fecha final fija, disponibilidad parcial de seis estudiantes y acceso a Hostinger/MySQL aún por comprobar. Se reserva tiempo de corrección y de aprendizaje de PHP, SQL, sesiones y Git. No se supone que los integrantes tengan experiencia ya acreditada. El proveedor de hosting, dominio y correo se usarán según la cuenta disponible, sin compras presupuestadas ni accesos inventados.

# 1.1 Equipo y participación propuesta

Tabla 3. Integrantes y responsabilidad principal
| Clave / integrante | Desarrollo propuesto | Actividades | Documentación a su cargo |
| --- | --- | --- | --- |
| E; Soto Saavedra Erwin Emmanuel | Coordinación, arquitectura e identidad | A01, A03, A06, A09, A14, A21, A22 | Alcance, arquitectura, autenticación y bitácora de cambios. |
| J; Aguirre Sierra Juan Damián | Datos y proceso de venta | A05, A07, A11, A16 | ER, diccionario, SQL, transacciones y correcciones. |
| S; Crisanto Vázquez Sofía | Experiencia de usuario e interfaz | A04, A10, A20 | Wireframes, navegación, capturas y comparación con interfaz final. |
| A; Rosales Alarcón Amairani | Reportes y documentación | A13, A19 | Reportes, criterios de aceptación e integración del PDF. |
| K; Gutiérrez Rocha Kevin Yahir | Servicios, inventario y despliegue | A02, A08, A12, A17 | API de sus módulos, seguridad de configuración, instalación y respaldo. |
| I; Rivera Alvarado Itzel Daniela | Pruebas y trazabilidad | A15, A18 | Casos, evidencias, matriz de trazabilidad y manual de prueba. |

Las claves E, J, S, A, K e I se utilizan en las matrices para mantenerlas legibles. Cada integrante deberá realizar cambios reales en código o pruebas y actualizar la documentación de su trabajo. La persona de apoyo revisa el entregable; nadie valida en solitario una operación crítica.

La participación de I en desarrollo incluye pruebas automatizadas e integración; la de A incluye las consultas y la interfaz de reportes. La documentación no se asigna exclusivamente a una persona: cada responsable entrega texto, contrato API, captura o evidencia de su módulo; A integra y verifica consistencia.

## Objetivos de aceptación

Se verificará una base con al menos 6 tablas relacionadas (el diseño disponible contempla 18), al menos 3 CRUD (se plantean 4), al menos 12 endpoints funcionales (el inventario previo documenta 56), 2 roles, 1 dashboard, 5 reglas de negocio como mínimo, 1 transacción crítica, 2 reportes y 2 gráficas dinámicas. Estas cantidades se comprobarán de nuevo sobre la versión final.

# 2. Requisitos y entregables

RG01-RG35 corresponden a los numerales del Markdown base. Las actividades de la sección 3 indican el trabajo requerido; la columna final es evidencia esperada, aún sujeta a ejecución y revisión. Un intervalo como A07-A09 comprende las tres actividades.

Tabla 4. Trazabilidad de requisitos generales 1/3
| ID / requisito | Módulo | Actividad / responsable | Evidencia y aceptación esperadas |
| --- | --- | --- | --- |
| RG01; Arquitectura mínima | Capas | A03; E | Diagrama usuario/interfaz/API/backend/BD; sin acceso directo a BD. |
| RG02; Cliente-servidor | Capas | A03, A14; E | Recorrido HTTP; reglas y validación críticas ejecutadas en servidor. |
| RG03; BD real, integridad y 3FN | Datos | A05; J | Revisión de dependencias, PK/FK, UNIQUE, CHECK, índices y tipos; >=6 tablas. |
| RG04; Modelo y scripts | Datos | A05; J | ER, modelo lógico, diccionario, schema.sql y seed.sql; creación limpia. |
| RG05; API REST >=12 rutas | API | A03, A14; E | Contratos y prueba de métodos GET/POST/PUT/PATCH/DELETE según operación. |
| RG06; JSON | API | A14; E | Peticiones/respuestas JSON comprobadas; excepciones CSV/multipart documentadas. |
| RG07; Códigos HTTP | API | A14, A15; E | Casos 200, 201, 400, 401, 403, 404, 409, 422 y 500 controlado. |
| RG08; >=3 CRUD principales | Administración | A07-A09; J | Productos, categorías, proveedores y usuarios: alta, lista, edición y baja. |
| RG09; Autenticación | Cuentas | A06; E | Login/logout, hash de contraseña y sesión invalidada tras salir. |
| RG10; >=2 roles | Cuentas | A06, A09; E | Matriz de permisos administrador/cliente y pruebas directas a la API. |
| RG11; Autorización endpoints | Seguridad | A06, A15; E | Rechazo a cliente sin permiso y aislamiento de datos ajenos. |
| RG12; Dashboard | Reportes | A13; A | Indicadores, contadores, tablas y alertas recalculados desde BD. |

# 2.1 Matriz general (continuación)

Tabla 5. Trazabilidad de requisitos generales 2/3
| ID / requisito | Módulo | Actividad / responsable | Evidencia y aceptación esperadas |
| --- | --- | --- | --- |
| RG13; Buscador dinámico | Catálogo | A10; S | Buscar nombre, SKU o marca; laptop disponible en sección superior. |
| RG14; Filtros | Catálogo/reportes | A10, A13; S | Categoría/estado/fechas filtran registros reales y combinan con búsqueda. |
| RG15; Ordenamiento | Listados | A10; S | Nombre, precio o fecha en ambos sentidos; orden permitido por servidor. |
| RG16; Paginación | Listados | A10; S | Página/límite/total coherentes; registros sin repeticiones ni pérdida. |
| RG17; Validación en ambas capas | Formularios/API | A07-A11, A15; I | Vacíos, correo, duplicados, precio/cantidad y límites rechazados en backend. |
| RG18; Manejo de errores | API/interfaz | A14, A15; I | Mensajes legibles ante red/BD/servidor; no mostrar SQL ni secretos. |
| RG19; >=5 reglas de negocio | Ventas | A11, A12; J | RN01-RN08, con casos positivos y negativos definidos en sección 7. |
| RG20; Transacción | Ventas/inventario | A11; J | Venta + detalle + stock + movimiento se confirman o revierten juntos. |
| RG21; Historial | Auditoría | A12; K | Fecha, actor, operación, registro y estados consultables. |
| RG22; Baja lógica | Administración | A07-A09; K | Inactivo no ofertado; pedidos e historial anteriores preservados. |
| RG23; >=2 reportes | Reportes | A13; A | Ventas por periodo e inventario; totales cotejados con consultas SQL. |
| RG24; >=2 gráficas dinámicas | Reportes | A13; A | Ventas diarias y unidades por categoría; datos desde API, no constantes. |

# 2.1 Matriz general (continuación)

Tabla 6. Trazabilidad de requisitos generales 3/3
| ID / requisito | Módulo | Actividad / responsable | Evidencia y aceptación esperadas |
| --- | --- | --- | --- |
| RG25; Rango de fechas | Reportes | A13; A | Inicio/fin incluidos; rango invertido inválido y periodo vacío visible. |
| RG26; Diseño responsivo | Interfaz | A10, A15; S | Laptop, PC y tablet; además móvil. Capturas 1440, 1024, 768 y 390 px. |
| RG27; Navegación coherente | Interfaz | A04, A10; S | Acceso a login, dashboard, módulos, historial, reportes, perfil y ajustes. |
| RG28; Estados de interfaz | Interfaz | A10, A14; S | Carga, vacío, error, éxito y confirmación sin pantalla en blanco. |
| RG29; Confirmaciones | Venta/administración | A10, A12; S | Confirmar venta, cancelación y baja antes de operar. |
| RG30; Seguridad básica | Todo el sistema | A06, A15; I | Hash, PDO parametrizado, sesiones, CSRF, permisos y salida segura. |
| RG31; Variables de entorno | Configuración | A02, A17; K | .env fuera de Git y zona pública; .env.example sin secretos. |
| RG32; Documentación de API | Documentación | A03, A19; A | Método, URL, parámetros, body, respuesta, errores y permiso por ruta. |
| RG33; Git progresivo y equipo | Colaboración | A02, A22; E | Commits auténticos por integrante, revisiones y enlaces a tareas; sin fechas inventadas. |
| RG34; README completo | Documentación | A19, A20; A | Objetivo, equipo, tecnologías, instalación, BD, .env, ejecución, demo, rutas y capturas. |
| RG35; Estructura del proyecto | Organización | A02, A03; K | public_html, private, database, docs y test; responsabilidades separadas. |

# 2.2 Requisitos específicos del POS

Tabla 7. Funciones obligatorias del POS y diferenciador
| ID / función | Actividad / responsable | Evidencia esperada |
| --- | --- | --- |
| POS01; Usuarios, roles y clientes | A06, A09 / E | Cuentas, perfil y permisos; cliente consulta solo su información. |
| POS02; Productos, categorías e inventario | A07, A08, A12 / J, K | Catálogo relacionado, existencias y movimientos verificables. |
| POS03; Ventas y detalle | A11 / J | Folio, usuario, líneas, cantidades y precios persistentes. |
| POS04; Carrito | A10 / S | Agregar productos, modificar cantidades y retirar líneas. |
| POS05; Importes | A11 / J | Subtotal, impuesto y total recalculados en backend; importes del navegador ignorados. |
| POS06; Registro y folio único | A11 / J | Una venta confirmada obtiene un folio no duplicado y detalle consultable. |
| POS07; Actualización de stock | A11 / J | Descuento asociado a venta sin stock negativo ni venta parcial. |
| POS08; Historial y ventas anteriores | A12 / K | Lista/detalle por cliente y vista administrativa con fechas y estados. |
| POS09; Cancelación con permisos | A12 / K | Permiso especial, motivo, cambio de estado y reposición única de stock. |
| POS10; Dashboard, reportes y alertas | A12, A13 / K, A | Stock bajo visible, ventas por fecha e inventario con gráficas dinámicas. |
| POS11; Transacción crítica | A11, A15 / J, I | Crear venta + detalle + descontar inventario + movimiento; rollback ante cualquier fallo. |
| POS12; Diferenciador de tienda | A08 / K | Proveedores relacionados con productos; cubre la ampliación de POS personalizado si aplica. |

Las funciones de POS de los proyectos 1 y 4 se contemplan completas. La identidad NexTech y el directorio de proveedores añaden contexto de tienda de tecnología; no se presume un número de proyecto o equipo asignado por el docente.

# 2.3 Entregables de documentación final

A19 integra los 16 apartados obligatorios del PDF final. Esta planeación es un documento separado, con siete apartados conforme a la actividad extra; no sustituye la documentación técnica final. Cada responsable aportará evidencia esperada de su área.

Tabla 8. Cobertura de los 16 apartados
| ID / apartado | Responsable | Entregable esperado |
| --- | --- | --- |
| D01; Portada y control | E | Datos, versión, fecha, enlaces y cambios. |
| D02; Resumen ejecutivo | A | Problema, solución y resultados verificados al cierre. |
| D03; Problema, objetivos y alcance | E | Objetivos medibles, exclusiones y restricciones. |
| D04; Modelo de negocio | A | Usuarios, valor, proceso, recursos, costos e indicadores. |
| D05; Planeación y cronograma | E | Este plan, Gantt y comparación fechas previstas/reales. |
| D06; Requerimientos | I | RF/RNF, reglas y criterios; trazabilidad a pruebas. |
| D07; Arquitectura | E | Diagrama, justificación y operación de punta a punta. |
| D08; UX/UI | S | Seis bocetos, navegación y comparación con interfaz. |
| D09; Base de datos | J | ER, modelo lógico, 3FN, diccionario y SQL. |
| D10; API | K | Métodos, rutas, entradas, JSON, errores y permisos. |
| D11; Módulos y desarrollo | Todos; A integra | Objetivo, actores, entradas, lógica, BD, endpoints y capturas. |
| D12; Seguridad | E | Hash, sesiones, autorización, PDO y entorno. |
| D13; Pruebas | I | Esperado/obtenido/estado, capturas y defectos corregidos. |
| D14; Instalación y despliegue | K | Versiones, pasos, semilla, usuarios y respaldo. |
| D15; Resultados y conclusiones | Todos; A integra | Cumplimiento real, límites y reflexión de cada integrante. |
| D16; Anexos | I | ER completo, API, Git, manual y matriz de trazabilidad. |

# 3. Actividades y responsabilidades

Las horas son esfuerzo total de la actividad, sumando responsable y apoyo; no representan días completos de dedicación. Las fechas son inclusivas y se muestran en la sección 4. Dependencia significa terminar la actividad precedente, salvo la documentación continua, que se actualiza a lo largo del desarrollo.

Tabla 9. Desglose verificable 1/3
| Actividad y descripción | Principal / apoyo / horas | Depende de | Entregable y criterio de cierre |
| --- | --- | --- | --- |
| A01 · Análisis; Revisar alcance y numerar RF/RNF, POS y criterios. | E / S; 6 h | Inicio | Matriz sin requisitos obligatorios omitidos; alcance revisado. |
| A02 · Git y organización; Preparar repositorio compartido, tablero y reglas de colaboración. | K / J; 6 h | Inicio | Acceso de seis integrantes, .gitignore y primer registro auténtico; URL anotada. |
| A03 · Arquitectura; Trazar capas y contratos de la API para la venta. | E / J; 8 h | A01 | Diagrama y secuencia HTTP/API/PDO; sin acceso directo del navegador a BD. |
| A04 · Diseño UX/UI; Revisar seis wireframes y navegación cliente/administrador. | S / A; 12 h | A01 | Login, dashboard, catálogo, formulario, detalle y reportes; contraste y tamaños revisados. |
| A05 · Base de datos; Revisar ER, 3FN, restricciones y scripts SQL reproducibles. | J / I; 12 h | A03 | Base nueva con >=6 tablas relacionadas; ER, diccionario, schema y seed coherentes. |
| A06 · Autenticación; Revisar registro, sesiones, hash y permisos de API. | E / K; 10 h | A05 | Cliente y administrador aislados; login/logout y rechazos 401/403 reproducibles. |
| A07 · CRUD productos; Ajustar altas, consultas, edición y baja lógica de productos. | J / S; 12 h | A05 | SKU único, precio/stock válidos y producto histórico conservado. |
| A08 · CRUD categorías/proveedores; Ajustar categorías y proveedores relacionados con productos. | K / A; 10 h | A05 | Dos CRUD completos; no romper FK ni listar inactivos como disponibles. |

# 3.1 Desglose de actividades (continuación)

Tabla 10. Desglose verificable 2/3
| Actividad y descripción | Principal / apoyo / horas | Depende de | Entregable y criterio de cierre |
| --- | --- | --- | --- |
| A09 · Usuarios y perfiles; Revisar administración de usuarios, perfil y propiedad de datos. | E / I; 8 h | A06 | Cliente solo modifica lo propio; administración restringida y bajas preservan historial. |
| A10 · Frontend de compra; Integrar catálogo, búsqueda de laptops, filtros y carrito. | S / A; 14 h | A04, A06, A07, A08 | Agregar, cambiar y retirar; ordenar/paginar; estados vacío/error/carga y vista móvil. |
| A11 · Venta transaccional; Revisar venta, detalle, totales, folio y descuento de stock en una transacción. | J / K; 14 h | A06, A07, A08 | COMMIT consistente; fallo intermedio revierte venta, detalle, stock y movimiento. |
| A12 · Historial e inventario; Ajustar seguimiento, cancelación autorizada y movimientos. | K / I; 10 h | A11 | Motivo/actor/fecha registrados; cancelación repone stock una sola vez; alerta de stock bajo. |
| A13 · Dashboard y reportes; Integrar ventas por periodo e inventario con dos gráficas. | A / J; 12 h | A11 | Totales y gráficas coinciden con BD; rango inválido rechazado y vacío representado. |
| A14 · Integración completa; Conectar recorridos cliente/administrador y revisar errores HTTP. | E / S; 12 h | A09, A10, A12, A13 | Compra completa y gestión administrativa desde interfaz; JSON y códigos correctos. |
| A15 · Pruebas sistemáticas; Ejecutar casos funcionales, negativos, seguridad, concurrencia y responsividad. | I / K; 14 h | A14 | Matriz con obtenido/esperado, capturas, logs y defectos priorizados; sin resultados supuestos. |
| A16 · Correcciones e integración; Corregir defectos y repetir casos fallidos sobre la versión integrada. | J / S; 12 h | A15 | Sin defectos críticos abiertos; commits vinculados a pruebas repetidas. |

# 3.1 Desglose de actividades (continuación)

Tabla 11. Desglose verificable 3/3
| Actividad y descripción | Principal / apoyo / horas | Depende de | Entregable y criterio de cierre |
| --- | --- | --- | --- |
| A17 · Despliegue de ensayo; Instalar en Hostinger/MySQL, configurar HTTPS y ensayar respaldo/restauración. | K / E; 10 h | A16 | Instalación limpia, .env privado y recorrido funcional en destino; bitácora de despliegue. |
| A18 · Regresión y aceptación; Repetir pruebas críticas en destino y ensayar demostración. | I / A; 12 h | A17 | Ningún fallo crítico; enlaces, roles y datos de prueba comprobados; acta de revisión. |
| A19 · Documentación continua; Integrar los 16 apartados del PDF, README, API y evidencias por módulo. | A / I; 18 h | A01, A02 | Cada cambio funcional tiene documentación; cierre con índice, tablas y capturas legibles. |
| A20 · Cierre de entrega; Revisar paquete, vínculos, PDF final y material de exposición. | S / I; 6 h | A18, A19 | Checklist completa; descarga e instalación del paquete verificadas por otro integrante. |
| A21 · Entrega y demostración; Entregar archivos en Teams y demostrar el proceso principal. | E / A; 4 h | A20 | Acuse de entrega y versión identificada; cada integrante explica su contribución real. |
| A22 · Seguimiento y cambios; Revisar tablero, bloqueos, carga y cambios en cada reunión. | E / I; 12 h | Inicio | Actas breves; fechas originales preservadas y reprogramaciones justificadas. |

Esfuerzo previsto: 234 horas-persona. Se añade una reserva de 24 horas-persona para imprevistos, para un presupuesto de 258 horas-persona. La capacidad supuesta es de 12 horas por semana por integrante durante seis semanas equivalentes: 432 horas-persona; debe validarse con la disponibilidad real del equipo.

Tabla 12. Carga orientativa por integrante (70% principal / 30% apoyo)
| E | J | S | A | K | I |
| --- | --- | --- | --- | --- | --- |
| 45.0 h | 42.8 h | 35.0 h | 36.6 h | 36.6 h | 38.0 h |

La documentación de cada módulo está incluida en sus horas. A19 cubre revisión e integración editorial. El reparto 70/30 es una estimación de capacidad, no una declaración de horas trabajadas. Si el trabajo rebasa 3 horas por persona al día o supera la disponibilidad semanal, se reprograma con registro de cambio; no se eliminan requisitos obligatorios.

# 4. Cronograma de trabajo

Periodo: 24/09/2026 a 06/11/2026, 44 días calendario. Los días de fin de semana son ventanas posibles de trabajo breve, sujetas a disponibilidad. P = planeado; R = fechas previas al 10/10 reconstruidas y pendientes de cotejo. Ninguno de estos estados equivale a completado.

Tabla 13. Fechas iniciales, hitos y evidencias 1/2
| Fase / ID | Inicio / fin | Resp. | Entregable estimado / evidencia esperada | Estado |
| --- | --- | --- | --- | --- |
| A01; Análisis | 24/09; 26/09 | E | Matriz sin requisitos obligatorios omitidos; alcance revisado. | R |
| A02; Git y organización | 24/09; 26/09 | K | Acceso de seis integrantes, .gitignore y primer registro auténtico; URL anotada. | R |
| A03; Arquitectura | 27/09; 29/09 | E | Diagrama y secuencia HTTP/API/PDO; sin acceso directo del navegador a BD. | R |
| A04; Diseño UX/UI | 27/09; 30/09 | S | Login, dashboard, catálogo, formulario, detalle y reportes; contraste y tamaños revisados. | R |
| A05; Base de datos | 30/09; 03/10 | J | Base nueva con >=6 tablas relacionadas; ER, diccionario, schema y seed coherentes. | R |
| A06; Autenticación | 04/10; 06/10 | E | Cliente y administrador aislados; login/logout y rechazos 401/403 reproducibles. | R |
| A07; CRUD productos | 04/10; 07/10 | J | SKU único, precio/stock válidos y producto histórico conservado. | R |
| A08; CRUD categorías/proveedores | 04/10; 07/10 | K | Dos CRUD completos; no romper FK ni listar inactivos como disponibles. | R |
| A09; Usuarios y perfiles | 07/10; 08/10 | E | Cliente solo modifica lo propio; administración restringida y bajas preservan historial. | R |
| A10; Frontend de compra | 09/10; 12/10 | S | Agregar, cambiar y retirar; ordenar/paginar; estados vacío/error/carga y vista móvil. | R |
| A11; Venta transaccional | 09/10; 12/10 | J | COMMIT consistente; fallo intermedio revierte venta, detalle, stock y movimiento. | R |

# 4.1 Cronograma (continuación)

Tabla 14. Fechas iniciales, hitos y evidencias 2/2
| Fase / ID | Inicio / fin | Resp. | Entregable estimado / evidencia esperada | Estado |
| --- | --- | --- | --- | --- |
| A12; Historial e inventario | 13/10; 14/10 | K | Motivo/actor/fecha registrados; cancelación repone stock una sola vez; alerta de stock bajo. | P |
| A13; Dashboard y reportes | 13/10; 15/10 | A | Totales y gráficas coinciden con BD; rango inválido rechazado y vacío representado. | P |
| A14; Integración completa | 16/10; 18/10 | E | Compra completa y gestión administrativa desde interfaz; JSON y códigos correctos. | P |
| A15; Pruebas sistemáticas | 19/10; 22/10 | I | Matriz con obtenido/esperado, capturas, logs y defectos priorizados; sin resultados supuestos. | P |
| A16; Correcciones e integración | 23/10; 26/10 | J | Sin defectos críticos abiertos; commits vinculados a pruebas repetidas. | P |
| A17; Despliegue de ensayo | 27/10; 29/10 | K | Instalación limpia, .env privado y recorrido funcional en destino; bitácora de despliegue. | P |
| A18; Regresión y aceptación | 30/10; 02/11 | I | Ningún fallo crítico; enlaces, roles y datos de prueba comprobados; acta de revisión. | P |
| A19; Documentación continua | 27/09; 04/11 | A | Cada cambio funcional tiene documentación; cierre con índice, tablas y capturas legibles. | R |
| A20; Cierre de entrega | 05/11; 05/11 | S | Checklist completa; descarga e instalación del paquete verificadas por otro integrante. | P |
| A21; Entrega y demostración | 06/11; 06/11 | E | Acuse de entrega y versión identificada; cada integrante explica su contribución real. | P |
| A22; Seguimiento y cambios | 24/09; 06/11 | E | Actas breves; fechas originales preservadas y reprogramaciones justificadas. | R |

Hitos de control: 03/10 modelo y scripts; 12/10 compra y venta transaccional; 18/10 integración completa; 22/10 informe de defectos; 26/10 correcciones; 29/10 ensayo de instalación; 02/11 aceptación; 04/11 PDF consolidado; 05/11 paquete revisado; 06/11 entrega. Las fechas transcurridas se cotejarán con evidencias existentes sin retrofechar commits.

# 4.2 Diagrama de Gantt

Figura 1. Cronograma de 22 actividades. Turquesa: tareas de análisis, diseño, construcción, validación y entrega. Morado: documentación y seguimiento continuos. La línea naranja indica la fecha de elaboración; las barras a su izquierda no acreditan ejecución.

Figura 1: Gantt; consultar PDF. Fechas originales en tabla de cronograma.

## Dependencias y margen de recuperación

Secuencia de mayor sensibilidad: A01 → A03 → A05 → A07/A08 → A11 → A13 → A14 → A15 → A16 → A17 → A18 → A20 → A21. La rama de interfaz A04 → A10 debe converger antes de A14. A19 debe concluir antes del cierre. El 23-26/10 cubre correcciones, el 03-04/11 permite atender ajustes finales tras aceptación y el 05/11 se reserva al empaquetado.

# 5. Recursos y decisiones técnicas

Tabla 15. Tecnologías, herramientas y justificación
| Recurso | Uso y decisión | Responsable |
| --- | --- | --- |
| HTML, CSS y JavaScript | Mantener interfaz existente y consumo HTTP/JSON; sin compilación obligatoria en el hosting. Diseño oscuro con contraste legible. | S / A |
| PHP 8.2+ y PDO | Compatibilidad con el proyecto y alojamiento previsto. Reglas, sesiones y consultas parametrizadas en servidor. Verificar versión y extensiones del plan real. | E / J |
| MySQL o MariaDB | Persistencia relacional y transacciones en destino; revisar compatibilidad de restricciones y SQL. SQLite solo para desarrollo/pruebas locales. | J / K |
| Git y repositorio compartido | Ramas por tarea, commits reales de cada autor, revisión de otro integrante y etiquetas de entrega. Repositorio del equipo: https://github.com/EESH02011/NEXTECH-. | K / todos |
| Editor y diagramas Mermaid | Editar código y documentación versionada; conservar fuentes ER, arquitectura y wireframes. Revisar visualmente su exportación. | E / S / J |
| Navegador y pruebas HTTP | Herramientas de desarrollo, colección de peticiones y pruebas Node.js existentes. Capturas responsivas y comparación de BD antes/después. | I / K |
| Hostinger, dominio y HTTPS | Ensayo de despliegue en Hostinger para https://mamawevo.com/; confirmar plan, base, permisos, certificado y acceso antes del 16/10. | K / E |
| Equipo y conectividad | Computadora con navegador/editor por integrante cuando esté disponible; uso compartido en laboratorio si falta equipo. Internet y respaldo local cifrado. | E / todos |
| Servicios de pago y correo | Modo de prueba para integraciones externas. Credenciales en entorno y permisos restringidos; ningún cobro real requerido para la demostración académica. | K / E |

No se fija un costo de hosting, dominio o servicios porque no se proporcionó el plan contratado. El presupuesto técnico supone reutilizar el proyecto y las herramientas disponibles. Cualquier gasto adicional se consultará con el equipo antes de comprometerlo.

## Flujo de arquitectura a conservar

Cliente/administrador → navegador → HTTP/JSON → API REST → validación y permisos en PHP → regla de negocio → transacción PDO → base de datos → respuesta → interfaz actualizada. Las credenciales de BD y las operaciones críticas permanecen en servidor; el total enviado por el navegador no es la autoridad de cálculo.

# 6. Riesgos y seguimiento

Tabla 16. Registro de riesgos
| Riesgo | Prob. / impacto | Prevención | Respuesta | Resp. |
| --- | --- | --- | --- | --- |
| R01; Acceso o plan Hostinger incompatible | Media / alto | Confirmar PHP, BD y acceso antes del 16/10. | Instalar en entorno MySQL alterno autorizado; registrar limitación y gestionar acceso antes del ensayo. | K |
| R02; Errores por diferencias SQLite/MySQL | Alta / alto | Probar scripts y venta en MySQL antes del despliegue. | Aislar diferencias, corregir SQL y repetir instalación/rollback. | J |
| R03; Tiempo o ausencia de integrantes | Media / alto | Validar disponibilidad y revisar carga dos veces por semana. | Apoyo toma tarea con traspaso; consumir reserva y registrar cambio sin retirar requisitos. | E |
| R04; Venta duplica stock o pierde consistencia | Media / alto | Transacción, control de existencias e idempotencia; pruebas concurrentes. | Bloquear liberación; corregir y repetir comparación de tablas y cancelaciones. | J |
| R05; Conflictos de integración y regresiones | Media / alto | Ramas pequeñas y revisión antes de integrar. | Revertir cambio defectuoso con Git; reproducir fallo y añadir caso de regresión. | S |
| R06; Evidencias personales o Git insuficientes | Alta / alto | Cada autor registra sus cambios y documentación desde ahora; revisar historial semanal. | Documentar faltantes y aportes verificables; consultar al docente, nunca inventar historial. | E |
| R07; Credenciales expuestas o acceso indebido | Media / alto | .env privado, revisión de secretos, permisos y sesiones. | Revocar/rotar credenciales, corregir acceso y repetir pruebas antes de publicar. | K |
| R08; PDF, enlaces o evidencias incompletos | Media / medio | Actualizar documentos junto al código; cotejo con 16 apartados. | Usar 03-04/11 para corregir; revisar PDF y enlaces desde otro equipo. | A |
| R09; Proveedor de pago/correo no disponible | Media / medio | Mantener cuentas y datos de prueba; aislar integración externa. | Demostrar venta en modo académico y registrar integración pendiente sin afirmar cobros reales. | K |

# 6.1 Seguimiento y control de cambios

Se propone actualizar el tablero al terminar cada sesión y realizar reuniones de 20 minutos los martes y viernes, con revisión adicional el 05/11. Primer control de esta versión: 13/10; los controles anteriores no se dan por celebrados. E coordina; cada responsable explica el estado, enlace a evidencia, bloqueo y siguiente paso.

Tablero propuesto: tareas/issues de https://github.com/EESH02011/NEXTECH-; como respaldo, archivo docs/seguimiento.md versionado y compartido por el canal del equipo. Estados: Planeada, En proceso, En revisión, Bloqueada y Terminada. Una tarea solo pasa a Terminada cuando cumple su criterio y el apoyo revisa código, prueba y documento.

Tabla 17. Indicadores de seguimiento
| Indicador | Cálculo y criterio de acción |
| --- | --- |
| Avance verificable | Suma de horas estimadas de tareas aceptadas / horas del plan. No usar porcentaje de archivos escritos. |
| Desviación de fecha | Fecha real menos fecha fin original en días calendario; si no termina, registrar fecha de corte y atraso acumulado. |
| Defectos y bloqueos | Cualquier venta inconsistente, vulneración de permisos, pérdida de datos o instalación imposible bloquea entrega. |
| Participación | Al menos una contribución verificable en desarrollo o pruebas y una en documentación por integrante; no basta el número de commits. |
| Cambio de alcance o fecha | Retraso superior a 2 días o dependencia bloqueada obliga a revisar cronograma y responsables en la siguiente reunión. |

## Procedimiento de cambio

1. Crear solicitud C-XX con causa y actividad afectada. 2. Estimar impacto en dependencias, horas y fecha final. 3. E y el responsable proponen solución; el equipo revisa y consulta al docente si cambia un requisito obligatorio o la entrega. 4. Registrar fecha original y nueva sin sobrescribir la línea base. 5. Versionar el plan y repetir las pruebas afectadas.

Tabla 18. Formato para comparar previsto frente a real
| Actividad / original | Real / resultado | Desviación / causa | Acción / responsable |
| --- | --- | --- | --- |
| A15 / 22/10/2026 | Pendiente de ejecución | Por medir al cierre | Registrar defectos y correcciones / I |
| A18 / 02/11/2026 | Pendiente de ejecución | Por medir al cierre | Validar regresión antes de cierre / I |
| A21 / 06/11/2026 | Pendiente de ejecución | Por medir al cierre | Registrar acuse / E |

Cada registro conservará también fecha revisada, evidencia, persona revisora y código de cambio. El repositorio, su enlace y el número de versión entregado se actualizarán en portada, README y PDF antes del envío. La URL no se inventará ni se sustituirá por una dirección ficticia.

# 7. Plan de pruebas y entrega

Las siguientes pruebas están planificadas; el resultado obtenido y el estado se llenarán al ejecutarlas. Se usarán datos académicos reiniciables, un administrador y dos clientes distintos. I coordina, el responsable del módulo corrige y otra persona verifica. A15 ejecuta la batería; A18 repite los casos críticos en el entorno de destino.

Tabla 19. Reglas de negocio que deben comprobarse
| ID | Regla de aceptación |
| --- | --- |
| RN01 | No vender sin existencias; validar stock al confirmar, no solo al agregar. |
| RN02 | Cantidades enteras positivas; precio y producto válidos. |
| RN03 | Calcular subtotal, impuesto y total en backend con redondeo consistente. |
| RN04 | Crear venta, detalle, stock y movimiento en una transacción; fallo implica rollback. |
| RN05 | Folio único y repetición de solicitud sin duplicar descuento o venta. |
| RN06 | Cancelar solo bajo permiso y estado permitido; registrar motivo y compensar stock una vez. |
| RN07 | Preservar ventas finalizadas e historial; bajas importantes son lógicas. |
| RN08 | Cliente accede solo a su información; rol y propiedad se comprueban en servidor. |

Tabla 20. Preparación de pruebas
| Elemento | Condición |
| --- | --- |
| Entorno | Base nueva desde SQL; separar demo de datos reales. Registrar versión/commit, PHP, motor de BD y navegador. |
| Datos | Producto con 2 unidades, producto agotado, categoría/proveedor relacionados, SKU/correo duplicados y ventas de fechas conocidas. |
| Registro | ID, fecha, ejecutor, versión, entrada, esperado, obtenido, estado, captura/log y defecto relacionado. |
| Cierre | Cero defectos críticos o altos sin resolver; rutas obligatorias y reglas aprobadas. Los casos fallidos se repiten tras la corrección. |

# 7.1 Matriz de pruebas previstas

Tabla 21. Pruebas de aceptación 1/2
| ID / módulo | Entrada o acción | Resultado esperado | Resp. |
| --- | --- | --- | --- |
| CP01; Autenticación / A06 | Credenciales válidas, inválidas y logout. | Acceso correcto; rechazo genérico; sesión cerrada no permite rutas privadas. | E |
| CP02; Permisos / A09 | Cliente solicita administración y pedido de otro cliente. | 401 sin sesión; 403/404 según contrato para acceso ajeno; sin fuga de datos. | I |
| CP03; CRUD / A07-A09 | Alta/edición/baja y SKU/correo repetidos; referencia inexistente. | Persistencia correcta; 409/422 duplicado; referencia rechazada y baja conserva historial. | J |
| CP04; Catálogo / A10 | Buscar laptop, filtrar categoría, ordenar y pasar de página. | Productos correctos; total coherente y páginas sin repetición. | S |
| CP05; Carrito / A10 | Agregar 1 unidad, cambiar a 2, retirar; cantidad -1 o decimal. | Operaciones válidas persistentes; inválidas rechazadas en API. | S |
| CP06; Importes / A11 | 2 unidades de 116.00, tasa de prueba incluida 16%, envío 0; alterar total del cliente. | Total 232.00, base 200.00, impuesto 32.00; backend ignora importes manipulados. | J |
| CP07; Venta exitosa / A11 | Confirmar carrito válido desde navegador. | Folio único, venta/detalle persistentes, stock descontado y movimiento enlazado. | J |
| CP08; Rollback / A11 | Provocar fallo de BD tras crear venta, antes de terminar movimiento. | Ninguna venta parcial; comparar las cuatro operaciones con estado previo. | I |

Estado de estos casos en el plan: pendiente de ejecución. La evidencia final deberá enlazar el caso con la actividad, el requisito, el commit probado y el resultado observado; el historial de pruebas previas no sustituye la aceptación de la versión final.

# 7.1 Matriz de pruebas previstas (continuación)

Tabla 22. Pruebas de aceptación 2/2
| ID / módulo | Entrada o acción | Resultado esperado | Resp. |
| --- | --- | --- | --- |
| CP09; Stock / A11 | Dos clientes intentan adquirir la última unidad simultáneamente. | Solo una venta aceptada; otra rechazada por stock; inventario nunca negativo. | I |
| CP10; Cancelación / A12 | Cancelar con rol permitido; repetir y probar sin permiso. | Actor/motivo visibles; stock repuesto una vez; repetida o no autorizada no altera BD. | K |
| CP11; Reportes / A13 | Periodo con ventas conocidas, vacío y rango invertido; cambiar inventario. | Tablas y ambas gráficas coinciden con BD; vacío visible y rango inválido rechazado. | A |
| CP12; Errores / A14 | Registro inexistente, JSON malformado, servidor 500 y conexión caída. | 404/400/500 según contrato; interfaz recuperable; sin SQL, stack ni credenciales. | I |
| CP13; Seguridad / A06 | Entrada maliciosa, sesión expirada, CSRF ausente y revisión de .env. | Sin inyección ni script ejecutado; acceso/operación denegados y secretos no públicos. | K |
| CP14; Responsividad / A10 | Recorrido a 1440, 1024, 768 y 390 px; teclado y foco. | Controles legibles y utilizables; sin contenido cortado ni acciones inaccesibles. | S |
| CP15; Instalación / A17 | Desplegar desde paquete y restaurar respaldo en base aislada. | SQL y semilla reproducibles; login/compra funcionales; datos restaurados verificables. | K |
| CP16; Regresión / A18 | Repetir CP01-CP15 y doble envío de confirmar venta. | Resultados registrados y coherentes; reintento no duplica venta ni inventario. | I |

Estado de estos casos en el plan: pendiente de ejecución. La evidencia final deberá enlazar el caso con la actividad, el requisito, el commit probado y el resultado observado; el historial de pruebas previas no sustituye la aceptación de la versión final.

# 7.2 Lista de verificación de entrega

Tabla 23. Checklist final; todos los puntos pendientes de revisión
| Elemento | Actividad / responsable | Criterio para marcar conforme |
| --- | --- | --- |
| Sistema integrado | A14, A18 / E, I | Recorrido cliente y administrador; todos los botones del alcance funcionan. |
| Base de datos | A05, A17 / J, K | schema.sql, seed.sql y datos de prueba; instalación desde cero y restauración. |
| Seguridad | A15, A18 / I | Roles, propiedad, validación, sesiones y transacción sin defectos críticos. |
| Repositorio | A02, A22 / E, K | URL real, acceso del docente, historial progresivo y aportes identificables de seis integrantes. |
| API | A19 / K, A | Contratos completos con errores/permisos y al menos 12 rutas funcionales verificadas. |
| README e instalación | A19, A20 / A, S | Pasos ejecutables, requisitos, .env.example, usuarios demo, capturas y ejecución. |
| Documentación final PDF | A19, A20 / A, S | 16 apartados, índice, páginas, tablas/figuras, pruebas reales y enlaces. |
| Planeación para Unidad 2 | A20, A21 / S, E | Adjuntar este archivo al documento/entrega de Teams indicado en la actividad. |
| Demostración | A18, A21 / I, E | Guion: login, CRUD, compra, regla inválida, rollback, historial, reportes y roles. |
| Paquete y entrega | A20, A21 / S, E | Versión identificada, archivos abiertos desde otro equipo y acuse del 06/11. |

## Criterio de liberación y presentación

La entrega se autoriza cuando la lista esté revisada por dos integrantes, no haya defectos críticos/altos abiertos y los documentos correspondan al código entregado. El guion de demostración mostrará la comunicación interfaz/API/BD y los rechazos de seguridad. Cada persona explicará cambios propios comprobables, evitando atribuirse historial no documentado.

Nombre del entregable de esta actividad: Planeacion_Proyecto_TiendaTecnologia.pdf, al no existir número asignado al equipo. Repositorio: https://github.com/EESH02011/NEXTECH-. E y K deberán verificar el acceso del docente antes del cierre. Sitio previsto: https://mamawevo.com/; el despliegue y sus pruebas siguen planificados. El plan queda preparado para agregarse a la entrega de la Actividad de Teams Proyecto Unidad 2; el envío lo realiza el equipo.

# Fuentes de la planeación

Tabla 24. Documentos base y aplicación
| Fuente | Uso en este documento |
| --- | --- |
| Actividad extra: Planeación y organización del proyecto final de software (texto proporcionado por el usuario). | Siete apartados, matriz, actividades, cronograma/Gantt, recursos, riesgos, pruebas y formato de entrega. |
| Requerimientos_Proyectos_Finales_Software.md (documento del docente proporcionado por el usuario). | Numerales 1-35, mínimos de complejidad, POS de proyectos 1 y 4 y diferenciador del POS personalizado (proyecto 6). |
| Lineamientos_Documentacion_Proyecto_Final_Software.pdf (documento del docente proporcionado por el usuario). | Cronograma con entregables, comparación planeado/real, estructura final de 16 apartados, evidencia y control de versiones. |
| Documentación y código local de NexTech: README.md, docs/cumplimiento.md y documentación técnica previa. | Base de alcance y tecnologías del sistema existente; las afirmaciones de implementación deberán cotejarse en las actividades previstas. |
| Datos proporcionados por el equipo en esta conversación. | Institución, materia, docente, grupo, seis integrantes, ausencia de número de equipo, inicio 24/09/2026 y entrega 06/11/2026. |

## Estado de la línea base

Se conserva la distinción entre alcance documentado anteriormente, trabajo propuesto y evidencia futura. Las fechas, asignaciones y esfuerzo son una propuesta de organización; las fechas reales, contribuciones, aprobaciones y resultados se incorporarán durante el seguimiento. La fecha final informada es fija salvo autorización del docente.

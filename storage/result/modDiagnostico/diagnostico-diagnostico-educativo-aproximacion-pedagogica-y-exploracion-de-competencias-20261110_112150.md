---
title: "Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias"
document_type: "diagnostic_snapshot_for_llm"
language: "es-VE"
generated_at: "2026-11-10T11:21:50-04:00"
source_url: "http://localhost:8000/app/planning/diagnostico?selectedId=1"
instrument: "Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias"
instrument_id: 1
lapso: null
plan: null
referent: null
filters: {"selected_id":1,"selected_name":"Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias","progress_search":"","progress_pestudio":"Todos","progress_grado":"Todos","progress_pensum":"Todos","summary_pestudio":"Todos","summary_grado":"Todos","summary_area":"Todos","question_bank_scope":"Todos","question_bank_load":"Todos","question_search":"","question_show_inactive":"Sí","activation_search":""}
schema_version: 1.0
---

# Exportación Markdown del módulo Diagnóstico

> Snapshot generado el 2026-11-10T11:21:50-04:00 desde `http://localhost:8000/app/planning/diagnostico?selectedId=1`.
> Este snapshot describe el instrumento seleccionado y los catálogos independientes visibles en la página.

El archivo es autocontenido, de solo lectura y preparado para análisis asistido por un LLM. No contiene tokens de acceso, texto libre de respuestas ni datos de contacto.

## 1. Cómo leer este documento

Las cifras corresponden al instante indicado en el front matter. Los denominadores y los filtros se explicitan junto a cada indicador. `—` significa que el dato no está disponible o no aplica; no debe interpretarse como cero.

La ficha del instrumento seleccionado y los resúmenes de preguntas, sesiones y respuestas pertenecen a ese instrumento. El banco de preguntas y la activación de áreas son catálogos de componentes hermanos: usan sus propios filtros y no se restringen automáticamente al instrumento seleccionado. La exportación incluye todos los registros del alcance, no solo la página visible de una tabla.

## 2. Contexto del dominio

La jerarquía académica relevante es **programa educativo → plan de estudio → grado → área de formación (pensum)**. Un instrumento diagnóstico (`diag_mains`) agrupa preguntas; cada pregunta puede asociarse a un pensum, competencia, indicador y opciones. Las sesiones representan participaciones de estudiantes y las respuestas enlazan una sesión con preguntas y, cuando aplica, opciones.

| Término | Definición operativa |
|---|---|
| Instrumento | Conjunto de preguntas de diagnóstico identificado por un registro `diag_mains`. |
| Sesión | Intento de un estudiante; puede conservar `diag_main_id` nulo y resolverse por preguntas respondidas o pensum. |
| Respuesta | Registro asociado a una pregunta y sesión. Se exportan metadatos agregados, nunca el texto libre `respuesta`. |
| Opción | Alternativa de una pregunta; `valor = 1` identifica la respuesta correcta para el cálculo de precisión. |
| Competencia / indicador | Referencias curriculares asociadas a una pregunta. |
| Grupo estable | Agrupación de cargas académicas que contextualiza el listado del banco de preguntas. |
| Área de formación | Pensum/asignatura de un grado y plan de estudio. |
| Área de conocimiento | Agrupación opcional de pensums a través de `campo_conocimientos`; puede haber cero o varias adscripciones. |
| Referente | Marco curricular asociado al instrumento. |

## 3. Filtros aplicados y alcance real

| Filtro | Valor | Efecto |
|---|---|---|
| Instrumento seleccionado | 1 | Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias |
| Búsqueda del resumen por área | — | Solo afecta la tabla por pensum; exporta todas las filas coincidentes. |
| Plan de estudio (resumen por área) | Todos | Filtro local de esa tabla. |
| Grado (resumen por área) | Todos | Filtro local de esa tabla. |
| Pensum (resumen por área) | Todos | Filtro local de esa tabla. |
| Plan de estudio (resumen por grado) | Todos | Filtro independiente; grado solo aplica con plan seleccionado. |
| Grado (resumen por grado) | Todos | Filtro independiente. |
| Área de conocimiento | Todos | Filtro independiente de la tabla por área de conocimiento. |
| Banco: plan / grado / pensum | Todos | Filtros propios del componente; independiente del instrumento. |
| Banco: pevaluación / grupo | Todos | Acota el resumen a los pensums de esa carga o grupo. Con un pensum seleccionado solo limita las cargas mostradas como contexto, no las preguntas. |
| Banco: búsqueda | — | Se aplica al detalle cuando hay un pensum seleccionado. |
| Banco: inactivas | Sí | Sí incluye activas e inactivas; No muestra solo activas. |
| Activación: búsqueda | — | Filtro propio por asignatura, código o grado; independiente del instrumento. |

`selectedId` selecciona el instrumento. Los filtros de áreas, grados, conocimiento, banco y activación se mantienen independientes. La pestaña activa y los números de página no limitan la exportación.

## 4. Metodología y denominadores

| Indicador | Fórmula / base |
|---|---|
| Preguntas | Conteo de `diag_questions` con `diag_main_id` igual al instrumento. |
| Pensums con respuestas | Pensums distintos entre preguntas con al menos una respuesta completada. |
| Preguntas con respuestas | Preguntas distintas con respuestas completadas. |
| Respuestas | Respuestas del instrumento con `completado_at` no nulo. |
| Estudiantes | `estudiant_id` distintos en el universo de sesiones descrito abajo. El documento usa solo nombres de pantalla en las cinco sesiones recientes. |
| Completitud | Sesiones completadas ÷ sesiones del universo × 100. Si no hay sesiones, se informa `—`. |
| Abandono | 100 − completitud, sobre el mismo universo; si no hay sesiones, `—`. |
| Precisión | Respuestas correctas ÷ respuestas elegibles × 100. Incluye solo preguntas activas `tipo_pregunta = multiple`, respuestas completadas con `option_id` y opción seleccionada con `valor = 1`. |
| Precisión por pensum | Correctas ÷ respuestas completadas con opción en preguntas de tipo múltiple de ese pensum. La consulta del viewer no restringe este detalle a preguntas activas. |
| Sesiones del instrumento | Primero se cuentan sesiones con `diag_main_id` del instrumento. Si el total es cero, el viewer usa sesiones cuyos `pensum_id` aparecen en las preguntas del instrumento; ese fallback puede incluir sesiones de otros instrumentos. |
| Sesiones por pensum | La pantalla cuenta primero sesiones del pensum, aunque pertenezcan a otros instrumentos; intenta restringir por instrumento solo cuando el conteo por pensum es cero. |
| Área de conocimiento | Agrega métricas por las adscripciones existentes. Un pensum puede aparecer en varias áreas; los totales pueden solaparse. Los pensums sin adscripción se muestran como “Sin área asignada”. |
| Activación | Solo se puede activar un pensum con al menos una pregunta activa. La activación mostrada es un catálogo global de áreas elegibles, no un subconjunto del instrumento seleccionado. |

La exportación es de solo lectura. No accede a `diag_mains.token`, `diag_answers.respuesta`, correos, documentos ni datos de contacto.

## 5. Catálogo de instrumentos

| Instrumento | Estado | Descripción | Lapso | Plan de estudio | Referente | Preguntas | Sesiones directas | Completadas | Respuestas | Precisión |
|---|---|---|---|---|---|---|---|---|---|---|
| Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias | Sí | Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identif… | — | — | — | 955 | 0 | 0 | 37.442 | 57,3% |

**Sesiones directas** cuenta solo `diag_sessions.diag_main_id` del instrumento. Los KPIs de la sección 7 aplican un fallback por pensum cuando ese conteo es cero, por lo que pueden ser mayores. **Precisión** se calcula sobre respuestas de opción múltiple con opción marcada como correcta.

### 5.1 Descripciones completas

**Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias** — Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identifican fortalezas y áreas de oportunidad de manera ágil y precisa. La información recolectada será analizada rigurosamente e incorporada al historial del estudiante, permitiendo trazar rutas de aprendizaje personalizadas y garantizar un seguimiento evolutivo basado en evidencias, facilitando así una intervención docente más asertiva y centrada en el desarrollo individual.

## 6. Ficha del instrumento seleccionado

| Campo | Valor |
|---|---|
| ID | 1 |
| Nombre | Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias |
| Descripción | Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identif… |
| Estado | Sí |
| Lapso mostrado | — |
| Plan mostrado | — |
| Referente mostrado | — |
| Origen de lapso | No disponible; la vista muestra —. |
| Origen del plan | Las preguntas corresponden a varios planes; la vista indica “Múltiples planes”. |
| Origen del referente | No disponible; la vista muestra —. |

**Descripción completa:** Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identifican fortalezas y áreas de oportunidad de manera ágil y precisa. La información recolectada será analizada rigurosamente e incorporada al historial del estudiante, permitiendo trazar rutas de aprendizaje personalizadas y garantizar un seguimiento evolutivo basado en evidencias, facilitando así una intervención docente más asertiva y centrada en el desarrollo individual.

## 7. KPIs del instrumento seleccionado

| Indicador | Valor | Definición / base |
|---|---|---|
| Preguntas | 955 | Preguntas vinculadas al instrumento. |
| Áreas de formación con respuestas | 60 | Pensums distintos con respuestas completadas. |
| Preguntas con respuestas | 637 | Preguntas distintas con respuestas completadas. |
| Respuestas completadas | 37.442 | `completado_at` no nulo. |
| Estudiantes | 441 | Estudiantes distintos en el universo de sesiones. |
| Sesiones | 3.572 | Fallback por pensum de preguntas; puede incluir sesiones de otros instrumentos. |
| Sesiones completadas | 3.524 | `completado_at` no nulo. |
| Completitud | 98,7% | Completadas ÷ sesiones × 100. |
| Tasa de abandono | 1,3% | 100 − completitud. |
| Precisión | 57,3% | 18.177 correctas / 31.750 respuestas elegibles. |

## 8. Resumen general

### 8.1 Sesiones recientes

| Estudiante (nombre visible) | Área / pensum | Progreso | Inicio |
|---|---|---|---|
| LÓPEZ PALACIOS OMAR HERNÁN | PRIMER AÑO - OC-1 | 100,0% | 19/10/2026 07:29 |
| LÓPEZ PALACIOS OMAR HERNÁN | PRIMER AÑO - CN-1 | 100,0% | 13/10/2026 07:58 |
| QUINTANA VILLEGAS JOSÉ IGNACIO | CUARTO AÑO - GH-4 | 100,0% | 09/10/2026 12:15 |
| TORRES REVILLA ÁNGEL EDUARDO | CUARTO AÑO - GH-4 | 100,0% | 09/10/2026 12:13 |
| QUINTANA VILLEGAS JOSÉ IGNACIO | CUARTO AÑO - BI-4 | 100,0% | 09/10/2026 12:12 |

### 8.2 Preguntas por tipo

| Tipo | Cantidad |
|---|---|
| multiple | 807 |
| open | 147 |
| scale | 1 |

### 8.3 Preguntas por dificultad

| Dificultad | Cantidad |
|---|---|
| easy | 256 |
| medium | 585 |
| hard | 114 |

## 9. Por área de formación (pensum)

| Área de formación | Plan | Grado | Asignatura | Preguntas activas | Sesiones | Completadas | Finalización | Precisión | Respuestas | Correctas |
|---|---|---|---|---|---|---|---|---|---|---|
| 5TO GRADO - LEN-5G | 21000 EDUCACION PRIMARIA | 5TO GRADO | LENGUA | 10 | 53 | 53 | 100,0% | 54,5% | 530 | 289 |
| 6TO GRADO - LEN-6G | 21000 EDUCACION PRIMARIA | 6TO GRADO | LENGUA | 10 | 53 | 53 | 100,0% | 59,6% | 530 | 316 |
| 6TO GRADO - MAT-6G | 21000 EDUCACION PRIMARIA | 6TO GRADO | MATEMÁTICAS | 10 | 50 | 50 | 100,0% | 41,4% | 500 | 207 |
| PRIMER AÑO - CA-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | CASTELLANO | 20 | 55 | 55 | 100,0% | 43,2% | 880 | 380 |
| PRIMER AÑO - EF-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | EDUCACION FÍSICA | 10 | 54 | 54 | 100,0% | 73,5% | 540 | 397 |
| PRIMER AÑO - OC-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | ORIENTACIÓN Y CONVIVENCIA | 10 | 38 | 38 | 100,0% | 63,4% | 380 | 241 |
| QUINTO AÑO - MA-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | MATEMÁTICAS | 10 | 44 | 44 | 100,0% | 42,5% | 440 | 187 |
| QUINTO AÑO - EF-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | EDUCACION FÍSICA | 10 | 61 | 61 | 100,0% | 56,9% | 610 | 347 |
| QUINTO AÑO - FI-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FÍSICA | 10 | 57 | 57 | 100,0% | 59,8% | 570 | 341 |
| QUINTO AÑO - BI-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | BIOLOGÍA | 10 | 58 | 58 | 100,0% | 57,5% | 463 | 266 |
| QUINTO AÑO - CT-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | CIENCIAS DE LA TIERRA | 10 | 56 | 56 | 100,0% | 53,6% | 448 | 240 |
| SEGUNDO AÑO - EDFI-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | EDUCACION FÍSICA | 10 | 70 | 70 | 100,0% | 91,7% | 700 | 642 |
| SEGUNDO AÑO - BIO-TEC-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 68 | 68 | 100,0% | 52,5% | 543 | 285 |
| SEGUNDO AÑO - FISICA-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | FÍSICA | 10 | 67 | 67 | 100,0% | 70,3% | 670 | 471 |
| SEGUNDO AÑO - MATEMAT-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | MATEMÁTICAS | 10 | 68 | 68 | 100,0% | 61,3% | 680 | 417 |
| SEGUNDO AÑO - IDIOMAS-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | IDIOMAS | 10 | 66 | 66 | 100,0% | 47,6% | 462 | 220 |
| SEGUNDO AÑO - LEN-LIT-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | LENGUA Y LITERATURA | 10 | 68 | 68 | 100,0% | 42,5% | 544 | 231 |
| SEGUNDO AÑO - GHSN-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 67 | 67 | 100,0% | 50,1% | 469 | 235 |
| SEGUNDO AÑO - IN-TE-PR-2 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | INNOVACION TECNOLÓGICA Y PRODUCTIVA | 8 | 68 | 68 | 100,0% | 61,8% | 542 | 335 |
| SEGUNDO AÑO - ORI-VOC-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | ORIENTACIÓN VOCACIONAL | 10 | 67 | 67 | 100,0% | 76,7% | 670 | 514 |
| TERCER AÑO - EDFI-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | EDUCACION FÍSICA | 10 | 70 | 70 | 100,0% | 87,9% | 700 | 615 |
| TERCER AÑO - QU-3 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | QUÍMICA | 10 | 68 | 68 | 100,0% | 55,9% | 544 | 304 |
| TERCER AÑO - MATEMAT-3A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | MATEMÁTICAS | 10 | 64 | 64 | 100,0% | 45,8% | 640 | 293 |
| TERCER AÑO - IDIOMAS-3A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | IDIOMAS | 10 | 66 | 66 | 100,0% | 36,9% | 526 | 194 |
| TERCER AÑO - GHSN-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 62 | 62 | 100,0% | 65,4% | 309 | 202 |
| TERCER AÑO - OR-VOC-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | ORIENTACIÓN VOCACIONAL | 5 | 63 | 63 | 100,0% | — | 0 | 0 |
| CUARTO AÑO - QUIM-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | QUÍMICA | 10 | 18 | 18 | 100,0% | 53,5% | 144 | 77 |
| SEGUNDO AÑO - FHC-EMCT-2 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | FORMACION HUMANO CRISTIANA | 10 | 68 | 68 | 100,0% | 61,3% | 543 | 333 |
| QUINTO AÑO - FHC-EMG-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FORMACION HUMANO CRISTIANA | 10 | 61 | 61 | 100,0% | 67,4% | 488 | 329 |
| CUARTO AÑO - CA-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | CASTELLANO | 10 | 73 | 73 | 100,0% | 46,1% | 584 | 269 |
| CUARTO AÑO - MA-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | MATEMÁTICAS | 10 | 62 | 62 | 100,0% | 43,5% | 620 | 270 |
| CUARTO AÑO - EF-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | EDUCACION FÍSICA | 10 | 71 | 71 | 100,0% | 64,1% | 710 | 455 |
| CUARTO AÑO - FI-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | FÍSICA | 10 | 51 | 51 | 100,0% | 50,0% | 510 | 255 |
| CUARTO AÑO - QU-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | QUÍMICA | 10 | 72 | 72 | 100,0% | 46,7% | 576 | 269 |
| CUARTO AÑO - FHC-EMG-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | FORMACION HUMANO CRISTIANA | 10 | 67 | 67 | 100,0% | 53,4% | 534 | 285 |
| SEGUNDO AÑO - QUIMICA-2A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | QUÍMICA | 10 | 70 | 69 | 98,6% | 58,3% | 552 | 322 |
| TERCER AÑO - LEN-LIT-3A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | LENGUA Y LITERATURA | 11 | 69 | 68 | 98,6% | 49,8% | 621 | 309 |
| CUARTO AÑO - IN-TE-PR-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | INNOVACION TECNOLÓGICA Y PRODUCTIVA | 14 | 74 | 73 | 98,6% | 49,9% | 732 | 365 |
| TERCER AÑO - FHC-EMCT-3 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | FORMACION HUMANO CRISTIANA | 10 | 68 | 67 | 98,5% | 57,9% | 534 | 309 |
| QUINTO AÑO - CA-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | CASTELLANO | 10 | 62 | 61 | 98,4% | 58,9% | 489 | 288 |
| QUINTO AÑO - QU-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | QUÍMICA | 10 | 61 | 60 | 98,4% | 45,0% | 480 | 216 |
| TERCER AÑO - FISICA-3A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | FÍSICA | 10 | 64 | 63 | 98,4% | 60,3% | 632 | 381 |
| TERCER AÑO - IN-TE-PR-3 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | INNOVACION TECNOLÓGICA Y PRODUCTIVA | 7 | 64 | 63 | 98,4% | 26,6% | 192 | 51 |
| QUINTO AÑO - OC-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | ORIENTACIÓN Y CONVIVENCIA | 10 | 60 | 59 | 98,3% | 76,6% | 478 | 366 |
| QUINTO AÑO - GH-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | GEOGRAFÍA HISTORIA Y CIUDADANIA | 10 | 59 | 58 | 98,3% | 78,1% | 352 | 275 |
| QUINTO AÑO - FS-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FORMACION PARA LA SOBERANÍA NACIONAL | 10 | 53 | 52 | 98,1% | 61,4% | 417 | 256 |
| TERCER AÑO - BI-AM-T-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 70 | 68 | 97,1% | 75,1% | 546 | 410 |
| QUINTO AÑO - PG-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | PARTICIPACION EN GRUPOS DE C.R.P. | 28 | 62 | 60 | 96,8% | 59,1% | 1.695 | 1.001 |
| QUINTO AÑO - IL-5 | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | INGLES Y OTRAS LENGUAS EXTRANJERAS | 10 | 60 | 58 | 96,7% | 49,2% | 463 | 228 |
| CUARTO AÑO - OR-VOC-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | ORIENTACIÓN VOCACIONAL | 10 | 61 | 59 | 96,7% | 63,4% | 599 | 380 |
| PRIMER AÑO - IL-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | INGLES Y OTRAS LENGUAS EXTRANJERAS | 9 | 54 | 52 | 96,3% | 67,2% | 366 | 246 |
| CUARTO AÑO - BI-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | BIOLOGÍA | 10 | 54 | 52 | 96,3% | 27,3% | 417 | 114 |
| PRIMER AÑO - PG-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | PARTICIPACION EN GRUPOS DE C.R.P. | 24 | 53 | 51 | 96,2% | 46,4% | 977 | 453 |
| CUARTO AÑO - GH-4 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | GEOGRAFÍA  HISTORIA Y CIUDADANIA | 10 | 48 | 46 | 95,8% | 50,0% | 280 | 140 |
| CUARTO AÑO - IDIOMAS-4A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | IDIOMAS | 10 | 69 | 66 | 95,7% | 40,1% | 536 | 215 |
| 5TO GRADO - MAT-5G | 21000 EDUCACION PRIMARIA | 5TO GRADO | MATEMÁTICAS | 10 | 51 | 48 | 94,1% | 40,0% | 493 | 197 |
| PRIMER AÑO - MA-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | MATEMÁTICAS | 11 | 44 | 41 | 93,2% | 74,3% | 460 | 342 |
| PRIMER AÑO - CN-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | CIENCIAS NATURALES | 10 | 40 | 37 | 92,5% | 64,9% | 296 | 192 |
| PRIMER AÑO - GH-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | GEOGRAFÍA  HISTORIA Y CIUDADANIA | 10 | 37 | 33 | 89,2% | 67,3% | 245 | 165 |
| PRIMER AÑO - AP-1 | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | ARTE Y PATRIMONIO | 10 | 41 | 36 | 87,8% | 81,9% | 299 | 245 |
| SEGUNDO AÑO - CA-2 | 31059 EDUCACION MEDIA GENERAL | SEGUNDO AÑO | CASTELLANO | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - CA-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | CASTELLANO | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - IL-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | INGLES Y OTRAS LENGUAS EXTRANJERAS | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - MA-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | MATEMÁTICAS | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - EF-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | EDUCACION FÍSICA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - FI-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | FÍSICA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - QU-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | QUÍMICA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - BI-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | BIOLOGÍA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - GH-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | GEOGRAFÍA  HISTORIA Y CIUDADANIA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - FS-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | FORMACION PARA LA SOBERANÍA NACIONAL | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - OC-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | ORIENTACIÓN Y CONVIVENCIA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - PG-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | PARTICIPACION EN GRUPOS DE C.R.P. | 4 | 0 | 0 | 0,0% | — | 0 | 0 |
| 1ER GRADO - ACI-1G | 21000 EDUCACION PRIMARIA | 1ER GRADO | ÁREAS COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 2DO GRADO - ACI-2G | 21000 EDUCACION PRIMARIA | 2DO GRADO | ÁREA COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 3ER GRADO - ACI-3G | 21000 EDUCACION PRIMARIA | 3ER GRADO | ÁREA COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 4TO GRADO - ACI-4G | 21000 EDUCACION PRIMARIA | 4TO GRADO | ÁREAS COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 5TO GRADO - ACI-5G | 21000 EDUCACION PRIMARIA | 5TO GRADO | ÁREA COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 6TO GRADO - ACI-6G | 21000 EDUCACION PRIMARIA | 6TO GRADO | ÁREA COMPLEMENTARIA INGLES | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - EDFI-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | EDUCACION FÍSICA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - BI-AM-T-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - FISICA-1 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | FÍSICA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - QUIM-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | QUÍMICA | 5 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - MAT-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | MATEMÁTICAS | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - IDIO-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | IDIOMAS | 9 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - LE-LI-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | LENGUA Y LITERATURA | 20 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - GHSN-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - IN-TE-PR-1 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | INNOVACION TECNOLÓGICA Y PRODUCTIVA | 1 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - OR-VOC-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | ORIENTACIÓN VOCACIONAL | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 3ER GRADO - ACR-3G | 21000 EDUCACION PRIMARIA | 3ER GRADO | ÁREAS COMPLEMENTARIA ROBÓTICA 3G | 5 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - BI-AM-T-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - GHSN-1A | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| PRIMER AÑO - FHC-EMCT-1 | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | FORMACION HUMANO CRISTIANA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| CUARTO AÑO - FHC-EMG-4 | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | FORMACION HUMANO CRISTIANA | 10 | 0 | 0 | 0,0% | — | 0 | 0 |

Los filtros de búsqueda, plan, grado y pensum de esta tabla se aplican antes de exportar. Se incluyen todas las filas coincidentes, no solo la página visible.

## 10. Por grado

| Grado activo | Preguntas | Sesiones | Completadas | Finalización | Precisión | Respuestas | Correctas |
|---|---|---|---|---|---|---|---|
| 1ER GRADO | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 2DO GRADO | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 3ER GRADO | 15 | 0 | 0 | 0,0% | — | 0 | 0 |
| 4TO GRADO | 10 | 0 | 0 | 0,0% | — | 0 | 0 |
| 5TO GRADO | 30 | 104 | 101 | 97,1% | 47,5% | 1.023 | 486 |
| 6TO GRADO | 30 | 103 | 103 | 100,0% | 50,8% | 1.030 | 523 |
| CUARTO AÑO | 144 | 720 | 710 | 98,6% | 49,6% | 6.242 | 3.094 |
| PRIMER AÑO | 114 | 416 | 397 | 95,4% | 59,9% | 4.443 | 2.661 |
| QUINTO AÑO | 148 | 754 | 745 | 98,8% | 58,7% | 7.393 | 4.340 |
| SEGUNDO AÑO | 108 | 747 | 746 | 99,9% | 62,8% | 6.375 | 4.005 |
| TERCER AÑO | 103 | 728 | 722 | 99,2% | 58,5% | 5.244 | 3.068 |

Solo se incluyen grados con `status_active = 'true'`. El plan de estudio y el grado usan los filtros propios de esta sección.

## 11. Por área de conocimiento

| Área (código — nombre) | Pensums | Preguntas | Sesiones | Completadas | Finalización | Precisión | Respuestas | Correctas |
|---|---|---|---|---|---|---|---|---|
| BI-ES-MG — Bienestar Estudiantil MG | 22 | 262 | 1.170 | 1.142 | 97,6% | 59,2% | 11.836 | 7.001 |
| BI-ES-MGCT — Bienestar Estudiantil MGC&T | 32 | 315 | 2.116 | 2.101 | 99,3% | 56,7% | 17.118 | 9.710 |
| BI-ES-PRI — Bienestar Estudiantil Primaria | 4 | 40 | 207 | 204 | 98,6% | 49,1% | 2.053 | 1.009 |
| BICN-C1 — BIOLOGÍA - CIENCIAS NATURALES (MG) | 6 | 60 | 222 | 219 | 98,6% | 56,2% | 1.750 | 983 |
| BIQI — BIOLOGÍA Y QUÍMICA (MG) | 8 | 75 | 257 | 255 | 99,2% | 53,4% | 2.038 | 1.089 |
| CSC-EXP — CIENCIAS EXPERIMENTALES | 9 | 85 | 313 | 311 | 99,4% | 53,5% | 2.486 | 1.329 |
| CSC-SOC — CIENCIAS SOCIALES | 11 | 110 | 362 | 359 | 99,2% | 66,5% | 2.834 | 1.886 |
| DP-PRI — Docentes Especialistas Primaria | 6 | 60 | 0 | 0 | 0,0% | — | 0 | 0 |
| ESP-MG — Especialistas MG | 5 | 49 | 315 | 308 | 97,8% | 46,9% | 2.353 | 1.103 |
| FHC-MG — FORMACION HUMANO CRISTIANA MG | 5 | 50 | 197 | 196 | 99,5% | 62,0% | 1.565 | 971 |
| IN-MG — INGLES MG | 2 | 19 | 114 | 110 | 96,5% | 57,2% | 829 | 474 |
| IN-MGCT — INGLES MG. CC. TECN. | 5 | 49 | 201 | 198 | 98,5% | 41,3% | 1.524 | 629 |
| IN-PRIM — INGLES PRIMARIA | 6 | 60 | 0 | 0 | 0,0% | — | 0 | 0 |
| LEN — LENGUA (MG) | 2 | 30 | 117 | 116 | 99,1% | 48,8% | 1.369 | 668 |
| LEN — LENGUA (PRI) | 2 | 20 | 106 | 106 | 100,0% | 57,1% | 1.060 | 605 |
| LN-MGCT — LENGUA (MG C&T) | 3 | 41 | 137 | 136 | 99,3% | 46,4% | 1.165 | 540 |
| MAT — MATEMÁTICAS (MG) | 5 | 51 | 258 | 255 | 98,8% | 53,7% | 2.600 | 1.395 |
| MAT — MATEMÁTICAS (PRI) | 2 | 20 | 101 | 98 | 97,0% | 40,7% | 993 | 404 |
| MG-CYT — MATEMÁTICAS MG-CYT | 5 | 50 | 263 | 262 | 99,6% | 59,6% | 2.622 | 1.562 |
| MG-EF — MG - ÁREA EDUCACIÓN FÍSICA | 2 | 20 | 61 | 61 | 100,0% | 56,9% | 610 | 347 |
| MGCT-EF — MGCT - ÁREA EDUCACIÓN FÍSICA | 2 | 20 | 70 | 70 | 100,0% | 91,7% | 700 | 642 |
| PMG — Planificación Media General | 25 | 262 | 754 | 745 | 98,8% | 58,7% | 7.393 | 4.340 |
| PMG-CT — Planificación Media General CT | 24 | 233 | 879 | 877 | 99,8% | 61,0% | 7.549 | 4.607 |
| PP — Planificación Primaria | 11 | 105 | 207 | 204 | 98,6% | 49,1% | 2.053 | 1.009 |
| PR-1RA-ET — PRIMARIA 1RA ETAPA | 2 | 20 | 0 | 0 | 0,0% | — | 0 | 0 |
| PR-2DA-ET — PRIMARIA 2DA ETAPA | 9 | 85 | 207 | 204 | 98,6% | 49,1% | 2.053 | 1.009 |
| RO-MGCT — ROBÓTICA MGCT | 4 | 30 | 206 | 204 | 99,0% | 51,2% | 1.466 | 751 |
| ROB-MG — ROBÓTICA MG | 2 | 52 | 115 | 111 | 96,5% | 54,4% | 2.672 | 1.454 |
| ROB-PRI — ROBÓTICA PRIMARIA | 4 | 21 | 132 | 131 | 99,2% | 52,6% | 734 | 386 |
| Sin área asignada | 5 | 50 | 79 | 77 | 97,5% | 61,5% | 743 | 457 |

> Las áreas con el mismo código y nombre se consolidan en una sola fila. Las adscripciones son de cero a varias por pensum, por lo que un pensum puede sumar en más de un área: las filas pueden solaparse y su suma no equivale al total del instrumento; los pensums sin adscripción aparecen como **Sin área asignada**.

## 12. Banco de preguntas

**Alcance:** Resumen completo por pensum; la paginación visible no limita la exportación.

| Pensum | Plan | Grado | Preguntas | Activas | Cargas | Grupos |
|---|---|---|---|---|---|---|
| #112 PARTICIPACION EN GRUPOS DE C.R.P. | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 28 | 28 | 6 | 3 |
| #80 PARTICIPACION EN GRUPOS DE C.R.P. | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 24 | 24 | 6 | 3 |
| #62 CASTELLANO | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 20 | 20 | 2 | 0 |
| #140 LENGUA Y LITERATURA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 20 | 20 | 0 | 0 |
| #67 MATEMÁTICAS | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 15 | 11 | 2 | 0 |
| #207 INNOVACION TECNOLÓGICA Y PRODUCTIVA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 14 | 14 | 6 | 3 |
| #195 LENGUA Y LITERATURA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 11 | 11 | 2 | 0 |
| #114 ÁREAS COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 1ER GRADO | 10 | 10 | 2 | 0 |
| #115 ÁREA COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 2DO GRADO | 10 | 10 | 2 | 0 |
| #116 ÁREA COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 3ER GRADO | 10 | 10 | 2 | 0 |
| #117 ÁREAS COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 4TO GRADO | 10 | 10 | 2 | 0 |
| #118 ÁREA COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 5TO GRADO | 10 | 10 | 2 | 0 |
| #53 LENGUA | 21000 EDUCACION PRIMARIA | 5TO GRADO | 10 | 10 | 2 | 0 |
| #54 MATEMÁTICAS | 21000 EDUCACION PRIMARIA | 5TO GRADO | 10 | 10 | 2 | 0 |
| #60 LENGUA | 21000 EDUCACION PRIMARIA | 6TO GRADO | 10 | 10 | 2 | 0 |
| #119 ÁREA COMPLEMENTARIA INGLES | 21000 EDUCACION PRIMARIA | 6TO GRADO | 10 | 10 | 2 | 0 |
| #61 MATEMÁTICAS | 21000 EDUCACION PRIMARIA | 6TO GRADO | 10 | 10 | 2 | 0 |
| #63 ARTE Y PATRIMONIO | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #64 EDUCACION FÍSICA | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #65 CIENCIAS NATURALES | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #79 ORIENTACIÓN Y CONVIVENCIA | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #66 GEOGRAFÍA  HISTORIA Y CIUDADANIA | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #69 CASTELLANO | 31059 EDUCACION MEDIA GENERAL | SEGUNDO AÑO | 10 | 10 | 0 | 0 |
| #225 FORMACION HUMANO CRISTIANA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #100 ORIENTACIÓN Y CONVIVENCIA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #99 FORMACION PARA LA SOBERANÍA NACIONAL | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #91 CASTELLANO | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #92 INGLES Y OTRAS LENGUAS EXTRANJERAS | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #93 MATEMÁTICAS | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #95 FÍSICA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #96 QUÍMICA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #97 BIOLOGÍA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #98 GEOGRAFÍA  HISTORIA Y CIUDADANIA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #94 EDUCACION FÍSICA | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 10 | 10 | 0 | 0 |
| #120 GEOGRAFÍA HISTORIA Y CIUDADANIA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #226 FORMACION HUMANO CRISTIANA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #111 ORIENTACIÓN Y CONVIVENCIA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #102 CASTELLANO | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #103 INGLES Y OTRAS LENGUAS EXTRANJERAS | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #104 MATEMÁTICAS | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #105 EDUCACION FÍSICA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #106 FÍSICA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #107 QUÍMICA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #108 BIOLOGÍA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #109 CIENCIAS DE LA TIERRA | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #110 FORMACION PARA LA SOBERANÍA NACIONAL | 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | 10 | 10 | 2 | 0 |
| #222 FORMACION HUMANO CRISTIANA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #143 ORIENTACIÓN VOCACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #141 GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #136 MATEMÁTICAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #132 FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #131 BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #130 EDUCACION FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 10 | 10 | 2 | 0 |
| #164 ORIENTACIÓN VOCACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #223 FORMACION HUMANO CRISTIANA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #162 GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #161 LENGUA Y LITERATURA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 1 |
| #160 IDIOMAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #158 MATEMÁTICAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #155 QUÍMICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #154 FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #153 BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #152 EDUCACION FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 10 | 10 | 2 | 0 |
| #224 FORMACION HUMANO CRISTIANA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #196 GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #189 EDUCACION FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #190 BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #191 FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #192 QUÍMICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #193 MATEMÁTICAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #194 IDIOMAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 10 | 10 | 2 | 0 |
| #208 ORIENTACIÓN VOCACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #240 GEOGRAFÍA  HISTORIA Y CIUDADANIA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #239 BIOLOGÍA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #238 QUÍMICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #237 FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #236 EDUCACION FÍSICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #235 MATEMÁTICAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #234 IDIOMAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #233 CASTELLANO | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #200 BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 0 | 0 |
| #202 QUÍMICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 0 | 0 |
| #206 GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 0 | 0 |
| #244 FORMACION HUMANO CRISTIANA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | 10 | 10 | 2 | 0 |
| #68 INGLES Y OTRAS LENGUAS EXTRANJERAS | 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | 9 | 9 | 2 | 0 |
| #138 IDIOMAS | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 9 | 9 | 2 | 0 |
| #163 INNOVACION TECNOLÓGICA Y PRODUCTIVA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | 8 | 8 | 6 | 3 |
| #197 INNOVACION TECNOLÓGICA Y PRODUCTIVA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 7 | 7 | 6 | 3 |
| #146 ÁREAS COMPLEMENTARIA ROBÓTICA 3G | 21000 EDUCACION PRIMARIA | 3ER GRADO | 5 | 5 | 2 | 0 |
| #133 QUÍMICA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 5 | 5 | 2 | 0 |
| #198 ORIENTACIÓN VOCACIONAL | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | 5 | 5 | 2 | 0 |
| #101 PARTICIPACION EN GRUPOS DE C.R.P. | 31059 EDUCACION MEDIA GENERAL | CUARTO AÑO | 4 | 4 | 6 | 3 |
| #142 INNOVACION TECNOLÓGICA Y PRODUCTIVA | 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | PRIMER AÑO | 1 | 1 | 6 | 4 |

### 12.1 Preguntas y opciones

*No hay detalle de preguntas para el filtro actual.*

El banco pertenece a su componente global, no queda limitado por `selectedId`. Los filtros de plan, grado, carga y grupo restringen el resumen de pensums. Cuando hay un pensum seleccionado, plan, grado, carga y grupo ya no acotan las preguntas: solo la búsqueda de texto y la casilla de inactivas lo hacen; carga y grupo únicamente contextualizan las pevaluaciones visibles.

## 13. Activación de áreas de formación

| Plan | Grado | Asignatura | Preguntas totales | Preguntas activas | Activa para diagnóstico |
|---|---|---|---|---|---|
| 21000 EDUCACION PRIMARIA | 1ER GRADO | CNAT-1G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | CSOC-1G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | EFIS-1G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | ACF-1G — ÁREA COMPLEMENTARIA  FORMACION HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | MAT-1G — MATEMATICAS | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | EEST-1G — EDUCACION ESTETICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | LEN-1G — LENGUA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | ACM-1G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | ACI-1G — ÁREAS COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | ACR-1G — ÁREAS COMPLEMENTARIA ROBÓTICA 1G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 1ER GRADO | COM-IN-1G — ÁREA COMPLEMENTARIA INTEGRAL 1G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | LEN-2G — LENGUA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | CNAT-2G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | CSOC-2G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | EEST-2G — EDUCACIÓN ESTÉTICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | EFIS-2G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | ACF-2G — ÁREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | MAT-2G — MATEMATICAS | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | ACI-2G — ÁREA COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | ACR-2G — ÁREAS COMPLEMENTARIA ROBÓTICA 2G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | ACM-2G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 2DO GRADO | COM-IN-2G — ÁREA COMPLEMENTARIA INTEGRAL 2G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | LEN-3G — LENGUA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | MAT-3G — MATEMÁTICAS | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | CNAT-3G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | CSOC-3G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | EEST-3G — EDUCACIÓN ESTÉTICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | EFIS-3G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | ACF-3G — ÁREA COMPLEMENTARIA FORMACIÓN HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | ACI-3G — ÁREA COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | ACR-3G — ÁREAS COMPLEMENTARIA ROBÓTICA 3G | 5 | 5 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | ACM-3G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 3ER GRADO | COM-IN-3G — ÁREA COMPLEMENTARIA INTEGRAL 3G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | LEN-4G — LENGUA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | MAT-4G — MATEMÁTICAS | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | CNAT-4G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | CSOC-4G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | EEST-4G — EDUCACIÓN ESTÉTICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | EFIS-4G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | ACF-4G — ÁREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | ACI-4G — ÁREAS COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | ACR-4G — ÁREAS COMPLEMENTARIA ROBÓTICA 4G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | ACM-4G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 4TO GRADO | COM-IN-4G — ÁREA COMPLEMENTARIA INTEGRAL 4G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | LEN-5G — LENGUA | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | MAT-5G — MATEMÁTICAS | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | CNAT-5G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | CSOC-5G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | EEST-5G — EDUCACIÓN ESTÉTICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | EFIS-5G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | ACF-5G — ÁREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | ACI-5G — ÁREA COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | ACR-5G — ÁREAS COMPLEMENTARIA ROBÓTICA 5G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | ACM-5G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 5TO GRADO | COM-IN-5G — ÁREA COMPLEMENTARIA INTEGRAL 5G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | LEN-6G — LENGUA | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | MAT-6G — MATEMÁTICAS | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | CNAT-6G — CIENCIAS NATURALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | CSOC-6G — CIENCIAS SOCIALES | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | EEST-6G — EDUCACIÓN ESTÉTICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | EFIS-6G — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | ACF-6G — ÁREA COMPLEMENTARIA FORMACION HUMANO CRISTIANA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | ACI-6G — ÁREA COMPLEMENTARIA INGLES | 10 | 10 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | ACR-6G — ÁREAS COMPLEMENTARIA ROBÓTICA 6G | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | ACM-6G — ÁREA COMPLEMENTARIA MUSICA | 0 | 0 | No |
| 21000 EDUCACION PRIMARIA | 6TO GRADO | COM-IN-6G — ÁREA COMPLEMENTARIA INTEGRAL 6G | 0 | 0 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | CA-1 — CASTELLANO | 20 | 20 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | IL-1 — INGLES Y OTRAS LENGUAS EXTRANJERAS | 9 | 9 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | MA-1 — MATEMÁTICAS | 15 | 11 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | EF-1 — EDUCACION FÍSICA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | AP-1 — ARTE Y PATRIMONIO | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | CN-1 — CIENCIAS NATURALES | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | GH-1 — GEOGRAFÍA  HISTORIA Y CIUDADANIA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | OC-1 — ORIENTACIÓN Y CONVIVENCIA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | PRIMER AÑO | PG-1 — PARTICIPACION EN GRUPOS DE C.R.P. | 24 | 24 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | CA-5 — CASTELLANO | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | IL-5 — INGLES Y OTRAS LENGUAS EXTRANJERAS | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | MA-5 — MATEMÁTICAS | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | EF-5 — EDUCACION FÍSICA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FI-5 — FÍSICA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | QU-5 — QUÍMICA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | BI-5 — BIOLOGÍA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | CT-5 — CIENCIAS DE LA TIERRA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FS-5 — FORMACION PARA LA SOBERANÍA NACIONAL | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | GH-5 — GEOGRAFÍA HISTORIA Y CIUDADANIA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | OC-5 — ORIENTACIÓN Y CONVIVENCIA | 10 | 10 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | PG-5 — PARTICIPACION EN GRUPOS DE C.R.P. | 28 | 28 | No |
| 31059 EDUCACION MEDIA GENERAL | QUINTO AÑO | FHC-EMG-5 — FORMACION HUMANO CRISTIANA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | LEN-LIT-2A — LENGUA Y LITERATURA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | IDIOMAS-2A — IDIOMAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | MATEMAT-2A — MATEMÁTICAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | BIO-TEC-2A — BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | FISICA-2A — FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | QUIMICA-2A — QUÍMICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | ORI-VOC-2A — ORIENTACIÓN VOCACIONAL | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | EDFI-2A — EDUCACION FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | GHSN-2A — GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | IN-TE-PR-2 — INNOVACION TECNOLÓGICA Y PRODUCTIVA | 8 | 8 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | SEGUNDO AÑO | FHC-EMCT-2 — FORMACION HUMANO CRISTIANA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | QU-3 — QUÍMICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | BI-AM-T-1A — BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | GHSN-1A — GEOGRAFÍA HISTORIA Y SOBERANÍA NACIONAL | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | OR-VOC-1A — ORIENTACIÓN VOCACIONAL | 5 | 5 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | LEN-LIT-3A — LENGUA Y LITERATURA | 11 | 11 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | IDIOMAS-3A — IDIOMAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | MATEMAT-3A — MATEMÁTICAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | FISICA-3A — FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | EDFI-1A — EDUCACION FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | FHC-EMCT-3 — FORMACION HUMANO CRISTIANA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | TERCER AÑO | IN-TE-PR-3 — INNOVACION TECNOLÓGICA Y PRODUCTIVA | 7 | 7 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | CA-4 — CASTELLANO | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | MA-4 — MATEMÁTICAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | EF-4 — EDUCACION FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | FI-4 — FÍSICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | QU-4 — QUÍMICA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | BI-4 — BIOLOGÍA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | GH-4 — GEOGRAFÍA  HISTORIA Y CIUDADANIA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | OR-VOC-1A — ORIENTACIÓN VOCACIONAL | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | IDIOMAS-4A — IDIOMAS | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | FHC-EMG-4 — FORMACION HUMANO CRISTIANA | 10 | 10 | No |
| 31059B EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA | CUARTO AÑO | IN-TE-PR-4 — INNOVACION TECNOLÓGICA Y PRODUCTIVA | 14 | 14 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | FPS-G1 — FORMACIÓN PERSONAL Y SOCIAL | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | REAM-G1 — RELACIÓN CON EL AMBIENTE | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | CORE-G1 — COMUNICACIÓN Y REPRESENTACIÓN | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | IN-INI-1G — INGLÉS | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | MU-INI-1G — MÚSICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | RO-INI-1G — ROBÓTICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 1ER GRUPO | EFIS-1N — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | FPS-G2 — FORMACIÓN PERSONAL Y SOCIAL | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | REAM-G2 — RELACIÓN CON EL AMBIENTE | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | CORE-G2 — COMUNICACIÓN Y REPRESENTACIÓN | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | IN-INI-2G — INGLÉS | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | MU-INI-2G — MÚSICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | RO-INI-2G — ROBÓTICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 2DO GRUPO | EFIS-2N — EDUCACIÓN FÍSICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | FPS-G3 — FORMACIÓN PERSONAL Y SOCIAL | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | REAM-G3 — RELACIÓN CON EL AMBIENTE | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | CORE-G3 — COMUNICACIÓN Y REPRESENTACIÓN | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | IN-INI-3G — INGLÉS | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | MU-INI-3G — MÚSICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | RO-INI-3G — ROBÓTICA | 0 | 0 | No |
| 20000 EDUCACION INICIAL | 3ER GRUPO | EFIS-3N — EDUCACIÓN FÍSICA | 0 | 0 | No |

**Búsqueda activa:** 

La vista incluye pensums activos asociados a cargas o preguntas, con grado activo, y filtra por asignatura, código o grado. El universo es independiente del instrumento seleccionado. La regla de negocio impide activar un área sin al menos una pregunta activa.

## 14. CRUD del instrumento seleccionado

| Campo editable | Valor actual |
|---|---|
| Nombre | Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias |
| Descripción | Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identif… |
| Activo | Sí |
| Lapso | — |
| Plan de estudio | — |
| Referente | — |

**Descripción completa:** Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identifican fortalezas y áreas de oportunidad de manera ágil y precisa. La información recolectada será analizada rigurosamente e incorporada al historial del estudiante, permitiendo trazar rutas de aprendizaje personalizadas y garantizar un seguimiento evolutivo basado en evidencias, facilitando así una intervención docente más asertiva y centrada en el desarrollo individual.

El campo `token` se excluye deliberadamente.

## 15. Observaciones y alertas de datos

| Observación |
|---|
| 4 pregunta(s) inactiva(s) están en el alcance del banco. |
| 137 pregunta(s) no tienen opciones. |
| Se aplicó el fallback de sesiones por pensum; esas sesiones pueden corresponder a otros instrumentos asociados al mismo pensum. |
| Las áreas de conocimiento incluyen pensums compartidos; sus agregados pueden solaparse. |
| La ficha seleccionada se presenta junto al catálogo global; sus bases no deben sumarse. |

## 16. Guía para redactar el informe ejecutivo

1. Separar hechos observados, interpretación y recomendación.
2. Citar valor y denominador; no usar porcentajes sin base.
3. No atribuir causas sin evidencia.
4. Explicar ceros y `—` según ausencia real de registros o falta de aplicabilidad.
5. No generalizar resultados del instrumento seleccionado a todo el catálogo ni atribuir las cifras globales del banco/activación a ese instrumento.
6. Considerar solapamientos de áreas de conocimiento y el fallback de sesiones antes de sumar o comparar valores.

## 17. Apéndices y trazabilidad técnica

| Sección | Registros exportados | Fuente principal |
|---|---:|---|
| Instrumentos | 1 | `diag_mains`, `diag_questions`, `diag_sessions`, `diag_answers` |
| Pensums del instrumento | 93 | `diag_questions`, `pensums`, `diag_sessions`, `diag_answers` |
| Banco de preguntas detallado | 0 | `diag_questions`, `diag_options`, `diag_competencies`, `diag_indicators` |
| Sesiones recientes | 5 | `diag_sessions`, `estudiants` |

Las relaciones académicas se obtienen de `pensums`, `pestudios`, `grados`, `asignaturas`, `campo_conocimientos` y `area_conocimientos`. Las sesiones se relacionan con estudiantes, pensums e instrumentos; las respuestas se agregan sin exportar su texto libre.

## 18. Nota de reproducibilidad

Abre `/app/planning/diagnostico`, selecciona el instrumento y establece los filtros que correspondan en cada componente. Pulsa **Descargar MD**. El archivo corresponde al estado al momento de la descarga; para repetir el análisis con datos actuales, vuelve a generarlo.

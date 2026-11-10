# TAREA: Exportación Markdown del módulo Diagnóstico

## 0. Rol y objetivo

Actúa como ingeniero de software senior en un proyecto Laravel 10 / PHP 8.2 en producción.
Debes implementar un botón **"Descargar MD"** en la página de Diagnóstico
(`http://localhost:8000/app/planning/diagnostico`) que genere un archivo Markdown
autocontenido con toda la información presentada en esa página, respetando los
filtros activos. El archivo será usado por otro LLM para redactar informes
ejecutivos de nivel staff engineer con pedagogía y didáctica.

El documento debe explicarse por sí solo: definiciones, fórmulas, denominadores,
observaciones, limitaciones de datos y contexto del dominio. No es un volcado de
datos: es un snapshot analizable.

## 1. Repositorio, stack y reglas del entorno

- Raíz del proyecto: `/home/nuser/code/cfla`
- Stack: Laravel 10, PHP 8.2, Livewire 3, Blade, Tailwind.
- Comando PHP canónico: `/usr/bin/php8.2` (NUNCA `php` a secas).
- El proyecto está en **producción**. No modifiques base de datos, `.env`,
  migraciones, calendarios, horarios, `config:cache` ni assets compilados.
  Si algo lo requiere, detente y alerta.
- El repositorio tiene `graphify-out/`. Antes de explorar con grep, usa:
  `graphify query "<pregunta>"`, `graphify path "A" "B"`, `graphify explain "<concepto>"`.
  Al terminar cambios de código, ejecuta `graphify update .`.
- No ejecutes Pint sobre archivos completos (reformatea código no relacionado).
  Si lo usas, solo `--test` para diagnosticar.
- No hagas commit. Deja los cambios en el working tree.
- No inventes datos. Si un valor no existe, escribe `—` y explica por qué.

## 2. Lectura obligatoria antes de escribir código

### 2.1 Página y componentes

- `resources/views/planning/diagnostic/index.blade.php`
  (vista estática; contiene el header y anida los componentes).
- `app/Livewire/Planning/Diagnostic/DiagMainViewer.php`
  y `resources/views/livewire/planning/diagnostic/diag-main-viewer.blade.php`.
- `app/Livewire/Planning/Diagnostic/DiagMainCrud.php`
  y `resources/views/livewire/planning/diagnostic/diag-main-crud.blade.php`.
- `app/Livewire/Admin/Diagnostic/IndexComponent.php`
  y `resources/views/livewire/admin/diagnostic/index-component.blade.php`.
- `app/Livewire/Planning/Diagnostic/QuestionByPensum.php`
  y `resources/views/livewire/planning/diagnostic/question-by-pensum.blade.php`.

La vista anida:
`@livewire('planning.diagnostic.diag-main-viewer')`,
`@livewire('planning.diagnostic.diag-main-crud')`,
`@livewire('admin.diagnostic.index-component')` y
`@livewire('planning.diagnostic.question-by-pensum')`.
Las secciones dependientes se ocultan hasta que se emite `diag-main-selected`.

### 2.2 Modelos

`app/Models/app/Instrument/`: `DiagMain`, `DiagQuestion`, `DiagSession`,
`DiagAnswer`, `DiagOption`, `DiagReferent`, `DiagCompetency`, `DiagIndicator`.
También `GrupoEstable`, `AreaConocimiento`, `CampoConocimiento`, `Pensum`,
`Pestudio`, `Grado`, `Pevaluacion`, `Lapso`, `Estudiant`.

### 2.3 Referencia de implementación que debes imitar

Ya existe una exportación análoga en Indicadores de Planificación:

- `app/Services/Planning/IndicatorMarkdownBuilder.php`
  (servicio que construye el Markdown; NO consulta BD).
- `app/Livewire/Planning/Indicator/IndexComponent.php`
  (`downloadMarkdownReport()` y `buildMarkdownPayload()`).
- `resources/views/livewire/planning/indicator/index-component.blade.php`
  (botón junto a "Refrescar": `wire:click`, `wire:loading.attr="disabled"`,
  `text/markdown`, `streamDownload`, nombre con `Ymd_His`).

Replica esa arquitectura: **payload filtrado en el componente + builder puro +
descarga con `response()->streamDownload`**. Reutiliza su tono, estructura de
secciones, tablas, front matter y guía de análisis, pero **reanaliza el dominio
de Diagnóstico**: no copies textos de indicadores ni inventes equivalencias.

## 3. Inventario funcional que el documento debe cubrir

El agente debe mapear cada elemento visible de la UI a una sección del `.md`.
Como mínimo:

### 3.1 Catálogo de instrumentos

- Nombre, descripción, estado `active`, lapso, plan de estudio (`pestudio`),
  referente (`DiagReferent`), cantidad de preguntas y de sesiones.
- **Nunca incluir `diag_mains.token`** (identificador de acceso).
- Si un campo directo es null, el viewer lo deriva de las preguntas
  (`displayLapso`, `displayPestudio`, `displayReferent`): reproduce esa
  derivación y declara que es derivada.

### 3.2 KPIs del instrumento seleccionado

- **Preguntas**: `diag_questions` del instrumento.
- **A.Formación c/ resp.**: pensums distintos con al menos una respuesta
  completada del instrumento.
- **Preg. c/ resp.**: preguntas distintas con al menos una respuesta completada.
- **Respuestas**: `diag_answers` con `completado_at` no nulo, del instrumento.
- **Estudiantes**: `estudiant_id` distintos en las sesiones del instrumento.
- **% Completitud** = sesiones con `completado_at` / total de sesiones × 100.
- **Tasa de abandono** = 100 − completitud.
- **Precisión** = respuestas correctas / respuestas contestadas × 100,
  considerando SOLO preguntas `activo = 1` y `tipo_pregunta = 'multiple'`,
  respuestas con `completado_at` y `option_id` no nulos, y opción con `valor = 1`.
- Documenta el **fallback de sesiones**: si no hay sesiones con
  `diag_main_id = instrumento`, el viewer las cuenta por los `pensum_id` de las
  preguntas del instrumento. Ese fallback puede incluir sesiones de otros
  instrumentos sobre el mismo pensum: decláralo como limitación.

### 3.3 Pestaña "Resumen general"

- Sesiones recientes (máx. 5, orden `iniciado_at` desc): estudiante, área/pensum,
  progreso, fecha de inicio.
- Distribución de preguntas por `tipo_pregunta`.
- Distribución de preguntas por `difficulty`.

### 3.4 Pestaña "Por área de formación" (pensums)

Por cada pensum vinculado a las preguntas del instrumento:
- Nombre completo, preguntas activas, sesiones, sesiones completadas,
  % finalización, precisión, respuestas totales, correctas.
- Filtros: búsqueda por asignatura/código, plan de estudio, grado, pensum; paginación.
- **Advertencia**: las sesiones por pensum se cuentan primero por `pensum_id`
  (pueden incluir sesiones de otros instrumentos del mismo pensum); solo si el
  conteo es 0 se restringe por `diag_main_id`. Explícalo en el documento.

### 3.5 Pestaña "Por Grado"

Agregado de la base por pensum según `pensum.grado_id`:
- Grado, preguntas, sesiones, completadas, % finalización, precisión.
- Solo grados activos (`status_active = 'true'`).
- Filtros anidados: plan de estudio → grado. Sin plan, no se puede elegir grado.

### 3.6 Pestaña "Por Área de Conocimiento"

Agregado por `campo_conocimientos.pensum_id` → `area_conocimiento_id`:
- Área (código — nombre), cantidad de pensums, preguntas, sesiones,
  completadas, % finalización, precisión.
- Los pensums sin adscripción caen en la fila **"Sin área asignada"**.
- Un pensum puede pertenecer a 0..N áreas: **los totales pueden solaparse**.
  Declara explícitamente que la suma de filas no equivale al total del instrumento.

### 3.7 Banco de preguntas (`QuestionByPensum`)

- Filtros: plan de estudio, grado, pevaluación, grupo estable, pensum,
  búsqueda de texto, mostrar inactivas.
- Sin pensum seleccionado: resumen por pensum (preguntas totales, activas,
  pevaluaciones, grupos distintos).
- Con pensum seleccionado: preguntas agrupadas por `grupo_estable_id`
  (bloque "Sin grupo estable" cuando no aplica), con:
  pregunta, tipo, dificultad, peso (`weighing`), orden, activa, competencia,
  indicador, opciones y cuál es la correcta (`valor = 1`).
- Incluye el detalle completo del pensum seleccionado; no trunces la pregunta.

### 3.8 Activación de áreas de formación

- Pensums del alcance del instrumento (los que tienen preguntas):
  plan, grado, asignatura, preguntas totales, preguntas activas,
  `status_active_diagnostic`.
- Regla de negocio: no se puede activar un área sin al menos una pregunta activa.
- Incluye también el buscador activo si existe.

### 3.9 CRUD del instrumento

- Campos editables y su valor actual: nombre, descripción, `active`,
  lapso, plan de estudio, referente. Sin `token`.

### 3.10 Filtros y su alcance real

Documenta una tabla equivalente a "efecto real de cada filtro":
- `selectedId` (URL) selecciona el instrumento.
- Filtros de "Por área", "Por Grado" y "Por Área de Conocimiento" son
  independientes entre sí.
- El buscador y los filtros del banco de preguntas y de activación son
  independientes del instrumento, aunque se alimentan de él.
- Si un filtro no se puede aplicar a una sección, dilo: no finjas que aplica.

## 4. Entregables

1. **Servicio** `app/Services/Planning/DiagnosticMarkdownBuilder.php`
   - Método público `build(array $report): string`.
   - Sin consultas a BD.
   - Tolerante a colecciones vacías, nulos y paginadores.
   - Tablas Markdown con celdas escapadas (`|` → `\|`, saltos de línea → espacio).
   - Números con separador de miles y punto decimal; porcentajes con 1 decimal.
2. **Acción de descarga** en `App\Livewire\Planning\Diagnostic\DiagMainViewer`:
   - `downloadMarkdownReport()` que arma el payload y retorna
     `response()->streamDownload(..., 'diagnostico-{slug}-{Ymd_His}.md',
     ['Content-Type' => 'text/markdown; charset=UTF-8'])`.
   - `buildMarkdownPayload()` privado con los datos ya filtrados.
   - Nombre: slug del instrumento; `catalogo` si no hay selección.
3. **Botón** en `resources/views/livewire/planning/diagnostic/diag-main-viewer.blade.php`,
   en la barra del selector, junto a "Nuevo Diagnóstico":
   - `wire:click="downloadMarkdownReport"`, `wire:loading.attr="disabled"`,
     `min-h-[44px]`, `aria-label`, `title`, texto "Descargar MD" (móvil: "MD"),
     acento esmeralda coherente con la página.
   - Debe estar disponible **siempre**: con instrumento seleccionado exporta el
     detalle; sin selección exporta un catálogo con todos los instrumentos y sus
     KPIs resumidos, indicando que el detalle requiere seleccionar uno.
4. **Compatibilidad con filtros de componentes hermanos**:
   - El banco de preguntas y la activación tienen estado propio. Para incluirlos
     en el mismo archivo con sus filtros activos, promueve sus propiedades de
     filtro a `#[Url]` (solo persistencia en URL, sin cambiar la lógica) o
     define un mecanismo equivalente determinista.
   - Si decides no incluirlos filtrados, el documento debe declarar el alcance
     real y no atribuir cifras filtradas a secciones que no las aplican.

## 5. Estructura obligatoria del `.md`

1. YAML front matter: `title`, `document_type`, `language: es-VE`,
   `generated_at` ISO8601, `source_url`, instrumento, lapso, plan, referente,
   filtros activos, `schema_version`.
2. Título y aviso de snapshot reproducible + cómo usarlo con un LLM.
3. "Cómo leer este documento".
4. Contexto del dominio: jerarquía académica, qué es un diagnóstico, sesión,
   respuesta, opción, competencia, indicador, grupo estable, área de formación,
   área de conocimiento, referente; glosario operativo.
5. Filtros aplicados + efecto real de cada filtro.
6. Metodología: fórmulas, denominadores, criterios de inclusión, fallbacks y
   reglas de negocio (activación, preguntas múltiples, etc.).
7. Catálogo de instrumentos (o ficha del seleccionado).
8. KPIs generales con definición y lectura.
9. Resumen general: sesiones recientes + distribuciones.
10. Por área de formación (tabla completa, no solo la página visible).
11. Por grado.
12. Por área de conocimiento (con la advertencia de solapamiento).
13. Banco de preguntas (resumen o detalle según selección).
14. Activación de áreas de formación.
15. Observaciones y alertas de datos (dinámicas).
16. Guía para el informe ejecutivo: preguntas, estructura y reglas de redacción
    (hecho / interpretación / recomendación; citar cifra y base; no causalidad
    sin evidencia; explicar ceros por ausencia de datos).
17. Apéndices: datos planos y trazabilidad técnica (tablas y modelos usados).
18. Nota de reproducibilidad (cómo regenerar el archivo).

## 6. Observaciones que el builder debe calcular dinámicamente

Incluye al menos:
- Instrumentos sin preguntas.
- Instrumentos con preguntas pero sin sesiones o sin respuestas.
- Precisión `null` por no haber respuestas de opción múltiple.
- Completitud `null`/0 por no haber sesiones.
- Áreas de conocimiento sin adscripción ("Sin área asignada") y solapamiento.
- Pensums activos para diagnóstico sin preguntas activas.
- Preguntas inactivas y preguntas sin opciones (o sin opción correcta).
- Sesiones contadas por fallback de pensum (pueden pertenecer a otro instrumento).
- Campos derivados (lapso/plan/referente) cuando el instrumento no los define.
- Comparación entre el catálogo global y el instrumento seleccionado, si aplica.

## 7. Privacidad y seguridad

- Excluir `diag_mains.token` siempre.
- No incluir `diag_answers.respuesta` (texto libre) ni datos sensibles.
- Para estudiantes, usa el nombre visible en la pantalla o el ID, y declara
  el criterio. No agregues correos, documentos ni contactos.
- El archivo es de solo lectura: no debe ejecutar escrituras ni disparar eventos
  de negocio.

## 8. Rendimiento

- Evita N+1: agrupa con `withCount`, `with`, colecciones y agregados.
- No repitas consultas por fila en el builder.
- Limita sesiones recientes a 5 (igual que la UI).
- Para tablas paginadas, exporta el conjunto completo del alcance, no solo la
  página visible, salvo que decidas lo contrario y lo declares.

## 9. Validación obligatoria antes de terminar

1. `php -l` de los archivos PHP modificados/creados.
2. Compila la vista Blade (sin escribir cachés de producción) y verifica que el
   HTML compilado sea PHP válido.
3. Genera el `.md` real en estos escenarios:
   - instrumento con datos completos,
   - instrumento sin sesiones/respuestas,
   - instrumento sin preguntas,
   - sin instrumento seleccionado (catálogo),
   - con filtros de plan/grado/pensum/búsqueda activos.
4. Valida que todas las tablas Markdown tengan el mismo número de columnas por
   bloque y que no haya pipes sin escapar.
5. Verifica el `StreamedResponse`: `Content-Type`, `Content-Disposition` y
   nombre de archivo.
6. Confirma que no cambió el comportamiento visible de la página ni las
   fórmulas existentes.
7. Limpia scripts temporales y no dejes logs modificados (`git checkout -- logs/`).
8. Reporta `git status` final, archivos tocados y evidencia de las pruebas.

## 10. Criterios de aceptación

- [ ] El botón aparece en la página y descarga un `.md` válido.
- [ ] El archivo cubre todas las secciones visibles de la página.
- [ ] Cada indicador tiene definición, fórmula y denominador explícito.
- [ ] Los filtros activos se reflejan o se declara su alcance real.
- [ ] El documento explica sus propias limitaciones y ceros por falta de datos.
- [ ] Sirve como insumo directo para un informe ejecutivo con pedagogía.
- [ ] No hay consultas en el builder ni N+1 en el payload.
- [ ] No se expone `token` ni texto libre de respuestas.
- [ ] No se tocaron BD, `.env`, migraciones, horarios ni assets compilados.
- [ ] Sin cambios de comportamiento fuera del nuevo botón y la exportación.

## 11. Fuera de alcance

- No rediseñes la UI ni reordenes tabs.
- No cambies fórmulas, filtros ni reglas de activación.
- No exportes la página `/diagnostico/referents` (es otra ruta).
- No agregues dependencias ni paquetes.
- No hagas commit ni despliegue.

## 12. Reporte final esperado

Entrega un resumen con:
1. Archivos creados/modificados y por qué.
2. Inventario UI → sección del `.md`.
3. Fórmulas implementadas y fallbacks documentados.
4. Decisiones tomadas sobre filtros de componentes hermanos.
5. Evidencia de validación (tamaño del archivo, escenarios probados, checks).
6. Riesgos o deuda técnica detectada.

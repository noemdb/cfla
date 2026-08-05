# Plan de Implementación: Rol `is_director` (Dirección / Supervisión Ejecutiva)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-08-04

> **Nota de arranque:** este documento sigue exactamente el mismo patrón (phases,
> cadena de modelos, servicios de scope, ADRs, checklist de rollback) empleado en
> `blueprint/coordinacion/implementations.md` y `blueprint/leadership/implementations.md`.
> La diferencia esencial es que el rol `is_director` es **100% de solo lectura**:
> **no aprueba, no rechaza, no registra comentarios ni observaciones** — solo realiza
> **visualización y seguimiento** en toda la institución.

---

## Tabla de Contenidos

- [⚙️ Instrucciones de uso en loop de agente IA (LEER PRIMERO)](#%ef%b8%8f-instrucciones-de-uso-en-loop-de-agente-ia-leer-primero)
- [Checkpoint de Progreso](#checkpoint-de-progreso)
- [Resumen Ejecutivo](#1-resumen-ejecutivo)
- [Arquitectura Actual (AS-IS)](#2-arquitectura-actual-as-is)
- [Cadena de Modelos](#3-cadena-de-modelos)
- [Target (TO-BE)](#4-target-to-be)
- [Estrategia de Implementación](#5-estrategia-de-implementación)
- [Plan Detallado](#6-plan-detallado)
    - [Fase 1: Base de Datos y Modelo](#fase-1-base-de-datos-y-modelo)
    - [Fase 2: Middleware y Autorización](#fase-2-middleware-y-autorización)
    - [Fase 3: Servicios y Scope](#fase-3-servicios-y-scope)
    - [Fase 4: Rutas](#fase-4-rutas)
    - [Fase 5: Livewire Components](#fase-5-livewire-components)
    - [Fase 6: Navegación y Vistas](#fase-6-navegación-y-vistas)
    - [Fase 7: Seguridad y Validación (Read-Only Enforcement)](#fase-7-seguridad-y-validación-read-only-enforcement)
    - [Fase 8: Testing](#fase-8-testing)
- [ADRs (Architecture Decision Records)](#7-adrs)
- [Dependencias y Roadmap](#8-dependencias-y-roadmap)
- [Checklist de Rollback](#9-checklist-de-rollback)

---

## ⚙️ Instrucciones de uso en loop de agente IA (LEER PRIMERO)

Este documento es el **plan máquina-ejecutable** para crear el rol `is_director`.
Un agente IA debe consumirlo de forma **secuencial y verificada**, nunca en paralelo.

### Reglas del loop (contrato de ejecución)

1. **Orden estricto de fases.** Ejecuta las fases en el orden numérico.
   Cada fase declara sus **prerrequisitos** (`Verif pre`) y su **criterio de meta**
   (`Verif post`). No avances a la siguiente fase hasta que la anterior pase
   todos sus checks.

2. **Ruta absoluta de archivos.** Crear/editar SOLO los archivos listados en
   `Archi` de cada fase. No modifiques nada fuera de la lista. Todas las rutas
   son relativas a la raíz del repo `/home/nuser/code/cfla/`.

3. **Bucle de verificación (do + check).** Después de escribir (o editar)
   los archivos de una fase, ejecuta los comandos de `Verif post`. Si algo
   falla, corrige dentro dela misma fase antes de seguir. Criterio de parada:
   `php artisan test` debe pasar en verde a nivel global al concluir Fase 8.

4. **No escribas al rodar.** La regla más importante del rol: `is_director` es
   **de solo lectura**. Si al implementar detectas que una ruta no es `GET`,
   que un servicio expone un método `save*`/`approve*`/`comment*`, o que una
   vista contiene un `<form>` de escritura, **PARE** y revierte ese cambio.

5. **Registro de progreso.** Al terminar cada fase, marca la casilla en la
   sección [Checkpoint de Progreso](#checkpoint-de-progreso) y anota el resultado.
   Conserva ese bloque (no lo borres en la siguiente iteración).

6. **Handling de fallos.** Si una verificación falla: (a) lee el error,
   (b) corrige el archivo de la fase, (c) re-ejecuta la verificación,
   (d) solo entonces sigue. Nunca saltes una verificación roja con un FIXME.

7. **Referencias de código existente (leer antes de copiar).** Para mantener
   consistencia, lee los archivos espejo antes de crear los del director:
   - `app/Http/Middleware/IsLeadership.php` (base para `IsDirector.php`)
   - `app/Services/Lms/CoordinacionScopeService.php` (base para `DirectorScopeService.php`)
   - `app/Livewire/Leadership/IndicatorDashboard.php` (base para los componentes)
   - `routes/web.php` (grupo `leadership.*` y `coordinacion.*` para insertar `director.*`)
   - `app/Models/User.php` (helpers `isLeadership()`, `getIsDirectorAttribute`)

### Anatomía por fase

Cada fase tiene este encabezado, que el agente debe leer como contrato:

```
### Fase N: <titulo>

> Archivos: <lista de rutas a crear/editar>
> Accion: <actividad que realiza el agente>
> Verif pre: <checks previos a la fase>
> Verif post: <checks de meta de la fase; comandos y/o output esperado>
```

---

## Checkpoint de Progreso

> Copia este bloque al final del documento y mantenlo actualizado en cada loop.
> El agente lo lee al inicio para saber desde dónde retomar.

| Fase | Estado | Fallos / Notas | Hito de verificación clave |
|------|--------|----------------|----------------------------|
| 1. Migración + Modelo | ✅ | Migración `2026_08_04_000001_add_is_director_to_users_table` corrió con `--force`; fillable/casts fieles. `property_exists('is_director')` = `missing` por ser atributo mágico Eloquent (no es fallo). | `migrate` OK + `User::factory()->director()` existe (pendiente Fase 8) |
| 2. Middleware | ✅ | `php -l` OK; alias `isDirector` en `Kernel.php:82`. Prueba 200/403 queda cubierta por Fase 8. | 200 para director, 403 para no-director (test en Fase 8) |
| 3. Servicio Scope | ✅ | `DirectorScopeService` (103 líneas) sin métodos `save*`/`store*`/etc. + lint OK; `assertCanSupervise()` aborta 403. | `DirectorScopeService` sin métodos save*, global |
| 4. Rutas | ✅ | 9 rutas `GET|HEAD` bajo `/app/director` verificadas con `route:list --name=director` (bloque routes/web.php:317–356). Verificación quedó desbloqueada al crear las clases de Fase 5 (dependencia cruzada del spec). Grep literal `director/` sin match: el prefijo usa `prefix('director')`, no path literal. | `director.*` registradas, todas GET |
| 5. Livewire Components | ✅ | 7 clases creadas (verbatim spec, líneas 736–1194). `php -l` OK; sin métodos `save*`/`store*`/`editObservations`/`cancelEdit`. Render confirmado por smoke test Fase 6: las 7 rutas → 200. | 7 componentes renderizan sin error |
| 6. Nav y Vistas | ✅ | Navbar desktop+mobile con prefijo `app.`, layout `director/layouts/app.blade.php`, 7 vistas read-only. Verif post: grep `<form|wire:submit|method="post"` → sin salida. Smoke test 7 rutas GET → 200. Bug fijo: `route()` sin prefijo `app.` en activity-list; `'activity.topic'` removido de `with()` (columna, no relación). | navbar muestra submenú Dirección |
| 7. Seguridad read-only | ✅ | `DirectorReadOnlyTest.php`: reflexión sobre `DirectorScopeService` (sin save/update/store/create/approve/comment/observe...) + auditoría de rutas registradas (sin POST/PUT/PATCH/DELETE) → 2 passed (48 aserciones). Verif post `route:list --name=director`: 9 rutas `GET|HEAD`, cero verbos no-GET. Nota: el grep literal del spec `\sGET\s` no matchea `GET|HEAD` (GET va seguido de `|`), se audita con `grep -iE "POST|PUT|PATCH|DELETE"` → sin salida. | test de reflexión sin métodos save* |
| 8. Testing | ✅ | Estado `director()` en UserFactory (spec §8.3 verbatim); DirectorMiddlewareTest ×3 (200 / 403 / admin-bypass vía `getIsDirectorAttribute`); DirectorScopeTest ×4 (queryPensums sin filtro, queryProfesores activos-con-carga, factory state, role_label 'Dirección'); DirectorDashboardTest ×3 (KPIs de toda la institución, carga académica, profesores); DirectorReadOnlyTest ×3 (+ vista activity-list sin `<form>` ni `wire:click`). Suite completa: 216 passed, 0 failed, sin SKIP. | 13 tests, suite verde global |

**Siguiente fase a ejecutar:** Mejora de interfaz (onda 2) — toggle Grid/Tabla + paginación aplicados a `carga-academica` (ver §5.3 Vista), `activities` (ver §5.4 Vista) y `lesson-list` (ver §5.5 Vista); panel de filtros ampliado en Actividades con filtros de estado eliminados por decisión del usuario (ver §5.4 Filtros); grupo de botones en la columna Acciones de Actividades (ver §5.4 Vista); columna Estado de la actividad (ver §5.4 Vista); toggle Grid/Tabla + panel de filtros ampliado en Lecciones (ver §5.5 Vista). Pendiente: evaluar el resto de vistas del director (`pensums`, `recursos`, `profesores`, dashboard).

---

### Onda 2 — Mejoras de interfaz

> Trabajo posterior a la implementación funcional (fases 1-8). Cada ajuste se documenta
> en su sección (`§5.x`) y se registra aquí en el Checkpoint.

| # | Cambio | Estado | Notas |
|---|--------|--------|-------|
| 2.1 | Toggle Grid/Tabla en Carga Académica (`carga-academica-list.blade.php`) | ✅ | Key `carga-academica-view-mode`, evento `carga-academica-view-mode-changed`, default `table`. Documentado en §5.3 Vista. |
| 2.2 | Toggle Grid/Tabla en Lecciones (`lesson-list.blade.php`) | ✅ | Key `lessons-view-mode`, evento `lessons-view-mode-changed`, default `table`. Se incorporó además el panel de filtros ampliado (Búsqueda/Plan Estudio/Profesor/Grado/Sección/Lapso) y el `<x-pagination-wrapper :paginator="$lessons" />` dentro de cada bloque `x-show`. `$paginate = 15` + `updatingPaginate()`. Documentado en §5.5 Vista. |
| 2.3 | Toggle Grid/Tabla en Actividades (`activity-list.blade.php`) | ✅ | Key `director-activities-view-mode`, evento `director-activities-view-mode-changed`, default `table`. Documentado en §5.4 Vista. |
| 2.4 | Filtros ampliados en Actividades (`activity-list.blade.php` + `ActivityList.php`) | ✅ | Panel de filtros del módulo Planning adaptado a `Activity` (read-only): Plan Estudio/Profesor/Grado/Sección/Lapso. **Revisión (decisión del usuario):** los filtros de estado (Observaciones, En revisión, Estado segmented) se añadieron en 2.4 y luego se eliminaron — quedan solo los 5 context + Búsqueda + Lapso. PHP limpiado de props/condiciones/hooks de estado. Documentado en §5.4 Filtros. |
| 2.5 | Grupo de botones en columna Acciones (`activity-list.blade.php`) | ✅ | Enlaces planos Formato/Resumen → botones agrupados (patrón Planning/Leadership: `inline-flex items-center rounded-lg overflow-hidden border divide-x` + `role="group"`). Dos `<a>` GET read-only a `app.director.activities.format` / `app.director.activities.resume` con icono + etiqueta y tint de hover sky/purple. Aplicado igual en Grid y Tabla. Documentado en §5.4 Vista. |
| 2.6 | Columna Estado de la actividad (`activity-list.blade.php` + `ActivityList.php`) | ✅ | Nueva columna "Estado" (Tabla, entre Lapso y Acciones; `colspan` vacío 6→7) y badges de estado en cada card del Grid. Muestra dos estados independientes renderizados en puro Blade (`@if`/`@elseif`, sin `wire:click`/`<form>`): **Aprobación del Jefe de Área** (`$activity->status` boolean: `true`→Aprobada emerald con check, `false`→En revisión amber con X, guard `!== null`→Sin aprobar gris) y **Lección** (`$activity->lmsPublication->status`: `PUBLISHED`→Lección aprobada emerald, `SCHEDULED`→Lección programada sky, resto/null→Lección pendiente gris). Se agregó `lmsPublication` al eager-load de `ActivityList::render()` (anti N+1). Marcup de badges fiel a `leadership/activity-overview.blade.php`. Documentado en §5.4 Vista. |
| 2.7 | Ver/Imprimir lecciones LMS (`lesson-list.blade.php` + `LessonsPrintController.php` + `lessons-print.blade.php`) | ✅ | Botón "Ver / Imprimir" en la barra del listado (junto al toggle Grid/Tabla): `<a href>` plano GET (sin `wire:click`, compatible read-only) hacia `app.director.lessons.print` con los filtros activos como query string. Página de impresión HTML autónoma (misma semántica de filtros que LessonList + filtro `profesor`, orden `finicial desc`): la dirección supervisa TODA la institución, muestra el responsable de cada lección, y renderiza Mermaid/KaTeX en el navegador (mismo motor que el profesor, §5.5 del módulo profesor). Ruta nueva `/app/director/lecciones/print` GET-only. Bug fijo: nombre de ruta sin prefijo `app.` en el enlace (`route('director.lessons.print')` → `RouteNotFoundException`, ahora `route('app.director.lessons.print')`). Documentado en §5.5 Impresión; 15 tests en `DirectorLessonsPrintTest`. |
| 2.7.1 | Bug impresión: tarda + diagramas Mermaid en blanco en el PDF (`lessons-print.blade.php` director y profesor + `resources/js/lms-student-preview.js`) | ✅ | El `handlePrint()` original esperaba a que **cada** `[x-ref="target"]` tuviera `<svg>` (poll 200 ms, cap 10 s): (a) un diagrama con error de render nunca tiene `<svg>` → la espera corría siempre los 10 s completos ("tarda"); (b) con muchos diagramas (`?pestudio=1&grado=5`) el render superaba los 10 s → `window.print()` disparaba prematuro y el PDF salía con SVGs en blanco ("no muestra bien"). **Fix:** `mermaidEmbed.render()` marca estado terminal por wrapper (`data-mermaid-state` = `rendering`/`ok`/`error`, `ok` tras `setupUI()` para que el SVG ya esté escalado); `handlePrint()` espera a que TODOS los `[data-mermaid-code]` lleguen a `ok`/`error` (poll 150 ms, timeout 30 s solo si el chunk Mermaid no carga), con progreso `(done/total)` en el botón. Además `.mermaid-wrap svg` en `@media print` gana con `!important` (`max-width:100%; height:auto; overflow:visible`) al `style="max-width:<naturalWidth>px"` inline del SVG, evitando desbordes de la columna. Aplicado en lockstep a la vista del profesor. Ver §5.5.1; 28 tests de impresión (director+profesor) verdes + build Vite OK. |
| 2.7.2 | Defensa en profundidad vs. diagramas Mermaid desbordados en el PDF (`LessonWizard.php` + `lessons-print.blade.php` director/profesor + `lms-student-preview.js`) | ✅ | El PDF `LeccionesLMSResult010.pdf` mostraba un diagrama (contenido id 296: `graph TD`, 13 nodos, ~9 niveles, labels de hasta ~48 chars en una sola línea + 3 `style`) que desbordaba la columna de ~450px en las primeras páginas. Tres causas raíz: (1) `strip_tags()` destruía los `<br/>` de los labels multi-línea → el texto se concatenaba en una sola línea larga; (2) los prompts del wizard no limitaban nodos/profundidad/longitud de labels; (3) el CSS de impresión pre-2.7.1 no ganaba al `style` inline del SVG (ya fijo). **6 capas implementadas (todas aprobadas por el usuario):** **A1** conserva `<br/>` con `html_entity_decode(strip_tags($code,'<br><br/>'))` en la extracción de la vista de impresión Y en el flujo de guardado del wizard (`saveLesson` + extracción de embed), porque el `strip_tags` del SAVE ocurría ANTES de imprimir; **B1** `useMaxWidth: true` en ambos `mermaid.initialize()`; **B2** `break-inside:avoid` + `page-break-inside:avoid` en `.mermaid-wrap`; **E1** heurística por diagrama (`nodos≥12 || flechas≥11 || línea>55 chars`) → clase `mermaid-wide` con `column-span:all` para que el diagrama ancho cruce toda la página; **C1** endurece los prompts (sección + embed): máx. 12 nodos / 11 flechas / 3 niveles, IDs cortos, labels ≤30 chars por línea con `<br/>`, agrupar secundarios en nodo resumen, evitar `style`; **D1** validación post-generación (`validateMermaidDiagram()`: >14 nodos, >16 flechas, label >30 chars) con un único reintento a temperatura 0.3 + feedback (`diagramCorrectionBlock()`), vía helper `callMermaidModel()` (misma cadena de 3 modelos del flujo de diagramas), en los DOS flujos del wizard (`generateSlideDiagram()` y `generateEmbedCard()`, sin forzar `graph TD` en embeds). Refactor: extracción limpia en `extractMermaidCodeFromRaw()` y post-procesado unificado en `postProcessMermaid()` (graph TD + split de labels largos). Ver §5.5.2; 63 tests `LessonWizardCharacterizationTest` verdes + invariante read-only director OK. |
| 2.7.3 | Acotar ALTURA de diagramas Mermaid grandes a una página en el PDF (`lessons-print.blade.php` director/profesor) | ✅ | El PDF `LeccionesLMSResult011.pdf` mostraba el diagrama "El misterio de los símbolos perdidos" (contenido id 296: `graph TD`, 13 nodos, ~9 niveles) que, tras la capa E1 (2.7.2), ya cruzaba todo el ancho de la página como spanner pero era demasiado ALTO: ocupaba más de una página en vertical. **Fix (solo CSS de impresión, aplicado en lockstep a la vista del profesor):** capa de altura **F1** — `max-height` de escala en `.mermaid-wrap svg` dentro de `@media print` que reduce el SVG a la página conservando la proporción del `viewBox` (max-width ya lo anclaba al ancho). Cascada deliberada `pt`/`vh`: se declara `max-height:<n>pt !important` (fallback universal, inambigüo en print) y después `max-height:<m>vh !important` (gana si el motor soporta `vh` en print y se adapta al tamaño real del papel). Dos umbrales: `430pt`/`70vh` para diagramas en columna (no superan la columna de ~453pt) y `515pt`/`83vh` para `.mermaid-wide` (caben en la página de ~561pt de alto de contenido, menos marco/etiqueta). **Enmarcado reforzado:** `overflow:hidden` en `.mermaid-wrap` como red de seguridad (recorta si el escalado no llegara a aplicarse), `border-color:#94a3b8` (marco más visible) y etiqueta `DIAGRAMA · VISTA AMPLIA` vía `::before` en `.mermaid-wide` (barra superior que hace explícito que el diagrama está acotado). El `::before` y el `position:relative`/`overflow:hidden` viven dentro de `@media print`, así que la previsualización en pantalla (zoom/toolbar) no se ve afectada. Ver §5.5.3; 17 tests `DirectorLessonsPrintTest` (incluye el nuevo de regresión `director_print_bounds_tall_mermaid_diagrams_to_one_page`) + 28 `LessonsPrintTest` + 3 `DirectorReadOnlyTest` verdes. |
| 2.7.4 | Acotar ANCHO de diagramas Mermaid grandes a media página en el PDF (`lessons-print.blade.php` director/profesor) | ✅ | Continuación del bug 2.7.3 (mismo contenido id 296, "El misterio de los símbolos perdidos"): tras acotar la ALTURA, el diagrama cabía en una página pero, como spanner E1 (`column-span:all`), seguía ocupando el ANCHO completo de la página horizontal (~790 pt) — demasiado ancho; el usuario pidió acotarlo a "máximo media página". **Fix (solo CSS de impresión, espejo de F1):** capa de ancho **G1** — `max-width` de escala en `.mermaid-wrap.mermaid-wide` dentro de `@media print` que limita el diagrama amplio a media página y lo centra (`margin-left/right:auto`). Cascada deliberada `pt`/`vw` (mismo patrón que F1): primero `max-width:350pt !important` (fallback universal, inambigüo en print) y después `max-width:50vw !important` (gana si el motor soporta `vw` en print y se adapta al ancho real del papel; 50vw = media página del page box). **Doble tope:** en el marco `.mermaid-wrap.mermaid-wide` Y en el `svg` interno — red de seguridad por si el motor no honrara `max-width` sobre un spanner de `column-span:all`. Los diagramas en columna ya caben en media página (columna ≈383pt < 50vw ≈421pt), así que el tope solo actúa sobre los amplios. Previsualización en pantalla: el `.mermaid-wrap` base gana `max-width:50vw` para mostrar el mismo tope que tendrá la impresión. Ver §5.5.4; 18 tests `DirectorLessonsPrintTest` (incluye el nuevo de regresión `director_print_bounds_mermaid_width_to_half_page`) + 28 `LessonsPrintTest` + 3 `DirectorReadOnlyTest` verdes. |
| 2.7.5 | Botón "Ver / Imprimir" en el monitor LMS de Planificación + ruta `/app/planning/lms/print` (`monitor.blade.php` + `routes/web.php` + reuso de `LessonsPrintController`) | ✅ | Mismo botón "Ver / Imprimir" que el listado de la Dirección (2.7) en el monitor LMS de Planificación (`/app/planning/lms/monitor`, componente `LmsMonitor`): `<a href>` plano GET (sin `wire:click`, compatible read-only) hacia la ruta nueva `app.planning.lms.print` con los filtros activos del monitor (`pestudio/grado/seccion/profesor/asignatura/status/search`, vacíos descartados con `array_filter`) como query string, `target="_blank"`, misma clase teal e icono de impresora. **Reuso cross-módulo de `Director\LessonsPrintController`** (patrón espejo del ADR-005, que reusa `Planning\ActivityPdfController` en dirección): el mismo controlador sirve `/app/director/lecciones/print` (grupo `isDirector`) y `/app/planning/lms/print` (grupo `isPlanner`). Un planificador NO puede llegar a la ruta de la Dirección (`IsDirector` solo admite `is_director`) y viceversa (`IsPlanner` aborta 403), verificado por tests. El contexto del membrete se deduce del nombre de ruta (`str_contains($request->route()?->getName() ?? '', 'planning')`) → contexto `Planificación · Monitor LMS` y título `PLANIFICACIÓN · LECCIONES LMS · CONTENIDO COMPLETO`; el filtro `estado` (PUBLISHED/SCHEDULED/ARCHIVED) y el de `asignatura` se añadieron al controlador para el monitor (misma semántica que `LmsMonitor`). Ver §5.5.5 + ADR-006; 8 tests `LmsPrintTest` (acceso + membrete + filtros + botón con filtros activos + `target="_blank"` + read-only) + 33 tests de las suites director verdes. |
| 2.7.6 | Enmarcar diagramas Mermaid DENTRO de su columna: eliminar el spanner E1 `column-span:all` y la heurística `mermaid-wide` (`lessons-print.blade.php` director/profesor + `DirectorLessonsPrintTest.php`) | ✅ | Bug 2.7.5 (usuario): los diagramas seguían ocupando mucho espacio porque la capa E1 (2.7.2) los sacaba del flujo de 2 columnas con `column-span:all` para cruzar la página como spanner; G1 (2.7.4) solo acotaba su ancho a media página, no los devolvía a la columna. **Fix:** se elimina por completo el mecanismo de diagrama ancho — regla CSS `.mermaid-wrap.mermaid-wide{column-span:all;-webkit-column-span:all}`, etiqueta `DIAGRAMA · VISTA AMPLIA` (::before), tope de altura wide `515pt/83vh`, topes de ancho `350pt/50vw` (marco + svg), la previsualización en pantalla `max-width:50vw` y la heurística Blade `$mermaidWide` (nodos≥12 \|\| flechas≥11 \|\| línea>55 chars). Todo diagrama queda **enmarcado en su columna**: el marco `.mermaid-wrap` (fondo/borde/padding) + `max-width:100% !important; height:auto !important` en el svg limitan el ancho al de la columna (~383pt) y `max-height:430pt/70vh` (J2, 2.7.3) limita el alto — el tope de columna es ahora el ÚNICO tope de altura. Aplicado en lockstep a la vista del profesor. Tests: `director_print_bounds_tall_mermaid_diagrams_to_one_page` recortado (sin 515pt/VISTA AMPLIA) y `director_print_bounds_mermaid_width_to_half_page` **renombrado** a `director_print_frames_mermaid_diagrams_within_column` (afirma marco + `max-width:100%` y ausencia de `column-span:all`/`mermaid-wide`/`VISTA AMPLIA`). Ver §5.5.6; 41 tests de impresión + read-only verdes (18 `DirectorLessonsPrintTest` + 12 `LessonsPrintTest` + 8 `LmsPrintTest` + 3 `DirectorReadOnlyTest`). |

---

## 1. Resumen Ejecutivo

### ¿Qué es el rol `is_director`?

El **Director** es un rol de **supervisión y seguimiento ejecutivo a nivel institucional**.
Es un observador **de solo lectura** que puede visualizar la información académica de
**toda la institución** (todos los `Peducativo`, todas las áreas, todas las cargas académicas,
todas las actividades y lecciones LMS) para hacer seguimiento, pero **sin ninguna capacidad
de escritura**: no crea, edita, elimina, aprueba, rechaza, comenta ni registra observaciones.

Es el rol más restringido en cuanto a *escritura* de todos los de seguimiento:
- `coordinacion` → puede registrar `pevaluacion.observations`.
- `leadership` → puede comentar y aprobar/rechazar actividades de sus áreas.
- `is_director` → **ninguna acción de escritura**. Solo lee y hace seguimiento.

### Las responsabilidades del rol (todas read-only)

| # | Responsabilidad | Modelos involucrados | Capacidad |
|---|----------------|---------------------|-----------|
| 1 | **Ver sus datos / perfil** | `User` → `Profile` | Perfil visible (sin edición propia del rol) |
| 2 | **Dashboard institucional** | `Peducativo` → `Pestudio` → KPIs globales | Indicadores de toda la institución (sin filtro de scope) |
| 3 | **Información académica: Pensums** | `Peducativo` → `Pestudio` → `Pensum` → `Asignatura` | Listar pensums de toda la institución |
| 4 | **Carga Académica** | `Peducativo` → `Pestudio` → `Pensum` → `Pevaluacion` | Visualizar pevaluacions (año lectivo, profesor, sección) |
| 5 | **Actividades de Planificación** | `Activity` → `Pevaluacion` | Visualizar formato/resumen (PDF) — sin editar observaciones |
| 6 | **Lecciones LMS** | `Activity` → `LmsActivityPublication/Section/Resource/Link` | Visualizar contenido publicado |
| 7 | **Recursos compartidos** | `LmsActivityResource` | Listado de recursos descargables |
| 8 | **Seguimiento docente (KPIs)** | `Profesor` → KPIs (IEE, IRE) | Visualizar métricas de desempeño docente |

> **Restricción clave (requisito del usuario):** el director **no aprueba, no rechaza,
> no registra comentarios ni observaciones**. Por tanto el módulo Director **no expone
> ningún endpoint de escritura** y sus vistas **no contienen formularios** de ninguna clase.

### Principio de diseño

> **Namespace propio, reuso de lógica, alcance global y read-only estricto.** Igual que
> `coordinacion` y `leadership`, el módulo Director tiene su propio namespace completo
> (rutas, layout, componentes, vistas, servicio). La diferencia con los otros dos:
>
> 1. **Alcance global**: el director ve **todos** los `Peducativo` / `Pensum` / `Pevaluacion` /
>    `Activity` / `Profesor` de la institución (efectivamente "sin restricción"), porque la
>    dirección debe supervisar el conjunto, no un subconjunto. Se implementa con el mismo
>    "bypass" que se usa para `is_admin` dentro de `LeadershipService` (ver ADR-002).
> 2. **Read-only estricto**: no se registra ningún controlador/acción de escritura, no hay
>    estados editables en las vistas, y las rutas de PDF se sirven protegidas solo por GET.
> 3. **Reuso**: el servicio Director reusa la lógica de scope/key de los módulos Planning y
>    Lms, pero siempre en modo "sin filtro", y nunca invoca métodos de escritura.

---

## 2. Arquitectura Actual (AS-IS)

### Modelo de roles actual (impreso y verificado en el código)

| Columna `users` | Middleware | Rutas que protege |
|----------------|-----------|-------------------|
| `is_admin` | `IsAdmin` | `/admin/*` (logs, DB backup) |
| `is_admin` / `is_diagnostic` | `IsAdminOrDiagnostic` | `/admin/*` (users, voting, educational) |
| `is_admin` / `is_planner` / `is_diagnostic` | `IsPlanner` | `/app/planning/*` (todos los CRUDs) |
| `is_coordinacion` (+ admin bypass) | `IsCoordinacion` | `/app/coordinacion/*` (read + observations) |
| `is_leadership` (+ admin heredado) | `IsLeadership` | `/app/leadership/*` (read + comentar/aprobar) |
| `is_profesor` / `is_admin` | `IsProfesor` | `/app/profesors/*` |
| `is_student` | `IsStudent` | `/app/estudiante/*` |

### Relaciones existentes que reusamos

```php
// Peducativo ya tiene manager_id → User (para los otros roles, NO es ancla aquí)
class Peducativo extends Model {
    public function pestudios() { return $this->hasMany(Pestudio::class); }
}

// Pestudio → Pensum → Pevaluacion → Activity
class Pestudio extends Model {
    public function pensums() { return $this->hasMany(Pensum::class); }
}
class Pensum extends Model {
    public function pevaluacions() { return $this->hasMany(Pevaluacion::class); }
}
class Pevaluacion extends Model {
    public function activitys() { return $this->hasMany(Activity::class); }
    public function lapso() { return $this->belongsTo(Lapso::class); }
    public function profesor() { return $this->belongsTo(Profesor::class); }
    public function seccion() { return $this->belongsTo(Seccion::class); }
    public function pensum() { return $this->belongsTo(Pensum::class); }
}

// Activity → LMS
class Activity extends Model {
    public function lmsPublication() { return $this->hasOne(LmsActivityPublication::class); }
    public function lmsSections() { return $this->hasMany(LmsActivitySection::class); }
    public function lmsResources() { return $this->hasMany(LmsActivityResource::class); }
    public function lmsLogs() { return $this->hasMany(LmsActivityLog::class); }
}

// AreaConocimiento → CampoConocimiento → Asignatura (para seguimiento docente)
class AreaConocimiento extends Model {
    public function campo_conocimientos() { return $this->hasMany(CampoConocimiento::class); }
}
```

### Lo que NO existe (necesario para el rol `is_director`)

| Aspecto | Estado | Detalle |
|---------|--------|---------|
| Columna `is_director` | ❌ **Falta** | Migración pendiente |
| Middleware `IsDirector` | ❌ **Falta** | Similar a `IsLeadership` con `is_director` + admin bypass |
| `getRoleLabelAttribute` → 'Dirección' | ❌ **Falta** | Agregar en User model |
| `DirectorScopeService` | ❌ **Falta** | Permite visualización global (sin filtros) |
| Rutas `/app/director/*` | ❌ **Falta** | Grupo nuevo de rutas (GET only) |

> **Nota:** A diferencia de `coordinacion`, NO se requiere un campo tipo `manager_id` como
> ancla, porque el director supervisa toda la institución (ver ADR-002).

---

## 3. Cadena de Modelos

### Árbol de navegación (alcance global, read-only)

```
User (is_director = true)
  │   → sin filtro de scope: ve TODA la institución
  │
  ├── Peducativo (todos)
  │     └── Pestudio (todos, planning_module = 1, status_active)
  │           ├── Pensum (todos)
  │           │     └── Pevaluacion (todos)
  │           │           ├── Activity (todas)
  │           │           │     ├── Formato/Resumen PDF
  │           │           │     ├── LmsActivityPublication → Sections/Resources/Links
  │           │           │     └── LmsActivityResource (is_visible = true)
  │           │           ├── Lapso
  │           │           ├── Seccion → Grado
  │           │           └── Profesor → KPIs (IEE, IRE)
  │           │
  ├── AreaConocimiento (todas) → CampoConocimiento → Asignatura → Pensum
  └── Profesor (todos activos) → KPIs docentes
```

### Traducción a queries (todas read-only)

```sql
-- Todo el scope del director es "sin restricción".
-- Los métodos siguientes existen para mantener simetría con los otros roles,
-- pero DEVSOLVER siempre en modo "unrestricted".

SELECT * FROM peducativos WHERE status_active = 'true';

SELECT * FROM pestudios
WHERE status_active = 'true' AND planning_module = 1;

SELECT * FROM pensums (todos);

SELECT * FROM pevaluacions (todas);

SELECT * FROM activities (todas);
```

---

## 4. Target (TO-BE)

### Nuevo modelo de roles

```
users.is_director  →  middleware IsDirector  →  /app/director/*
                                                     ├── /                  → director.index (Dashboard con indicadores globales)
                                                     ├── /pensums           → director.pensums
                                                     ├── /carga-academica   → director.carga-academica
                                                     ├── /activities        → director.activities
                                                     ├── /activities/format/{pevaluacion} → director.activities.format (PDF GET)
                                                     ├── /activities/resume/{pevaluacion} → director.activities.resume (PDF GET)
                                                     ├── /lecciones         → director.lessons
                                                     ├── /recursos          → director.resources
                                                     └── /profesores        → director.profesores (KPIs docentes)
```

### Jerarquía de middleware

```
is_admin ──► pasa TODOS los middleware (including IsDirector)   [bypass]
is_director ──► pasa IsDirector                                  [NUEVO, independiente de planner]
otros ──► 403
```

### Principio de herencia (patrón existente)

```php
// Igual que getIsLeadershipAttribute
public function getIsDirectorAttribute()
{
    return $this->is_admin || ($this->attributes['is_director'] ?? false);
}
```

---

## 5. Estrategia de Implementación

### Decisión arquitectónica clave

El rol `is_director` es un **módulo completamente independiente** con su propio namespace,
siguiendo el patrón de `coordinacion`:

1. **Rutas propias** bajo `/app/director/*` con middleware `IsDirector` (solo `GET`).
2. **Layout dedicado** `director.layouts.app` con navbar propio.
3. **Componentes Livewire dedicados** en `App\Livewire\Director\*` que envuelven las
   consultas de solo lectura.
4. **Vistas Blade propias** en `resources/views/director/` y `resources/views/livewire/director/`.
5. **Servicio propio** `DirectorScopeService` (métodos `query*()` read-only; sin métodos de escritura).
6. **PDF reusados** de `ActivityPdfController` existente (es read-only y requiere GET).

### Diferencia clave con `coordinacion`/`leadership`

| Aspecto | `coordinacion` | `leadership` | **`is_director`** |
|--------|----------------|--------------|-------------------|
| Alcance/scope | Su `manager_id` | Sus áreas de conocimiento | **Toda la institución** (global) |
| Observaciones | ✅ edita `pevaluacion.observations` | ve pero no es su acción | ❌ **no aplica** |
| Comentarios en actividades | ❌ | ✅ comenta | ❌ **no aplica** |
| Aprobar/rechazar | ❌ | ✅ aprueba/rechaza | ❌ **no aplica** |
| KPIs docentes | parcial (counts) | ✅ dashboard | ✅ dashboard global |

### Orden lógico (bloqueante en cascada)

```
Fase 1: Migration (is_director column) + User Model
    │
    ▼
Fase 2: Middleware IsDirector + Kernel
    │
    ▼
Fase 3: DirectorScopeService (global read-only)
    │
    ├──► Fase 4a: Rutas (/app/director/* — GET only)
    ├──► Fase 4b: Dashboard (indicadores globales)
    ├──► Fase 4c: Pensums (listado global)
    ├──► Fase 4d: Carga Académica (listado global)
    ├──► Fase 4e: Actividades (listado global + PDF)
    ├──► Fase 4f: Lecciones + Recursos (global)
    └──► Fase 4g: Profesores KPIs (global)
    │
    ▼
Fase 5: Navbar (director-items) + Layout dedicado
    │
    ▼
Fase 6: Testing
```

---

## 6. Plan Detallado

### Fase 1: Base de Datos y Modelo

> Archivos: `database/migrations/*add_is_director_to_users_table.php` (nuevo), `app/Models/User.php` (editar)
> Accion: crear la migración con guarda `hasColumn` y agregar `is_director` a `$fillable`, `$casts`, método `isDirector()` + accessor + `getRoleLabelAttribute`.
> Verif pre: verificar que `app/Models/User.php` ya tiene `is_leadership` para seguir el patrón.
> Verif post: `php artisan migrate` OK (columna e índice creados). `php -r "require 'vendor/autoload.php'; \$u = new \App\Models\User; echo property_exists(\$u,'is_director') ? 'ok' : 'missing';"` muestra `ok`.

#### 1.1 Migration — `add_is_director_to_users_table`

```php
<?php
// database/migrations/2026_08_04_000001_add_is_director_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_director')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_director')
                    ->default(false)
                    ->after('is_leadership')
                    ->comment('Dirección: supervisión y seguimiento de solo lectura');
                $table->index('is_director');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_director')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_director']);
                $table->dropColumn('is_director');
            });
        }
    }
};
```

**Patrón:** idéntico a la migración de `is_leadership`. Sin índices adicionales: como
el director ve todo (no filtra por columna propia), no se requieren índices nuevos en
la cadena de JOINs.

#### 1.2 User Model — cambios

```php
// app/Models/User.php

// En $fillable (agregar después de 'is_leadership'):
'is_director',

// En $casts (agregar):
'is_director' => 'boolean',

// Nuevo helper method (después de isCoordinacion/isLeadership):
public function isDirector(): bool
{
    return $this->is_director ?? false;
}

// Accessor (herencia admin, mismo patrón que is_leadership):
public function getIsDirectorAttribute()
{
    return $this->is_admin || ($this->attributes['is_director'] ?? false);
}

// Actualizar getRoleLabelAttribute — 'Dirección' justo después de 'Jefe de Área':
public function getRoleLabelAttribute()
{
    if ($this->is_admin) return 'Administrador';
    if ($this->is_director) return 'Dirección';
    if ($this->is_leadership) return 'Jefe de Área';
    if ($this->is_diagnostic) return 'Personal de Diagnóstico';
    if ($this->isCoordinacion()) return 'Coordinación';
    if ($this->is_planner) return 'Planificación';
    if ($this->isProfesor()) return 'Profesor';
    return 'Usuario Estándar';
}
```

> **Nota de ordenamiento de role_label:** se coloca `Dirección` antes de `Jefe de Área`
> porque un director no suele ser también jefe de área; en caso de conflicto manda el
> `is_director` (mayor vertical). Igual que el patrón en `getRoleLabelAttribute` de User.

---

### Fase 2: Middleware y Autorización

> Archivos: `app/Http/Middleware/IsDirector.php` (nuevo), `app/Http/Kernel.php` (editar)
> Accion: crear middleware `IsDirector` (usa `Auth::user()->is_director`) y registrarlo como alias `isDirector`.
> Verif pre: Fase 1 completo (`is_director` en modelo).
> Verif post: `php artisan route:list --name=director` aún NO muestra rutas (aún no creadas); pero puedes probar el middleware en el test de Fase 8. Compilar sin error: `php -l app/Http/Middleware/IsDirector.php`.

#### 2.1 Nuevo middleware `IsDirector`

```php
<?php
// app/Http/Middleware/IsDirector.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsDirector
{
    /**
     * Acepta admins y usuarios con is_director. A diferencia de los otros
     * roles de seguimiento, este módulo es 100% read-only: el middleware
     * solo protege la VISUALIZACIÓN, nunca acciones de escritura (no existen).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_director) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de dirección.');
    }
}
```

**Nota:** `Auth::user()->is_director` usa el accessor con herencia admin
(`getIsDirectorAttribute`), por lo que `is_admin` pasa automáticamente. No hace falta
escribir `|| is_admin` explícito, aunque hacerlo es inofensivo y más explícito.

#### 2.2 Registrar en Kernel

```php
// app/Http/Kernel.php — en $middlewareAliases (junto a los otros):
'isDirector' => \App\Http\Middleware\IsDirector::class,
```

#### 2.3 (Sin Policies de escritura)

Como el módulo no expone ninguna acción de escritura, **no** se definen policies
`update*`/`approve*` para el director. La seguridad se reduce a: middleware + rutas solo GET
+ protección de scope implícita (por ser global no requiere autorización por entidad).

---

### Fase 3: Servicios y Scope

> Archivos: `app/Services/Director/DirectorScopeService.php` (nuevo), `app/Livewire/Director/Concerns/HasDirectorScope.php` (nuevo)
> Accion: crear servicio con métodos `queryPeducativos|Pestudios|Pensums|Pevaluacions|Activities|Resources|Profesores` (global, read-only) + `assertCanSupervise()`. Crear trait `HasDirectorScope`.
> Verif pre: Fase 2 completo (middleware + alias).
> Verif post: NO debe existir método `save*`, `approve*`, `comment*`, `update*`, `store*`, `delete*`. Comprobación: `grep -nE "function (save|update|store|approve|reject|comment|observe|delete)" app/Services/Director/DirectorScopeService.php` → sin salida.

#### 3.1 `DirectorScopeService` — visualización global read-only

A diferencia de `CoordinacionScopeService` (filtrar por `manager_id`) o
`LeadershipService` (filtrar por áreas), el director **no filtra**: devuelve todas las
entidades activas de la institución. Se escriben métodos `query*()` *deliberadamente*
para que la intención "solo lectura global" quede explícita y testeable, y **NO** se
exponen métodos de mutación (`saveObservations`, `approve`, `comment`, `update`).

```php
<?php
// app/Services/Director/DirectorScopeService.php

namespace App\Services\Director;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scope de SOLO LECTURA para el rol Dirección.
 *
 * A diferencia de coordinacion (scoped por manager_id) y leadership (scoped
 * por áreas), la dirección supervisa TODA la institución: estos métodos no
 * filtran por el usuario, devuelven todas las entidades activas.
 *
 * ⚠️ REGLA DE ORO: este servicio NO contiene métodos que muten el estado
 * (save, update, approve, reject, comment, observe...). Si algún día la
 * dirección requiere una acción de escritura, se agregará con su propio
 * ADR y su propia guarda de autorización.
 */
class DirectorScopeService
{
    public function __construct(
        protected User $user
    ) {}

    /**
     * Todos los Peducativos activos (visión global de la dirección).
     */
    public function queryPeducativos()
    {
        return Peducativo::where('status_active', 'true')->orderBy('order');
    }

    /**
     * Todos los Pestudios activos con planificación habilitada.
     */
    public function queryPestudios()
    {
        return Pestudio::where('status_active', 'true')
            ->where('planning_module', 1);
    }

    /**
     * Todos los Pensums.
     */
    public function queryPensums()
    {
        return Pensum::query();
    }

    /**
     * Todas las Pevaluacions.
     */
    public function queryPevaluacions()
    {
        return Pevaluacion::query();
    }

    /**
     * Todas las Activities.
     */
    public function queryActivities()
    {
        return Activity::query();
    }

    /**
     * Recursos compartidos visibles.
     */
    public function queryResources()
    {
        return LmsActivityResource::where('is_visible', true);
    }

    /**
     * Profesores activos con carga académica (para KPIs docentes).
     */
    public function queryProfesores()
    {
        return Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions');
    }

    /**
     * Verifica que un usuario logueado tiene derechos de dirección.
     * Usado por los componentes como guarda de defensa en profundidad.
     */
    public function assertCanSupervise(): void
    {
        if (! $this->user->is_director) {
            abort(403, 'No tienes permisos de dirección para supervisar esta información.');
        }
    }
}
```

#### 3.2 Trait para Livewire components

```php
<?php
// app/Livewire/Director/Concerns/HasDirectorScope.php

namespace App\Livewire\Director\Concerns;

use App\Services\Director\DirectorScopeService;
use Illuminate\Support\Facades\Auth;

trait HasDirectorScope
{
    protected DirectorScopeService $directorService;

    public function initializeHasDirectorScope(): void
    {
        $this->directorService = app(DirectorScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getDirectorService(): DirectorScopeService
    {
        return $this->directorService;
    }
}
```

---

### Fase 4: Rutas

> Archivos: `routes/web.php` (editar — dentro del grupo `app`, junto a `leadership.*`)
> Accion: agregar grupo `Route::prefix('director')->middleware(['auth','isDirector'])->name('director.')` con 7 rutas GET + 2 PDF (reusar `ActivityPdfController`).
> Verif pre: Fase 3 completo (service + trait).
> Verif post: `php artisan route:list --name=director` muestra todas las rutas y TODAS son `GET`. `grep -nE "director/" routes/web.php` muestra el bloque.

#### 4.1 Grupo de rutas (GET only)

```php
// routes/web.php — DENTRO del grupo `app`, junto a coordinacion y leadership

// ─── Dirección: Supervisión y Seguimiento (READ-ONLY) ─────────
Route::prefix('director')
    ->middleware(['auth', 'isDirector'])
    ->name('director.')
    ->group(function () {

    // Dashboard con indicadores globales
    Route::get('/', \App\Livewire\Director\IndicatorDashboard::class)
        ->name('index');

    // Información Académica: Pensums
    Route::get('/pensums', \App\Livewire\Director\PensumList::class)
        ->name('pensums');

    // Carga Académica (Pevaluacions)
    Route::get('/carga-academica', \App\Livewire\Director\CargaAcademicaList::class)
        ->name('carga-academica');

    // Actividades de Planificación (SÓLO VISUALIZACIÓN + PDF)
    Route::get('/activities', \App\Livewire\Director\ActivityList::class)
        ->name('activities');
    Route::get('/activities/format/{pevaluacion}', [
        \App\Http\Controllers\Planning\ActivityPdfController::class, 'format'
    ])->name('activities.format');
    Route::get('/activities/resume/{pevaluacion}', [
        \App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'
    ])->name('activities.resume');

    // Lecciones LMS
    Route::get('/lecciones', \App\Livewire\Director\LessonList::class)
        ->name('lessons');

    // Recursos Compartidos
    Route::get('/recursos', \App\Livewire\Director\ResourceList::class)
        ->name('resources');

    // Seguimiento Docente (KPIs)
    Route::get('/profesores', \App\Livewire\Director\ProfesorIndicators::class)
        ->name('profesores');
});
```

> **Seguridad:** todo el grupo es `GET`. No hay ninguna ruta `POST`/`PUT`/`DELETE`/
> `PATCH` bajo `/app/director/*`. Esto hace imposible una mutación por la interfaz.

---

### Fase 5: Livewire Components

> Archivos (7 nuevos): `app/Livewire/Director/IndicatorDashboard.php`, `PensumList.php`, `CargaAcademicaList.php`, `ActivityList.php`, `LessonList.php`, `ResourceList.php`, `ProfesorIndicators.php`
> Accion: crear los 7 componentes usando `HasDirectorScope`. **ActivityList NO debe tener métodos de escritura de observaciones**.
> Verif pre: Fase 4 completo (rutas apuntan a estos FQCN).
> Verif post: cada componente compila: `for f in app/Livewire/Director/*.php; do php -l $f; done` → `No syntax errors`. Ninguno expone `save*`/`editObservations`/`cancelEdit`.

#### 5.1 Dashboard — Indicadores globales

```php
<?php
// app/Livewire/Director/IndicatorDashboard.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IndicatorDashboard extends Component
{
    use Concerns\HasDirectorScope;

    public $selectedLapsoId;
    public $lapsos;
    public $lapsoActive;

    // ─── KPI globales (toda la institución) ───
    public $totalPeducativos = 0;
    public $totalPensums = 0;
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalPevaluacions = 0;
    public $totalResources = 0;

    // ─── KPIs por Peducativo ───
    public $peducativoIndicators = [];

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->lapsos = \App\Models\app\Academy\Lapso::orderBy('id')->get();
        $this->lapsoActive = \App\Models\app\Academy\Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->totalPeducativos = $service->queryPeducativos()->count();

        $this->loadIndicators();
    }

    public function updatedSelectedLapsoId(): void
    {
        $this->loadIndicators();
    }

    public function loadIndicators(): void
    {
        $service = $this->getDirectorService();

        $this->totalPensums = $service->queryPensums()->count();
        $this->totalPevaluacions = $service->queryPevaluacions()
            ->when($this->selectedLapsoId, fn($q) => $q->where('lapso_id', $this->selectedLapsoId))
            ->count();
        $this->totalActivities = $service->queryActivities()->count();
        $this->totalProfesoresActivos = $service->queryProfesores()->count();
        $this->totalResources = $service->queryResources()->count();

        // Indicadores por Peducativo (seguimiento institucional)
        $this->peducativoIndicators = $service->queryPeducativos()->get()
            ->map(function ($peducativo) use ($service) {
                $pestudioIds = $service->queryPestudios()
                    ->where('peducativo_id', $peducativo->id)
                    ->pluck('id');

                return (object) [
                    'peducativo'       => $peducativo,
                    'pensums_count'    => Pensum::whereIn('pestudio_id', $pestudioIds)->count(),
                    'activities_count' => Activity::whereHas('pevaluacion.pensum', fn($q) => $q->whereIn('pestudio_id', $pestudioIds))->count(),
                    'profesores_count' => DB::table('profesors')
                        ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
                        ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                        ->whereIn('pensums.pestudio_id', $pestudioIds)
                        ->whereNull('pevaluacions.deleted_at')
                        ->distinct('profesors.id')
                        ->count('profesors.id'),
                ];
            });
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.director.indicator-dashboard')
            ->layout('director.layouts.app');
    }
}
```

#### 5.2 Listado de Pensums (read-only)

```php
<?php
// app/Livewire/Director/PensumList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Pensum;
use Livewire\Component;
use Livewire\WithPagination;

class PensumList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = Pensum::with([
            'pestudio.peducativo',
            'asignatura',
            'grado',
        ]);
        $query = $service->queryPensums();

        if ($this->peducativoId) {
            $query->whereHas('pestudio', fn($q) => $q->where('peducativo_id', $this->peducativoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('grado', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('pestudio', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pensums = $query->orderBy('pestudio_id')->paginate(20);
        $peducativos = $service->queryPeducativos()->get();

        return view('livewire.director.pensum-list', [
            'pensums'      => $pensums,
            'peducativos'  => $peducativos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
}
```

#### 5.3 Carga Académica (read-only)

```php
<?php
// app/Livewire/Director/CargaAcademicaList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;
use Livewire\WithPagination;

class CargaAcademicaList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = Pevaluacion::with([
            'profesor:id,name,lastname',
            'seccion:id,name,grado_id',
            'seccion.grado:id,name',
            'pensum.asignatura',
            'pensum.pestudio.peducativo',
            'lapso',
        ]);
        $query = $service->queryPevaluacions();

        if ($this->lapsoId) $query->where('pevaluacions.lapso_id', $this->lapsoId);
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('profesor', fn($sq) => $sq->where('lastname', 'like', "%{$this->search}%"))
                  ->orWhereHas('pensum.asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('seccion', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pevaluacions = $query->orderBy('pevaluacions.created_at', 'desc')->paginate($this->paginate);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');
        $peducativos = $service->queryPeducativos()->get();

        return view('livewire.director.carga-academica-list', [
            'pevaluacions' => $pevaluacions,
            'lapsos'       => $lapsos,
            'peducativos'  => $peducativos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
```

**Vista (Blade) — `resources/views/livewire/director/carga-academica-list.blade.php`**

UI con patrón **Grid/Tabla** (`crud-mode-toggle`, ver `.claude/skills/crud-mode-toggle.md`).
Mantiene la regla de oro del director: 100% solo lectura (sin `<form>`, `wire:submit` ni acciones de mutación).

- **Filtros:** `search` (docente/asignatura/sección), `peducativoId`, `lapsoId` vía `wire:model.live`; reset de página en `updating*()`.
- **Toggle Grid/Tabla:** clave `carga-academica-view-mode` en `localStorage` + evento `carga-academica-view-mode-changed` (`CustomEvent`); default `'table'`; `x-cloak` en el contenedor de vista para evitar flash.
- **Grid:** columnas masonry `columns-1 sm:columns-2 lg:columns-3 xl:columns-4`; cada card: asignatura (icono teal), profesor, badges sección/grado (gris), plan (pestudio) y programa (peducativo) (sky), lapso (badge con punto).
- **Tabla:** columnas Asignatura, Profesor, Sección, Plan, Programa, Lapso; estado vacío con `colspan=6`.
- **Paginación:** componente `<x-pagination-wrapper :paginator="$pevaluacions" />` al final de cada modo (grid y tabla), con `@if($pevaluacions->hasPages())`. Incluye selector de resultados (15/30/50/100 vía `$paginate`, default 15) y contador `firstItem–lastItem de total`; `updatingPaginate()` resetea la página.

#### 5.4 Actividades de Planificación (SIN observaciones editables)

> **Diferencia crítica vs `coordinacion`:** aquí **NO** hay `editObservations()`,
> `cancelEdit()` ni `saveObservations()`. Las actividades solo se visualizan y se
> generan sus PDFs. No hay ningún estado editable.

```php
<?php
// app/Livewire/Director/ActivityList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';

    // Filtros de contexto (snake_case, patrón del módulo Planning)
    public $pestudio_id = '';
    public $grado_id = '';
    public $seccion_id = '';
    public $profesor_id = '';
    public $lapso_id = '';

    // Filtros de estado de la actividad
    public $filter_observations = false;
    public $filter_revision = false;
    public $filter_status = '';

    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    // Listas para los selects del panel de filtros
    public $list_pestudio;
    public $list_grado;
    public $list_seccion;
    public $list_profesor;

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->list_pestudio = $service->queryPestudios()
            ->orderBy('order')
            ->pluck('name', 'id');
        $this->list_grado = Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->list_seccion = collect();
        $this->list_profesor = $service->queryProfesores()
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => "{$p->lastname}, {$p->name}"]);
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryActivities()->with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
        ]);

        if ($this->pestudio_id) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('pestudio_id', $this->pestudio_id));
        }
        if ($this->grado_id) {
            $query->whereHas('pevaluacion.seccion', fn($q) => $q->where('grado_id', $this->grado_id));
        }
        if ($this->seccion_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('seccion_id', $this->seccion_id));
        }
        if ($this->profesor_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $this->profesor_id));
        }
        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }
        if ($this->filter_observations) {
            $query->whereNotNull('activities.observations')
                  ->where('activities.observations', '!=', '');
        }
        if ($this->filter_revision) {
            $query->where('activities.status', 0);
        }
        if ($this->filter_status === 'pending') {
            $query->where('activities.status', 0);
        } elseif ($this->filter_status === 'approved') {
            $query->where('activities.status', 1);
        }

        $activities = $query->orderBy('activities.created_at', 'desc')->paginate($this->paginate);
        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.activity-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('director.layouts.app');
    }

    // ─── FILTERS CASCADE (patrón del módulo Planning) ──────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        $this->list_grado = $value
            ? Grado::where('pestudio_id', $value)->where('status_active', 'true')->orderBy('order')->pluck('name', 'id')
            : Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        $this->list_seccion = $value
            ? Seccion::list_seccion_grado($value)
            : collect();
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value)     { $this->resetPage(); }
    public function updatedProfesorId($value)    { $this->resetPage(); }
    public function updatedLapsoId($value)       { $this->resetPage(); }
    public function updatedFilterObservations($value) { $this->resetPage(); }
    public function updatedFilterRevision($value)     { $this->resetPage(); }
    public function updatedFilterStatus($value)       { $this->resetPage(); }

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPaginate(){ $this->resetPage(); }
}
```

**Vista (Blade) — `resources/views/livewire/director/activity-list.blade.php`**

UI con patrón **Grid/Tabla** (`crud-mode-toggle`, ver `.claude/skills/crud-mode-toggle.md`).
Mantiene la regla de oro del director: 100% solo lectura (sin `<form>`, `wire:submit` ni acciones
de mutación; solo enlaces GET a `Formato` y `Resumen`).

- **Panel de filtros ampliado** (onda 2.4): card `bg-white dark:bg-gray-900/40 backdrop-blur-md border ... rounded-lg mb-8` con grid `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3`, fiel al diseño del módulo Planning. Controles:
  - **Grid principal** (celdas): Búsqueda (`lg:col-span-2 xl:col-span-2`, `wire:model.live.debounce.300ms="search"`, placeholder "Buscar por tema o temática…"), **Plan Estudio** (`pestudio_id`, `$list_pestudio` de `DirectorScopeService::queryPestudios()`), **Profesor** (`profesor_id`, `$list_profesor` global de `queryProfesores()` — el director supervisa toda la institución, no se cascada), **Grado/Año** (`grado_id`, `$list_grado`), **Sección** (`seccion_id`, `$list_seccion`).
  - **Fila secundaria** (`sm:col-span-2 lg:col-span-4 xl:col-span-6`): **Lapso** (`lapso_id`, `$lapsos`). Nota: originalmente esta fila incluía también los toggles **Observaciones** (`filter_observations`), **En revisión** (`filter_revision`) y el **segmented Estado** (`filter_status`: Todos / Pendientes / Aprobadas) — añadidos en 2.4 y **eliminados por decisión del usuario** (onda 2.4 revisión) por no aportar valor en una lista de `Activity` read-only. El segmented usaba `@click="$wire.set('filter_status', '')"` (adaptación read-only: `wire:click` está prohibido por `DirectorReadOnlyTest` incluso en comentarios Blade).
  - **Omitidos del panel Planning:** `status_activities` (SI/NO — no aplica a una lista de `Activity` individuales) y el select `paginate` (conflicta con el selector 15/30/50/100 canónico de `<x-pagination-wrapper>`).
- **Filtros PHP:** todos aplican vía `whereHas` sobre `pevaluacion` (la lista es de `Activity`, no de `Pevaluacion`): `pestudio_id` → `pevaluacion.pensum`; `grado_id` → `pevaluacion.seccion`; `seccion_id`/`profesor_id`/`lapso_id` → `pevaluacion`. Reset de página en `updating*()`/`updated*()`.
- **Cascada (patrón Planning):** `updatedPestudioId` recarga `$list_grado` con `Grado::where('pestudio_id', $value)->where('status_active', 'true')` (o `Grado::active('true')` si vacío) y nullea `grado_id`/`seccion_id` + `$list_seccion`; `updatedGradoId` recarga `$list_seccion` con `Seccion::list_seccion_grado($value)` y nullea `seccion_id`.
- **Toggle Grid/Tabla:** clave `director-activities-view-mode` en `localStorage` + evento `director-activities-view-mode-changed` (`CustomEvent`); default `'table'`; `x-cloak` en el contenedor de vista. La clave lleva prefijo `director-` para no colisionar con `activities-view-mode` (módulo profesor, default `'grid'`).
- **Grid:** columnas masonry `columns-1 sm:columns-2 lg:columns-3 xl:columns-4`; cada card: tema (icono cyan), asignatura · sección/grado, temática (si existe), badges profesor (gris) y lapso (sky), badges de estado (ver **Columna Estado** abajo), grupo de botones Formato/Resumen.
- **Tabla:** columnas Tema, Asignatura, Sección, Profesor, Lapso, Estado, Acciones; estado vacío con `colspan=7`.
- **Columna Estado** (onda 2.6): indica el estado de la actividad con dos badges independientes, renderizados en puro Blade (`@if`/`@elseif` — sin `wire:click`, sin `<form>` → cumple `DirectorReadOnlyTest`):
  - **Aprobación del Jefe de Área** — `$activity->status` (boolean, columna `boolean('status')->default(false)`): `@if($activity->status !== null)` y `@if($activity->status)` → badge emerald **"Aprobada"** (check `M5 13l4 4L19 7`); `@else` → badge amber **"En revisión"** (X `M6 18L18 6M6 6l12 12`); `@else` (null) → badge gris **"Sin aprobar"** (guion `M20 12H4`). Marcup fiel a `leadership/activity-overview.blade.php`.
  - **Lección** — `$activity->lmsPublication?->status` (enum `DRAFT|SCHEDULED|PUBLISHED|ARCHIVED`): `'PUBLISHED'` → badge emerald **"Lección aprobada"** (check `M9 12l2 2 4-4`); `'SCHEDULED'` → badge sky **"Lección programada"** (reloj `M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z`); resto/null → badge gris **"Lección pendiente"** (alerta `M12 9v3m0 4h.01M5 3h14...`). Coherente con el flujo de Lecciones LMS (la lección se programa `SCHEDULED` y se aprueba/publica al llegar a `PUBLISHED`, véase `leadership/lesson-monitor.blade.php`).
  - En la **Tabla** los badges van apilados verticalmente (`flex flex-col gap-1`) en una `<td>` entre Lapso y Acciones; en el **Grid** van en una fila `flex flex-wrap gap-1.5` entre los badges de contexto y el grupo de botones. Badges `text-[10px] font-bold rounded-md border` con color de fondo `*-100`/`*-500/10` y texto/borde del mismo matiz (emerald/amber/sky/gris), icono SVG 12px + etiqueta.
  - **Anti N+1:** `lmsPublication` se agrega al `->with([...])` de `ActivityList::render()` (relación `hasOne` en `Activity`, junto al closure de `pevaluacion`).
- **Grupo de botones en Acciones** (onda 2.5): los enlaces planos Formato/Resumen se reemplazan por un grupo de botones unidos (patrón Planning/Leadership, p. ej. `planning/activities/index-component.blade.php`): wrapper `inline-flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/5 divide-x divide-gray-200 dark:divide-white/5` con `role="group"`, y dos `<a>` GET read-only — **Formato** (`app.director.activities.format`, icono documento con líneas, hover purple) y **Resumen** (`app.director.activities.resume`, icono documento, hover sky) — cada uno `target="_blank" rel="noopener"`, `title` descriptivo, `bg-gray-100 dark:bg-white/5`, `text-[10px] font-bold uppercase tracking-widest` con icono + etiqueta. Mismo markup en Grid y Tabla para consistencia al alternar vistas. Sin `<form>` ni `wire:click` (solo `<a href>` GET) → cumple `DirectorReadOnlyTest` (grep literales prohibidos: 0).
- **Paginación:** componente `<x-pagination-wrapper :paginator="$activities" />` al final de cada modo (grid y tabla), con `@if($activities->hasPages())`. Incluye selector de resultados (15/30/50/100 vía `$paginate`, default 15) y contador `firstItem–lastItem de total`; `updatingPaginate()` resetea la página.

#### 5.5 Lecciones LMS (read-only)

```php
<?php
// app/Livewire/Director/LessonList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use Livewire\Component;
use Livewire\WithPagination;

class LessonList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';

    // Filtros de contexto (snake_case, patrón del módulo Planning)
    public $pestudio_id = '';
    public $grado_id = '';
    public $seccion_id = '';
    public $profesor_id = '';
    public $lapso_id = '';

    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    // Listas para los selects del panel de filtros
    public $list_pestudio;
    public $list_grado;
    public $list_seccion;
    public $list_profesor;

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->list_pestudio = $service->queryPestudios()
            ->orderBy('order')
            ->pluck('name', 'id');
        $this->list_grado = Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->list_seccion = collect();
        $this->list_profesor = $service->queryProfesores()
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => "{$p->lastname}, {$p->name}"]);
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryActivities()->with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
            'lmsPublication',
            'lmsSections.contents',
        ]);

        if ($this->pestudio_id) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('pestudio_id', $this->pestudio_id));
        }
        if ($this->grado_id) {
            $query->whereHas('pevaluacion.seccion', fn($q) => $q->where('grado_id', $this->grado_id));
        }
        if ($this->seccion_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('seccion_id', $this->seccion_id));
        }
        if ($this->profesor_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $this->profesor_id));
        }
        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $lessons = $query->orderBy('activities.created_at', 'desc')->paginate($this->paginate);
        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.lesson-list', [
            'lessons' => $lessons,
            'lapsos'  => $lapsos,
        ])->layout('director.layouts.app');
    }

    // ─── FILTERS CASCADE (patrón del módulo Planning) ──────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        $this->list_grado = $value
            ? Grado::where('pestudio_id', $value)->where('status_active', 'true')->orderBy('order')->pluck('name', 'id')
            : Grado::active('true')->orderBy('order')->pluck('name', 'id');
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        $this->list_seccion = $value
            ? Seccion::list_seccion_grado($value)
            : collect();
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value)     { $this->resetPage(); }
    public function updatedProfesorId($value)    { $this->resetPage(); }
    public function updatedLapsoId($value)       { $this->resetPage(); }

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPaginate(){ $this->resetPage(); }
}
```

**Vista** (`lesson-list.blade.php`) — mejoras de la onda 2:

- **Panel de filtros ampliado** (card `bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-2 sm:p-5 rounded-lg mb-8`, grid `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3`): Búsqueda (`search`, `lg:col-span-2 xl:col-span-2`, `wire:model.live.debounce.300ms`, placeholder "Buscar por tema o temática…"), Plan Estudio (`pestudio_id`), Profesor (`profesor_id`), Grado/Año (`grado_id`), Sección (`seccion_id`), Lapso (`lapso_id`, fila completa `sm:col-span-2 lg:col-span-4 xl:col-span-6` con `w-40 sm:w-44`). Cascada vía `updatedPestudioId`/`updatedGradoId`; `whereHas` sobre `pevaluacion`/`pevaluacion.pensum`/`pevaluacion.seccion`. **Idéntico a §5.4 Filtros** (solo cambia la columna `topic`).
- **Toggle Grid/Tabla** (patrón `crud-mode-toggle`, key `lessons-view-mode`, evento `lessons-view-mode-changed`, default `table`). Botones Alpine `@click="mode = 'grid'/'table'"` con iconos SVG grid/tabla (sin `wire:click`, cumpliendo read-only). Contenedor con `x-cloak` + `x-init` que persiste en `localStorage`.
- **Botón "Ver / Imprimir"** (barra superior, junto al toggle Grid/Tabla): `<a href>` plano hacia `route('app.director.lessons.print')` con los filtros activos como query string (`array_filter` sobre `lapso/pestudio/grado/seccion/profesor/search`), `target="_blank"`, icono impresora + etiqueta `hidden sm:inline`. Es un enlace GET puro (sin `wire:click`, compatible read-only) que abre la página de impresión con la MISMA vista que el listado (los filtros seleccionados se conservan). Ver §5.5 Impresión. **Ojo de nomenclatura:** el grupo de rutas de la app lleva `->name('app.')` (outer, `Route::prefix('app')` línea 146 de `routes/web.php`), así que el nombre completo es `app.director.lessons.print` — sin el prefijo `app.` `route()` lanza `RouteNotFoundException` (bug real encontrado y corregido en 2.7).
- **Grid (masonry)**: `columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-2.5`; card = icono libro púrpura + tema + subtítulo "asignatura · sección (· grado)" + temática opcional + badges de profesor/lapso + badge de estado de publicación + footer "N secciones · Publicada d/m/Y" (con `@if` guardando `lmsPublication->published_at`). Estado vacío "Sin lecciones para los filtros seleccionados." `<x-pagination-wrapper :paginator="$lessons" />` dentro del bloque `x-show`, protegido por `@if($lessons->hasPages())`.
- **Tabla**: columnas Tema/Asignatura/Sección/Profesor/Lapso/Estado/Contenido. Columna Estado con badge de publicación; Contenido = "N secciones · Publicada d/m/Y". Fila vacía `colspan="7"`. `<x-pagination-wrapper :paginator="$lessons" />` protegido por `@if($lessons->hasPages())`.
- **Badge de estado de publicación** (puro Blade `@if/@elseif/@else`, en ambos modos): `PUBLISHED` → emerald "Publicada" (check `M5 13l4 4L19 7`); `SCHEDULED` → sky "Programada" (reloj `M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z`); `ARCHIVED` → gris "Archivada" (caja `M20 7l-8-4-8 4m16 0l-2 13H6L4 7m16 0l-8 4m0 0L4 7`); `@elseif($lesson->lmsPublication)` → gris "Borrador" (lápiz `M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z`) para `DRAFT`; `@else` (sin registro de publicación) → stone "Sin publicar" (ojo tachado `M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24`), clases `bg-stone-100 dark:bg-stone-500/10 text-stone-600 dark:text-stone-400 border-stone-200 dark:border-stone-500/20` (coherente con el monitor). Clases `inline-flex items-center gap-1 px-2 py-0.5 bg-{color}-100 dark:bg-{color}-500/10 text-{color}-700 dark:text-{color}-400 text-[10px] font-bold rounded-md border border-{color}-200 dark:border-{color}-500/20`.

#### 5.5.1 Impresión de lecciones LMS (Ver / Imprimir)

Página HTML **autónoma** (sin herencia del layout) con TODAS las lecciones que la
dirección está visualizando, para imprimir / guardar PDF con los diagramas **Mermaid**
y las matemáticas **KaTeX ya dibujados en el navegador** (el PDF generado por el diálogo
de impresión del navegador incluye los SVGs renderizados).

**Nueva ruta** (dentro del grupo director, GET-only):

```php
// routes/web.php (grupo director)
Route::get('/lecciones/print', [
    \App\Http\Controllers\Director\LessonsPrintController::class, 'index'
])->name('lessons.print');   // nombre completo: app.director.lessons.print
```

**Controller** (`app/Http/Controllers/Director/LessonsPrintController.php`):

- `index(Request)` parte de `new DirectorScopeService($request->user())->queryActivities()` —
  la dirección supervisa TODA la institución, sin filtro por usuario.
- Eager-load: `pevaluacion` (profesor:id,name,lastname · seccion.grado · pensum.asignatura ·
  pensum.grado · pensum.pestudio.peducativo · lapso), `lmsPublication`, `lmsSections`
  (orderBy sort_order), `lmsSections.contents` (orderBy sort_order), `lmsHtmlEmbeds`/
  `lmsResources`/`lmsLinks` (is_visible).
- **Filtros** (misma semántica que `LessonList`, ver §5.5): `lapso` → `whereHas('pevaluacion', lapso_id)`;
  `pestudio` → `whereHas('pevaluacion.pensum', pestudio_id)`; `grado` → `whereHas('pevaluacion.seccion', grado_id)`;
  `seccion` → `whereHas('pevaluacion', seccion_id)`; **`profesor`** (nuevo respecto al listado:
  `whereHas('pevaluacion', profesor_id)`); `search` → `topic`/`thematic` `like`. Orden
  `finicial desc`.
- `prepareLesson(Activity)` normaliza a arreglo plano: topic, thematic, description,
  **`profesor`** (`"{$lastname}, {$name}"` del responsable — la dirección no tiene profesor
  propio), asignatura, `grado` (vía `seccion.grado.name`, coherente con el listado), seccion,
  lapso, finicial/ffinal, estado/estado_label/estado_class, contadores de secciones/contenidos,
  `has_lms`, `sections` (contents + embeds concatenados, filtrando vacíos), `resources`
  (`display_name`), `links` (title+url).
- `estadoLabel()`: `PUBLISHED`→Publicado, `SCHEDULED`→Programado, `ARCHIVED`→Archivado,
  `null`→N.PUB, resto→Borrador. `estadoClass()`: `estado-pub|estado-prog|estado-arc|
  estado-npub|estado-draft`.
- **No llama** a `assertCanSupervise()` (verificaría `is_director` a secas y bloquearía a los
  admins legítimos): el middleware `IsDirector` del grupo es la autoridad.
- `filterLabels` resuelve los nombres legibles de cada filtro (incluido `profesor` como
  `"{$lastname}, {$name}"`) para el membrete del documento.

**Vista** (`resources/views/director/lessons-print.blade.php`) — documento autónomo:

- `<body class="lms-print">`, `@vite(['resources/css/app.css','resources/js/app.js'])` +
  `@livewireStyles`/`@livewireScripts` (necesarios para los componentes Alpine de render).
- `.print-bar` sticky con `<button id="btn-print" onclick="handlePrint()" aria-label="Imprimir o guardar PDF">🖨 Imprimir / Guardar PDF</button>`; oculto en `@media print`.
- Membrete `.doc-head` DENTRO de `.lessons-columns` (abre la columna 1): "DIRECCIÓN · LECCIONES
  LMS · CONTENIDO COMPLETO" + fecha + etiquetas de filtros activos (lapso/pestudio/grado/
  seccion/profesor/search). El `.sub` muestra "Dirección" fijo (la dirección no filtra por
  profesor propio).
- Por lección `.lesson`: `.lesson-head` (contador `.nnum`, topic, badge estado), `.lesson-meta`
  con **Asignatura · Profesor** (`$lesson['profesor']`, entrada condicional `@if`) · grado ·
  sección · lapso · fechas (`d/m`) · eje/temática · contadores.
- Secciones `.section` con el MISMO motor de detección de contenidos que el profesor
  (ver §5.5 del módulo profesor LMS): `IMAGE` (o `<svg\b`) → HTML crudo; **Mermaid** (regex
  `class="[^"]*\bmermaid\b"` o keyword `flowchart|graph|mindmap|sequenceDiagram|classDiagram|
  gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline`) → wrapper `mermaidEmbed()`
  (`wire:ignore x-data` + `data-mermaid-code`); `HTML` → sanitizado directo (`LmsHtmlSanitizerService`);
  `TEXT`/`MATH` → markdown/LaTeX vía `x-lms.math-text` (`data-math-content`). Embeds `lmsHtmlEmbeds`
  entran con `type = 'HTML'` (la detección Mermaid corre sobre `body` ANTES del branch HTML).
- Recursos (`display_name`) y enlaces (title+url) al pie de cada lección en `.lesson-res`.
- `@media print`: `@page { size: landscape; margin: 0.9cm }`, `.lessons-columns {
  column-count: 2; column-gap: 0.9cm; column-fill: auto; }`, `body{font-size:6pt}`,
  `break-inside`/`break-after` para evitar viudas, `.print-bar{display:none}`, badges de estado
  con fondo de color. `.mermaid-wrap svg{ max-width:100% !important; height:auto !important;
  overflow:visible !important }` — el `!important` gana al `style="max-width:<naturalWidth>px"`
  que `mermaid.render()` incrusta inline en el SVG, de modo que un diagrama ancho escala a la
  columna en lugar de desbordarla (bug 2.7.1, "no muestra bien los diagramas").
- `handlePrint()` (JS inline): si hay wrappers `[data-mermaid-code]` en el DOM, espera a que
  TODOS alcancen un **estado terminal** (`data-mermaid-state` = `ok` o `error`, poll 150 ms,
  timeout de seguridad 30 s solo para el caso de que el chunk de Mermaid no llegue a cargar)
  antes de `window.print()`; si no hay Mermaid, imprime directo. **Mecánica (bug 2.7.1):**
  cada wrapper `mermaidEmbed()` marca `data-mermaid-state` = `rendering` al empezar y `ok`
  (después de `setupUI()`, para que el SVG ya esté escalado a la columna) o `error` al
  terminar. El botón muestra progreso `Renderizando diagramas… (done/total)`. Con esto se
  corrige que (a) un diagrama con error bloqueara siempre la espera completa de 10 s y
  (b) un conjunto grande de diagramas (>10 s) imprimiera prematuro con SVGs en blanco.
  El mismo `handlePrint()` se aplica en lockstep a la vista del profesor (`profesor/lms/lessons-print.blade.php`).
- Pie de documento: `{{ auth()->user()?->username ?? 'Sistema' }} · {{ $fecha }}`.

**Read-only:** la vista NO contiene `<form>`, `</form>`, `wire:submit`, `wire:click`,
`method="post"` ni `@csrf` (invariante verificado por `DirectorLessonsPrintTest::..._has_no_write_controls`
y cubierto por `DirectorReadOnlyTest::test_all_director_routes_are_get_only`, ya que la nueva
ruta es GET). Sí usa `wire:ignore` (permitido: no muta estado, solo desactiva la hidratación
de Livewire sobre el nodo Mermaid).

**Tests** (`tests/Feature/Director/DirectorLessonsPrintTest.php`, 15): visión global (ambos
profesores visibles + nombre del responsable en el meta), render de SVG/HTML/Mermaid/KaTeX,
botón de impresión, membrete en columna 1 (orden `lessons-columns` → `doc-head` → `lesson`,
`column-count: 2`, `column-fill: auto`), vacío cuando no coincide (`lapso=999999`), los 6
filtros (incluido `profesor`) + combinados, y la ausencia de literales de escritura.

#### 5.5.2 Defensa en profundidad: diagramas Mermaid desbordados (bug 2.7.2)

**Síntoma:** el PDF de impresión (`storage/app/lms/pdf/result/LeccionesLMSResult010.pdf`)
mostraba un diagrama Mermaid que desbordaba la columna de ~450px en las primeras páginas
(contenido id 296: `graph TD`, 13 nodos, ~9 niveles de profundidad, labels de hasta ~48
caracteres en una sola línea, 3 directivas `style`). El diagrama se genera desde el wizard
del profesor (`app/Livewire/Profesor/Lms/LessonWizard.php`), así que la defensa actúa tanto
en la **generación** (wizard) como en el **render** (vistas de impresión director/profesor)
y el **motor JS** de Mermaid.

**Causas raíz (tres):**
1. **`strip_tags()` destruye `<br/>`** — los labels multi-línea `["Texto<br/>largo"]`
   quedaban como `"Textolargo"` (una sola línea larga) al persistir/extraer el diagrama.
2. **Prompts del wizard sin límites de tamaño** — sin tope de nodos/profundidad ni de
   longitud de labels, la IA producía árboles profundos y anchos incompatibles con una
   columna de impresión.
3. **CSS de impresión pre-2.7.1** — sin `!important`, el `style="max-width:<naturalWidth>px"`
   inline que `mermaid.render()` incrusta en el SVG ganaba al CSS (ya resuelto en 2.7.1).

**Seis capas implementadas (A1, B1, B2, E1, C1, D1 — todas aprobadas por el usuario):**

- **A1 — Conservar `<br/>` en la extracción:** se reemplaza `strip_tags($x)` por
  `trim(html_entity_decode(strip_tags($x, '<br><br/>')))` en los 4 sitios de extracción:
  la detección Mermaid de las DOS vistas de impresión (director y profesor) y, sobre todo,
  el **flujo de guardado del wizard** (`saveLesson()` + extracción de embed), porque el
  `strip_tags` del SAVE ocurría ANTES de imprimir y ya había concatenado los labels en el
  momento de abrir la vista de impresión.
- **B1 — `useMaxWidth: true`:** en ambos `mermaid.initialize()` de
  `resources/js/lms-student-preview.js` (líneas ~32 y ~48), para que Mermaid use el ancho
  natural del SVG como `max-width` en su `style` inline (además del `max-width:100%` que
  aplica `setupUI()`), evitando que el layout interno desborde la columna.
- **B2 — `break-inside:avoid`:** `.mermaid-wrap { break-inside: avoid; page-break-inside:
  avoid; }` en el CSS de impresión de ambas vistas, para que un diagrama no se corte entre
  dos columnas/páginas.
- **E1 — Heurística de diagrama ancho → `column-span:all`:** por cada contenido Mermaid se
  calcula `nodos` (regex de declaraciones de nodo), `flechas` (`--[->]|==>|..->`) y la
  línea más larga; si `nodos ≥ 12 || flechas ≥ 11 || maxLine > 55`, el wrapper recibe la
  clase `mermaid-wide` que en `@media print` aplica `column-span:all` (y `-webkit-column-
  span:all`), haciendo que el diagrama cruce TODA la página (spanner) en lugar de apretarse
  en la columna. La heurística es idéntica en las dos vistas y está alineada con el umbral
  de validación D1 del wizard.
  **→ Actualizado en 2.7.6:** el spanner E1 (`column-span:all`) y la heurística
  `mermaid-wide` se ELIMINARON; todo diagrama queda enmarcado dentro de su columna (ver
  §5.5.6).
- **C1 — Endurecer prompts del wizard** (requisitos en `generateSlideDiagram()` y en la
  regla 6 de `generateEmbedCard()`): MÁXIMO 12 nodos y 11 flechas por diagrama, máximo 3
  niveles de profundidad; si hay más de 12 conceptos, AGRUPAR los secundarios en un nodo
  resumen ("Otros: A, B, C"); IDs de nodo cortos (A, B, C, N1...) sin repetir el label;
  labels ≤30 caracteres por línea separando con `<br/>` (o arreglos `[l1, l2]`); evitar
  `style` directives salvo casos imprescindibles; recordatorio explícito de que el diagrama
  se imprime en una columna de ~450px.
- **D1 — Validación post-generación + un reintento:** el wizard valida el código Mermaid
  recién generado con `validateMermaidDiagram()` (alerta si `nodos > 14`, `flechas > 16`,
  o algún label quoted >30 chars). Si falla, construye `diagramCorrectionBlock()` con los
  problemas detectados y el diagrama completo (truncado a 3000 chars) y hace UN reintento
  vía `callMermaidModel()` a temperatura 0.3 con la MISMA cadena de 3 modelos del flujo de
  diagramas (Qwen 3.1 32B → Mistral Large → Claude Sonnet 4). Se aplica en los DOS flujos:
  `generateSlideDiagram()` (tras extraer con `extractMermaidCodeFromRaw()` y antes del
  post-procesado) y `generateEmbedCard()` (validando el código Mermaid interno del card,
  SIN forzar `graph TD` para respetar el tipo de diagrama elegido). Si el reintento fracasa,
  se conserva el primer resultado (el usuario puede regenerar).

**Refactor asociado (wizard):**
- `extractMermaidCodeFromRaw(string $raw): string` — extracción unificada (fences ` ```html|
  mermaid ` o keyword inicial) + limpieza de scripts CDN/`mermaid.initialize`, tags
  `html/head/body/meta/link` y divs trailing. Reemplaza la lógica inline duplicada de
  `generateSlideDiagram()`.
- `postProcessMermaid(string $code, bool $forceGraphTd = true): string` — fuerza `graph TD`
  (si `$forceGraphTd`) y parte labels largos `["...{35,}"]` en multi-línea con `<br/>`.
  Reemplaza el post-procesado inline de `generateSlideDiagram()`.
- `extractMermaidSrc(string $code): string` — extrae SOLO el Mermaid interno de un wrapper
  `<div class="mermaid">…</div>` preservando `<br/>`; si no hay wrapper, devuelve el código.
- `validateMermaidDiagram(string $src): array` — devuelve `['ok','issues','nodes','arrows',
  'maxLabel']` con los umbrales de D1.
- `diagramCorrectionBlock(string $src, array $validation): string` — feedback para el modelo.
- `callMermaidModel(string $systemPrompt, string $userPrompt, array $overrides = []): array` —
  wrapper de `askWithCompaction()` con `['max_tokens'=>4096,'temperature'=>0.7,'timeout'=>300]`,
  presupuesto 3500 y la cadena de 3 modelos de diagramas (los `$overrides` pasados por el
  llamador ganan en el merge `+`).

**Nota de encoding:** `LessonWizard.php` contiene mojibake Latin-1 para acentos (`├│` = ó);
el texto añadido por esta capa es ASCII puro para no mezclar encodings.

**Verificación:** 63 tests de `LessonWizardCharacterizationTest` pasan (sin regresión tras
el refactor de los dos flujos), `php8.2 -l` OK en el wizard, e invariante read-only del
director (`DirectorReadOnlyTest`) verde. La capa E1 respeta el invariante read-only: la
clase `mermaid-wide` es solo CSS (`column-span:all`), sin literales de escritura.

#### 5.5.3 Acotar la altura de diagramas Mermaid grandes a una página (bug 2.7.3)

El PDF `LeccionesLMSResult011.pdf` mostró que el mismo contenido id 296 del bug 2.7.2
("El misterio de los símbolos perdidos", `graph TD`, 13 nodos, ~9 niveles) ya no
desbordaba en horizontal (capas 2.7.1/2.7.2), pero tras la capa E1 (spanner
`column-span:all`) ocupaba **más de una página en vertical**: demasiado alto.

**Síntoma:** un diagrama grande cabe en el ancho de la página pero su altura natural
supera el alto del área de contenido (~561 pt en horizontal con márgenes de 0.9 cm);
el bloque se extiende a la página siguiente. El `break-inside:avoid` de B2 no puede
partirlo en dos, pero tampoco lo reduce.

**Capa F1 — escala por altura (solo CSS de impresión, sin tocar el wizard):**
- `.mermaid-wrap svg` dentro de `@media print` gana `max-height` (además del
  `max-width:100% !important` existente). Como el SVG tiene `viewBox` y el CSS ya pone
  `height:auto`, el motor **escala el SVG a la página conservando la proporción**:
  respeta simultáneamente `max-width` y `max-height` (elemento con ratio intrínseco).
- **Cascada deliberada `pt`/`vh`:** se declara primero `max-height:430pt !important`
  (fallback universal; en print `pt` es inambigüo, 1/72 in) y después
  `max-height:70vh !important`. En la cascada CSS (misma especificidad e importancia)
  gana la **última** declaración *válida*: si el motor soporta `vh` en print, el valor
  en `vh` se adapta al alto real del papel; si no, la declaración `vh` se descarta como
  inválida y queda el `pt`. El `!important` solo es por consistencia: `mermaid.render()`
  no incrusta `max-height` inline (solo `max-width`), así que no hay conflicto real.
- **Dos umbrales:**
  - `.mermaid-wrap svg` → `430pt`/`70vh`: diagrama en **columna** (ancho ~470 pt). El
    tope es menor que el alto de columna (~453 pt) para que ningún diagrama se salga
    del flujo de 2 columnas.
  - `.mermaid-wrap.mermaid-wide svg` → `515pt`/`83vh`: spanner E1 que cruza toda la
    página. Cabe en los ~561 pt de alto de contenido menos marco/etiqueta.
    **→ Actualizado en 2.7.6:** el spanner `mermaid-wide` se eliminó; el tope de
    columna `430pt/70vh` es ahora el único tope de altura (ver §5.5.6).

**Enmarcado reforzado ("acotados, enmarcados"):**
- `overflow:hidden` en `.mermaid-wrap` (solo `@media print`): red de seguridad — recorta
  el exceso si por cualquier motivo el escalado no se aplicara. No afecta a la
  previsualización en pantalla, donde la regla no existe y el toolbar de zoom funciona.
- `border-color:#94a3b8` sobre el `#e2e8f0` base: marco más visible que delimita la
  zona acotada.
- Etiqueta `DIAGRAMA · VISTA AMPLIA` vía `::before` en `.mermaid-wrap.mermaid-wide`
  (barra superior verde `#0f766e`, texto blanco 5 pt), con `padding-top:12px` en el
  wrap para dejarle sitio; `position:relative` en el wrap. Hace explícito en el PDF que
  el diagrama está enmarcado y acotado.

**Presupuesto de altura (página horizontal, alto 612 pt, márgenes 0.9 cm → contenido
~561 pt):** wide → margen 9 pt + padding-top 12 px (9 pt) + padding-bottom 4 px (3 pt)
+ bordes 1.5 pt ≈ 24 pt → SVG máx. 515 pt cabe con holgura. Columna → ~453 pt menos
márgenes/padding/borde ≈ 445 pt disponibles → tope 430 pt. Ningún diagrama supera una
página.

**Nota de encoding:** la etiqueta usa "·" (U+00B7, punto medio). Las vistas Blade son
UTF-8 (ya contienen acentos), a diferencia de `LessonWizard.php` (mojibake Latin-1).

**Verificación:** 17 tests de `DirectorLessonsPrintTest` + 28 de `LessonsPrintTest` +
3 de `DirectorReadOnlyTest` verdes (el invariante read-only sigue intacto: la capa F1
es solo CSS dentro de `@media print`, sin literales de escritura). El `view:cache`
falla por un error pre-existente ajeno (componente `heroicon-m::x-mark`), verificado
con `git stash`.

#### 5.5.4 Acotar el ANCHO de diagramas Mermaid grandes a media página (bug 2.7.4)

Continuación del bug 2.7.3: el mismo contenido id 296 ("El misterio de los símbolos
perdidos") ya cabía en UNA página tras la capa F1 (2.7.3), pero como spanner E1
(`column-span:all`) seguía ocupando el **ANCHO completo** de la página horizontal
(~790 pt de contenido) — demasiado ancho. El usuario pidió que el diagrama quedara
"acotado" también en ancho: máximo media página.

**Síntoma:** un diagrama ancho (clase `mermaid-wide`, que cruza todo el ancho de la
página por `column-span:all`) se extiende de borde a borde; en pantalla también
dominaba la columna.

**Capa G1 — escala por ancho (solo CSS de impresión, espejo de F1):**
- `.mermaid-wrap.mermaid-wide` dentro de `@media print` gana `max-width` + centrado
  (`margin-left:auto !important;margin-right:auto !important`). Antes el spanner
  ocupaba el ancho completo; ahora se limita a media página y se centra.
- **Cascada deliberada `pt`/`vw`** (mismo patrón que F1): primero
  `max-width:350pt !important` (fallback universal, inambigüo en print) y después
  `max-width:50vw !important`. En la cascada CSS gana la **última** declaración
  *válida*: si el motor soporta `vw` en print, 50vw se adapta al ancho real del papel
  (media página); si no, queda el `pt`.
- **Doble tope:** en el marco `.mermaid-wrap.mermaid-wide` (el `margin:auto` lo centra)
  Y en el `svg` interno (`max-width:350pt !important;max-width:50vw !important`) —
  red de seguridad por si el motor no honrara `max-width` sobre un spanner de
  `column-span:all`.
- **Los diagramas en columna ya caben en media página** (columna ≈383 pt < 50vw ≈421 pt),
  así que el tope solo actúa sobre los amplios (`mermaid-wide`).

**Previsualización en pantalla:** el `.mermaid-wrap` base (fuera de `@media print`)
gana `max-width:50vw` para que la previsualización en pantalla (zoom/toolbar) muestre
el mismo tope de media página que tendrá la impresión.

**Verificación:** 18 tests de `DirectorLessonsPrintTest` (incluye el nuevo
`director_print_bounds_mermaid_width_to_half_page`, que verifica las dos reglas exactas
sobre el marco y el svg) + 28 de `LessonsPrintTest` + 3 de `DirectorReadOnlyTest`
verdes. Invariante read-only intacto: la capa G1 es solo CSS dentro de `@media print`,
sin literales de escritura.

**→ Actualizado en 2.7.6:** los topes de ancho G1 (`350pt/50vw`) y la previsualización
en pantalla `max-width:50vw` se ELIMINARON junto con el spanner `column-span:all`; el
ancho del diagrama lo limita ahora `max-width:100% !important` en el svg dentro del
marco de su columna (ver §5.5.6).

#### 5.5.5 Impresión de lecciones LMS desde el monitor de Planificación (Ver / Imprimir)

El monitor LMS de Planificación (`/app/planning/lms/monitor`, componente `LmsMonitor`)
recibe el MISMO botón "Ver / Imprimir" que el listado de la Dirección (5.5.1): un
`<a href>` plano GET hacia la página de impresión autónoma con los filtros activos del
monitor como query string, abriendo en `target="_blank"`. No usa `wire:click` — es un
enlace puro, compatible con el invariante read-only.

**Reuso cross-módulo de `Director\LessonsPrintController`:** el mismo controlador sirve
las dos rutas y deduce el contexto por el nombre de la ruta:

```php
Route::prefix('lms')->middleware(['auth', 'isPlanner'])->name('lms.')->group(function () {
    // ...
    Route::get('/print', [\App\Http\Controllers\Director\LessonsPrintController::class, 'index'])
        ->name('print');   // app.planning.lms.print
});
```

- **Aislamiento de roles:** `IsPlanner` (permite `is_admin || is_planner || is_diagnostic`,
  else `abort(403)`) protege la ruta de planificación; `IsDirector` protege la de la
  Dirección. Un planificador no puede imprimir la ruta de la Dirección ni un director la
  de planificación (verificado por `lms_print_requires_planner_role` y
  `director_print_keeps_director_letterhead`). Patrón espejo del ADR-005: allí el director
  reusaba `Planning\ActivityPdfController`; aquí planning reusa un controlador de la
  Dirección, manteniendo un único punto de formato de la vista de impresión (ver ADR-006).
- **Membrete adaptado al módulo:** `$isPlanning = str_contains($request->route()?->getName() ?? '', 'planning')`
  → `contexto = 'Planificación · Monitor LMS'`, `titulo = 'PLANIFICACIÓN · LECCIONES LMS ·
  CONTENIDO COMPLETO'`. El membrete de la Dirección queda intacto (test dedicado).
- **Filtros del monitor:** el enlace mapea `filterPestudio/filterGrado/filterSeccion/
  filterProfesor/filterAsignatura/filterStatus/search` → query string
  `pestudio/grado/seccion/profesor/asignatura/status/search` (los vacíos se descartan con
  `array_filter`). El controlador ganó los filtros `asignatura` (a través del pensum) y
  `status` (`whereHas('lmsPublication', status=...)`), con sus etiquetas en `filterLabels`
  vía `estadoLabel()`: PUBLISHED→Publicado, SCHEDULED→Programado, ARCHIVED→Archivado.
- **Verificación:** 8 tests de `LmsPrintTest` (acceso + membrete + filtros asignatura/
  status + botón llevando los filtros activos + `target="_blank"` + invariante read-only de
  la vista) + 33 tests de las suites director verdes. El href del botón se construye en el
  test con `route('app.planning.lms.print', ...)` y el `&` escapado a `&amp;` — robusto al
  `APP_URL` del entorno (`localhost` vs `cfla.local`).

#### 5.5.6 Enmarcar diagramas Mermaid dentro de su columna (bug 2.7.5)

El usuario reportó que los diagramas Mermaid **seguían ocupando demasiado espacio**: pese
al tope de media página de G1 (2.7.4), la capa E1 (2.7.2) los extraía del flujo de
2 columnas con `column-span:all` para cruzar la página como spanner. La solución
definitiva no es acotar el spanner, sino **eliminarlo**: ningún diagrama sale ya de su
columna; todos quedan **enmarcados dentro de una de las columnas**.

**Síntoma:** el spanner E1 (`mermaid-wide` → `column-span:all`) hacía que un diagrama
ancho ocupara el ancho completo (o media página tras G1) y rompiera la composición de
libro de 2 columnas; en pantalla dominaba la columna con `max-width:50vw`.

**Capa K1 — eliminar el mecanismo de diagrama ancho (solo vistas de impresión):**
- **Regla `column-span:all` eliminada:** ya no existe `.mermaid-wrap.mermaid-wide` en el
  CSS de impresión; ningún diagrama cruza el flujo de 2 columnas.
- **Heurística Blade `$mermaidWide` eliminada** (`nodos≥12 || flechas≥11 || línea>55`):
  el wrapper es siempre `<div class="mermaid-wrap">`, sin clase condicional.
- **Eliminados además:** etiqueta `DIAGRAMA · VISTA AMPLIA` (`::before`), tope de altura
  wide `515pt/83vh`, topes de ancho `350pt/50vw` (marco + svg) y la previsualización en
  pantalla `max-width:50vw`.

**Enmarcado dentro de la columna (lo que queda):**
- El marco `.mermaid-wrap` (`background:#f8fafc`, `border:1px solid #e2e8f0`,
  `padding:4px`, `border-radius:4px`, `break-inside:avoid` + `page-break-inside:avoid`)
  enmarca el diagrama y evita que se parta entre columnas/páginas (B2, 2.7.2).
- `max-width:100% !important; height:auto !important` en `.mermaid-wrap svg` limita el
  ancho al de la columna (~383pt); el `!important` gana al `style="max-width:<naturalWidth>px"`
  inline que `mermaid.render()` incrusta.
- `max-height:430pt !important; max-height:70vh !important` (J2, 2.7.3) limita el alto:
  el tope de columna es ahora el **único** tope de altura.
- `overflow:hidden` + `border-color:#94a3b8` en `@media print` (de 2.7.3): red de
  seguridad que recorta el exceso y marco más visible que delimita la zona acotada.

**Nota de composición:** al desaparecer el spanner, un diagrama muy ancho ya no desborda
ni sale de su columna: el escalado del `viewBox` (el SVG respeta `max-width` y
`max-height` simultáneamente, conservando la proporción) lo compacta dentro del ancho de
columna.

**Aplicado en lockstep** a la vista del profesor (`resources/views/profesor/lms/
lessons-print.blade.php`), que mantiene idéntico el CSS y el render de Mermaid.

**Verificación:** 41 tests de impresión + read-only verdes (18 `DirectorLessonsPrintTest`
+ 12 `LessonsPrintTest` + 8 `LmsPrintTest` + 3 `DirectorReadOnlyTest` — invariante
read-only intacto, sin literales de escritura). El test de regresión de 2.7.4
`director_print_bounds_mermaid_width_to_half_page` se convierte en
`director_print_frames_mermaid_diagrams_within_column` (afirma el marco + `max-width:100%`
y la ausencia de `column-span:all`, `mermaid-wide` y `DIAGRAMA · VISTA AMPLIA`);
`director_print_bounds_tall_mermaid_diagrams_to_one_page` pierde las aserciones del
spanner wide (`515pt/83vh`) y de la etiqueta.

#### 5.6 Recursos Compartidos (read-only)

```php
<?php
// app/Livewire/Director/ResourceList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Lms\LmsActivityResource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = LmsActivityResource::with([
            'activity.topic',
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.pensum.pestudio.peducativo',
            'activity.pevaluacion.profesor',
            'media',
        ]);
        $query = $service->queryResources();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                  ->orWhereHas('activity', fn($sq) => $sq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        $resources = $query->orderBy('lms_activity_resources.created_at', 'desc')->paginate(20);

        return view('livewire.director.resource-list', [
            'resources' => $resources,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
}
```

#### 5.7 Seguimiento Docente — KPIs (read-only)

```php
<?php
// app/Livewire/Director/ProfesorIndicators.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Profesor;
use Livewire\Component;
use Livewire\WithPagination;

class ProfesorIndicators extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $lapsoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        // Todos los profesores activos con su carga (seguimiento global)
        $query = Profesor::withCount([
            'pevaluacions as peva_count' => fn($q) => $q->when($this->lapsoId, fn($qq) => $qq->where('lapso_id', $this->lapsoId)),
        ]);
        $query = $service->queryProfesores();

        if ($this->search) {
            $query->where(fn($q) => $q->where('lastname', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%"));
        }

        $profesores = $query->orderBy('lastname')->paginate(20);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.profesor-indicators', [
            'profesores' => $profesores,
            'lapsos'     => $lapsos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
```

---

### Fase 6: Navegación y Vistas

> Archivos: `resources/views/director/layouts/app.blade.php` (nuevo), `resources/views/components/navbars/director-items.blade.php` (nuevo), `resources/views/components/navbars/director-items-mobile.blade.php` (nuevo), `resources/views/livewire/director/*.blade.php` (7 nuevas vistas)
> Accion: crear el layout dedicado, el submenú de navbar `Dirección` y las 7 vistas read-only (sin formularios de escritura).
> Verif pre: Fase 5 completo (componentes cargan).
> Verif post: las vistas no contienen `<form`, `wire:submit`, `x-on:submit`, ni `@csrf`. Comprobación: `grep -rnE "<form|wire:submit|method=\"post\"" resources/views/livewire/director/ resources/views/director/` → sin salida.

#### 6.1 Navbar items para dirección

```blade
{{-- resources/views/components/navbars/director-items.blade.php --}}
@if(Auth::user()->is_director)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('director.*') ? 'bg-sky-500/10 text-sky-400' : 'text-gray-400 hover:text-sky-300 hover:bg-white/5' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            Dirección
            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute left-0 mt-1 w-56 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-2 z-50">
            <a href="{{ route('director.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.index') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('director.pensums') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.pensums') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Pensums
            </a>
            <a href="{{ route('director.carga-academica') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.carga-academica') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Carga Académica
            </a>
            <a href="{{ route('director.activities') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.activities') || request()->routeIs('director.activities.*') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Actividades
            </a>
            <a href="{{ route('director.lessons') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.lessons') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Lecciones
            </a>
            <a href="{{ route('director.resources') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.resources') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Recursos
            </a>
            <a href="{{ route('director.profesores') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-sky-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('director.profesores') ? 'text-sky-400 bg-sky-500/5' : '' }}">
                <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profesores
            </a>
        </div>
    </div>
@endif
```

#### 6.2 Layout dedicado de dirección

```blade
{{-- resources/views/director/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dirección') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 antialiased">
    <nav class="sticky top-0 z-50 bg-gray-800/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('director.index') }}" class="text-lg font-semibold text-sky-400">
                        Dirección
                    </a>
                    @include('components.navbars.director-items')
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-400 transition-colors">Salir</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
    @livewireScripts
    @stack('scripts')
</body>
</html>
```

```blade
{{-- resources/views/components/navbars/director-items-mobile.blade.php --}}
{{-- Menú responsive para mobile (mismo set de enlaces que director-items) --}}
@if(Auth::user()->is_director)
    <div class="space-y-1">
        <div class="text-[10px] font-bold uppercase tracking-widest text-sky-400/60 px-3 py-1.5">Dirección · Supervisión</div>
        <a href="{{ route('director.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('director.index') ? 'text-sky-400 bg-sky-500/5' : 'text-gray-300 hover:text-sky-300 hover:bg-white/5' }} transition-colors">Dashboard</a>
        <a href="{{ route('director.pensums') }}" class="...">Pensums</a>
        <a href="{{ route('director.carga-academica') }}" class="...">Carga Académica</a>
        <a href="{{ route('director.activities') }}" class="...">Actividades</a>
        <a href="{{ route('director.lessons') }}" class="...">Lecciones</a>
        <a href="{{ route('director.resources') }}" class="...">Recursos</a>
        <a href="{{ route('director.profesores') }}" class="...">Profesores</a>
    </div>
@endif
```

#### 6.3 Vistas Blade read-only (sin formularios)

Todas las vistas de `livewire/director/*.blade.php` siguen un único patrón:
- Cabecera con título y filtros de búsqueda (que son lecturas).
- Tabla/tarjetas de resultados.
- **Ningún `<form>`, `wire:submit`, botón de guardar, textarea editable ni selector
  de aprobación.** Los únicos enlaces de acción son: ver PDF (GET), ver detalle.

Ejemplo (esqueleto) de `activity-list.blade.php`:

```blade
{{-- resources/views/livewire/director/activity-list.blade.php --}}
<div class="fade-in">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Actividades de Planificación</h1>
            <p class="text-sky-600 dark:text-sky-400 font-medium">Seguimiento · solo lectura</p>
        </div>
        {{-- Filtros de lectura --}}
        <div class="flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar actividad..." class="rounded-lg border-white/10 bg-white/5 px-3 py-2 text-sm" />
            <select wire:model.live="lapsoId" class="rounded-lg border-white/10 bg-white/5 px-3 py-2 text-sm text-gray-400">
                <option value="">Lapso: Todos</option>
                @foreach($lapsos ?? [] as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla de resultados (solo lectura, sin acciones de escritura) --}}
    @foreach($activities as $activity)
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 mb-3 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white">{{ $activity->topic }}</h3>
                <p class="text-xs text-gray-500">
                    {{ $activity->pevaluacion?->profesor?->lastname }},
                    {{ $activity->pevaluacion?->profesor?->name }} · {{ $activity->pevaluacion?->pensum?->asignatura?->name }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('director.activities.format', $activity->pevaluacion_id) }}" target="_blank"
                    class="text-[11px] font-bold uppercase tracking-widest text-sky-400 hover:text-sky-300">Formato</a>
                <a href="{{ route('director.activities.resume', $activity->pevaluacion_id) }}" target="_blank"
                    class="text-[11px] font-bold uppercase tracking-widest text-sky-400 hover:text-sky-300">Resumen</a>
            </div>
        </div>
    @endforeach

    {{ $activities->links() }}
</div>
```

---

### Fase 7: Seguridad y Validación (Read-Only Enforcement)

> Archivos: revisión de rutas + servicio + vistas (auditoría) + `tests/Feature/Director/DirectorReadOnlyTest.php` (nuevo)
> Accion: implementar el esqueleto de test de no-escritura (reflexión sobre `DirectorScopeService` y auditoría de rutas GET-only).
> Verif pre: Fases 3, 4 y 6 completas.
> Verif post: `php artisan route:list --name=director | grep -viE "\sGET\s"` → sin salida (todas GET).

#### 7.1 Matriz de autorización

| Ruta | Middleware | Scope | Lectura | Escritura |
|------|-----------|-------|---------|-----------|
| `/app/director/` | `IsDirector` | Global (toda la institución) | Dashboard indicadores | ❌ |
| `/app/director/pensums` | `IsDirector` | Global | Lista pensums | ❌ |
| `/app/director/carga-academica` | `IsDirector` | Global | Pevaluacions | ❌ |
| `/app/director/activities` | `IsDirector` | Global | Actividades + PDF | ❌ |
| `/app/director/lecciones` | `IsDirector` | Global | Lecciones LMS | ❌ |
| `/app/director/lecciones/print` | `IsDirector` | Global | Impresión Lecciones LMS (Mermaid/KaTeX en navegador) | ❌ |
| `/app/planning/lms/print` | `IsPlanner` | Global | Impresión Lecciones LMS — reusa `Director\LessonsPrintController`, membrete Planificación (módulo planning, 2.7.5) | ❌ |
| `/app/director/recursos` | `IsDirector` | Global | Recursos | ❌ |
| `/app/director/profesores` | `IsDirector` | Global | KPIs docentes | ❌ |

#### 7.2 Defensa en profundidad (Read-Only)

1. **No existen rutas de escritura**: el grupo de rutas solo define verbos `GET`.
2. **No se exponen métodos de mutación**: `DirectorScopeService` no tiene `save*`/`approve*`/
   `comment*`/`observe`. Los componentes Livewire de Director no tienen métodos `store`/
   `update`/`create`/`save*`.
3. **Vistas sin formularios**: las vistas read-only no contienen `<form>`, `wire:submit`,
   `x-on:submit` ni `@csrf` de escritura.
4. **Guarda de defensa** `assertCanSupervise()` disponible en el servicio por si un futuro
   endpoint de lectura necesita verificar el rol explícitamente.
5. **PDF por GET** reusan `ActivityPdfController` (read-only), protegidos por el mismo middleware
   `IsDirector` del grupo (por ser rutas `GET` dentro del grupo con `middleware(['auth','isDirector'])`).

> **Regla de revisión:** cualquier PR futuro que agregue una acción de escritura bajo
> `/app/director/*` debe acompañarse de un ADR nuevo y de un test que verifique la guarda
> de autorización (ver Fase 8). Es una violación de diseño si una vista de director
> contiene un `wire:click` que muta estado.

---

### Fase 8: Testing

> Archivos: `database/factories/UserFactory.php` (editar — agregar estado `director()`), `tests/Feature/Director/DirectorMiddlewareTest.php` (nuevo), `tests/Feature/Director/DirectorDashboardTest.php`, `tests/Unit/DirectorScopeTest.php`, `tests/Feature/Director/DirectorReadOnlyTest.php` (completar)
> Accion: completar la pirámide de tests (Fase 7 ya dejó el esqueleto) y correr la suite.
> Verif pre: Fases 1-7 completas.
> Verif post: `php artisan test` → **all green** (sin SKIP). Además `php artisan test --filter=DirectorReadOnlyTest` confirma el invariante read-only.

#### 8.1 Pirámide de tests

```
    ┌──────────────────────────────┐
    │  Feature: Flujo completo      │  ← 2 tests
    │  is_director (read-only flow) │
    ├──────────────────────────────┤
    │  Feature: Middleware/Acceso   │  ← 3 tests
    ├──────────────────────────────┤
    │  Feature: No-write enforcement│  ← 2 tests
    ├──────────────────────────────┤
    │  Unit: Model + Service        │  ← 4 tests
    └──────────────────────────────┘
```

#### 8.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|---------|
| `DirectorMiddlewareTest` | Feature | `is_director = true` → 200 |
| `DirectorMiddlewareTest` | Feature | `is_director = false` → 403 |
| `DirectorMiddlewareTest` | Feature | Admin bypass → 200 |
| `DirectorDashboardTest` | Feature | Dashboard carga indicadores de TODA la institución (no filtrado) |
| `DirectorScopeTest` | Unit | `queryPensums()` retorna todos los pensums (sin filtro) |
| `DirectorScopeTest` | Unit | `queryProfesores()` retorna todos los profesores activos con carga |
| `DirectorReadOnlyTest` | Feature | `DirectorScopeService` NO expone métodos de escritura (reflection test) |
| `DirectorReadOnlyTest` | Feature | La vista `activity-list` NO contiene `<form>` ni atributos `wire:click` de mutación |
| `DirectorRouteTest` | Feature | No existe ninguna ruta no-GET bajo `/app/director/*` (se inspeccionan las rutas registradas) |
| `DirectorCargaAcademicaTest` | Feature | Lista pevaluacions de toda la institución |
| `DirectorProfesoresTest` | Feature | KPIs de todos los profesores |

#### 8.3 Factory support

```php
// database/factories/UserFactory.php
public function director(): static
{
    return $this->state(fn (array $attributes) => [
        'is_director' => true,
    ]);
}
```

#### 8.4 Test de no-escritura (clave para este rol)

```php
// tests/Feature/Director/DirectorReadOnlyTest.php

use App\Models\User;
use App\Services\Director\DirectorScopeService;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

// 2. El servicio NO expone métodos que muten el modelo (save/update/approve/store...)
test('DirectorScopeService no expone metodos de escritura', function () {
    $publicMethods = array_map(
        fn ($m) => $m->name,
        (new ReflectionClass(DirectorScopeService::class))->getMethods(ReflectionMethod::IS_PUBLIC)
    );

    $forbidden = ['save', 'update', 'store', 'create', 'delete', 'destroy',
                  'approve', 'reject', 'comment', 'observe', 'saveObservation'];
    foreach ($forbidden as $f) {
        $this->assertEmpty(
            array_filter($publicMethods, fn ($m) => str_contains(strtolower($m), $f)),
            "DirectorScopeService no debe exponer '$f'."
        );
    }
});

// 3. Todas las rutas /app/director/* son GET
test('todas las rutas de director son de solo lectura (GET)', function () {
    $directorRoutes = collect(RouteFacade::getRoutes())
        ->reject(fn (Route $r) => ! str_starts_with((string) ($r->uri ?? ''), 'app/director'));

    foreach ($directorRoutes as $route) {
        $this->assertNotContains('POST', $route->methods(), 'Ruta no-GET no permitida');
        $this->assertNotContains('PUT', $route->methods());
        $this->assertNotContains('PATCH', $route->methods());
        $this->assertNotContains('DELETE', $route->methods());
    }
});
```

---

## 7. ADRs (Architecture Decision Records)

### ADR-001: `is_director` como columna booleana

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Columna booleana en `users` | Tabla pivote roles |
| **Razón** | Consistencia con `is_admin`, `is_leadership`, `is_coordinacion`, `is_planner`, `is_profesor` | |
| **Consecuencia** | Migración simple. Consistencia con el ecosistema existente | |

### ADR-002: Alcance global (sin scope) para la dirección

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | El director ve TODA la institución (sin filtro por `manager_id`/áreas) | Scoping por `manager_id` (como coordinación) o por áreas (como leadership) |
| **Razón** | La dirección tiene una visión ejecutiva del conjunto. Supervisor de supervisores: ve lo que ven coordinación y leadership, pero agrega todo a nivel institucional. No tiene sentido restringir a un subconjunto porque su rol es gobernar el todo | |
| **Consecuencia** | `DirectorScopeService` no filtra por el usuario. Es la misma semántica "unrestricted" que `LeadershipService` usa para `is_admin`, pero aquí es el comportamiento *por defecto* del rol, no un bypass | |

### ADR-003: Namespace y layout dedicado para dirección

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Namespace completo propio: layout, componentes, vistas, servicio | Reusar el layout de planning/coordinacion |
| **Razón** | Igual que coordinacion/leadership: permisos y controles de navegación distintos. Evita acoplar el módulo a otro. La cadena de fallos queda aislada | |

| **Consecuencia** | ~2 archivos adicionales de layout. Navbar propia con submenú Dirección. Coste marginal mínimo, aislamiento máximo |

### ADR-004: Read-Only estricto (no-writing role)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | El módulo director **no expone ninguna acción de escritura** | Heredar la capacidad de comentar/observar de coordinacion o leadership |
| **Razón** | Es el requisito explícito: "no aprueba, no registra comentarios, solo visualización y seguimiento". La dirección supervisa, pero el que ejecuta y valida es coordinación/leadership. Introducir escritura rompería el principio de separación de responsabilidades de supervisión | |
| **Consecuencia** | No hay formularios en las vistas, no hay métodos de mutación en el servicio, no hay rutas no-GET. Se agrega un **test de no-escritura** (reflection + inspección de rutas) para evitar que un futuro PR rompa este invariante |

### ADR-005: Reuso de `ActivityPdfController` para los formatos/resumen

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Las rutas `director.activities.format` y `director.activities.resume` reutilizan el `ActivityPdfController::format()`/`resume()` ya existente | Controlador propio de PDF |
| **Razón** | El controlador es **read-only** (genera PDF), por lo que no viola el rol. Evita duplicar lógica de generación de PDF y mantiene un único punto de formato. Está protegido por el middleware `IsDirector` del grupo | |
| **Consecuencia** | El director ve exactamente el mismo PDF que coordinación/leadership. Si el formato cambia, cambia en todos los módulos a la vez (sin merma de aislamiento, porque el PDF no es una acción de escritura) |

### ADR-006: Reuso de `LessonsPrintController` por el monitor LMS de Planificación

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | La ruta `app.planning.lms.print` reutiliza `Director\LessonsPrintController::index()` (el mismo de `/app/director/lecciones/print`) | Controlador propio de impresión para planning |
| **Razón** | El controlador es **read-only** (renderiza la vista de impresión; Mermaid/KaTeX se dibujan en el navegador) y comparte la MISMA semántica de filtros y el mismo scope global (`DirectorScopeService::queryActivities()`, sin filtro por usuario). Evita duplicar la preparación de lecciones y mantiene un único punto de formato. El contexto del membrete se deduce del nombre de ruta, y el aislamiento de roles se conserva porque cada grupo de rutas exige su propio middleware (`IsDirector` vs `IsPlanner`) | |
| **Consecuencia** | La planificación ve exactamente la misma vista de impresión que la dirección (con membrete propio). Si el formato cambia, cambia en ambos módulos a la vez. Patrón espejo del ADR-005: allí el director reusaba `ActivityPdfController` de planning; aquí planning reusa un controlador de la Dirección. El rol del director no se diluye: la ruta de planning sigue exigiendo `is_planner` (un director sin `is_planner` no puede imprimirla) |

---

## 8. Dependencias y Roadmap

### 8.1 Dependencias

| Dependencia | Tipo | Estado |
|-------------|------|--------|
| Modelo `User` (columna `is_director`) | Fase 1 | Bloqueante |
| Middleware `IsDirector` + registro en Kernel | Fase 2 | Bloqueante |
| `DirectorScopeService` | Fase 3 | Bloqueante |
| Componentes de `Planning` (Pensum, Pevaluacion, Activity) | Fase 5 (solo lectura) | Existentes |
| Componentes de `Lms` (publications, sections, resources) | Fase 5 (solo lectura) | Existentes |
| `ActivityPdfController` | Fase 4-5 (reuso) | Existentes |
| Clase `Lapso` y `Lapso::current()` | Fase 5 | Existentes |

### 8.2 Roadmap por fases

| Fase | Entregable | Estimación (días) | Dependencia |
|------|-----------|--------------------|-------------|
| Fase 1 | Migración + User model (`is_director`, `isDirector()`, role_label) | 0.5 | — |
| Fase 2 | Middleware `IsDirector` + registro Kernel | 0.5 | Fase 1 |
| Fase 3 | `DirectorScopeService` + trait `HasDirectorScope` | 1 | Fase 2 |
| Fase 4 | Rutas `/app/director/*` (GET only) | 0.5 | Fase 3 |
| Fase 5 | 7 componentes Livewire + vistas | 3 | Fase 4 |
| Fase 6 | Layout dedicado + navbar items | 1 | Fase 5 |
| Fase 7 | Seguridad read-only (verificación de no-escritura) | 0.5 | Fase 5 |
| Fase 8 | Tests (unit + feature + no-escritura) | 1 | Fase 7 |
| **Total** | | **~8 días** | |

> **Mejora de UX (opcional, dependiente):** para el rol dirección, cada indicador del
> dashboard filtra por `Peducativo` sin alterar datos. No agrega complejidad de escritura.

---

## 9. Checklist de Rollback

> **Motivo central de rollback:** el requisito más delicado es el **read-only estricto**.
> Si en cualquier punto se detecta que un director puede escribir datos, el release debe
> descartarse y revertirse a esta lista.

### 9.1 Rollback de código

```bash
# Revertir todas las fases de código
git revert <sha_del_release_director> --no-commit

# Revertir sólo una fase conflictiva (ej. middleware o rutas)
git checkout <tag_anterior> -- app/Http/Middleware/IsDirector.php
git checkout <tag_anterior> -- routes/web.php
git checkout <tag_anterior> -- app/Models/User.php
```

### 9.2 Rollback de base de datos

```bash
# Revertir la migración (quita columna is_director + índice)
php artisan migrate:rollback --step=1
```

### 9.3 Eliminación de usuarios/privilegios

```sql
-- Si es necesario quitar el rol a un usuario en concreto tras el rollback:
UPDATE users SET is_director = 0 WHERE is_director = 1;
```

> **Nota:** la migración `add_is_director_to_users_table` incluye una guarda
> `if (!Schema::hasColumn(...))`, por lo que es **idempotente** y segura para
> aplicar/re-aplicar.

### 9.4 Checklist de verificación post-rollback

- [ ] La vista `admin/users` ya no muestra el toggle/estado "Dirección".
- [ ] El navbar ya no muestra el submenú "Dirección" para usuarios no admin.
- [ ] Las rutas `director.*` devuelven 404 (o middleware no registrado).
- [ ] `User::find($id)->is_director` devuelve `false` para todos los usuarios.
- [ ] Los tests de `Director*` no se ejecutan en el suite (o pasan vacíos tras revertir rutas).
- [ ] `php artisan test` en verde a nivel global tras el rollback.

### 9.5 Verificación de invariante read-only (siempre activo)

| Invariante | Verificación |
|-----------|--------------|
| No hay rutas no-GET bajo `/app/director/*` | `php artisan route:list --name=director` → solo muestra GET |
| El servicio no expone métodos de mutación | Test de reflexión `DirectorReadOnlyTest` |
| Las vistas no contienen formularios de escritura | Revisión de código + test de auditoría `<form>` |
| Los PDF se sirven vía GET reusando read-only controller | Revisión de `route:list` |

---

_Fin del blueprint del rol `is_director`._ Siguiendo el patrón de
`blueprint/coordinacion/implementations.md` y `blueprint/leadership/implementations.md`,
con la restricción explícita de ser **100% visualización y seguimiento, sin escritura.**

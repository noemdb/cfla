# Spec: Módulo de Diagramas de Flujo (`/app/planning/flow`)

**Versión:** 1.3
**Autor:** Codex / Agent Workspace
**Fecha:** 2026-08-06
**Estado:** AS-IS con mejoras de alta y media prioridad implementadas, mejora de baja prioridad (servicio+selector) y nueva infografía `activity-lesson-planning` (ver secciones 5A, 5B y 5C)

---

## 1. Resumen Ejecutivo

La interfaz **`/app/planning/flow`** es el *hub* del módulo **Diagramas de Flujo** dentro del area de **Planificación Académica**. Su propósito es listar infografías estáticas HTML que explican procesos académicos de la institución y servirlas bajo URLs protegidas del panel de planificación.

El módulo actual:

- **No usa Livewire** — es un controlador HTTP estándar + vista Blade `@extends('planning.layouts.app')`.
- **Se alimenta dinámicamente** de archivos estáticos en `docs/infografia/flujo{Studly}.html`. Cualquier archivo nuevo con prefijo `flujo` se publica automáticamente.
- **Sirve cada infografía** como archivo estático (HTML completo con su propio `<html>`, Tailwind CDN, etc.) bajo `/app/planning/diagram/flow/{slug}`.
- **Incluye cobertura de tests** en `tests/Feature/Planning/FlowDiagramTest.php` (10 tests).
- **Tiene una hoja de estilos propia** determinada por la vista Blade del hub y el layout `planning.layouts.app`.

Dado que la ruta está protegida por el grupo `app/planning` (middleware `auth + isPlanner`), el contenido es **solo para planificadores autenticados**.

---

## 2. Arquitectura Actual (AS-IS)

### 2.1 Rutas (`routes/web.php`)

```php
// Dentro del grupo:
// Route::prefix('planning')->middleware(['auth', 'isPlanner'])->name('planning.')->group(...)

Route::get('/flow', [FlowDiagramController::class, 'index'])
    ->name('flow.index');                    // → app.planning.flow.index

Route::get('/diagram/flow/{diagram}', [FlowDiagramController::class, 'show'])
    ->name('diagram.flow.show');             // → app.planning.diagram.flow.show
```

### 2.2 Controlador (`app/Http/Controllers/Planning/FlowDiagramController.php`)

| Método | Propósito |
|--------|-----------|
| `index()` | Lista los archivos `docs/infografia/flujo*.html`, genera `slug` en kebab-case y devuelve `view('planning.flow', compact('diagrams'))`. |
| `show(string $diagram)` | Valida que el slug sea `^[a-z0-9][a-z0-9-]*$`, resuelve al archivo `docs/infografia/flujo{Studly}.html` y lo sirve con `response()->file($file)`. |
| `describe(string $slug)` | Devuelve metadatos de presentación (título, descripción, badge). Actualmente tiene 3 diagramas conocidos: `activity-lesson`, `activity-lesson-planning` y `consejo-directivo`. Los desconocidos usan un título/descripción genéricos. |

**Constante clave:**

```php
private const DIAGRAMS_PATH = 'docs/infografia';
```

### 2.3 Infografías registradas (`docs/infografia/`)

| Archivo | Slug | Título | Badge | Descripción (en hub) |
|---|---|---|---|---|
| `flujoActivityLesson.html` | `activity-lesson` | Flujo de Actividad y Lección (LMS) | Actividad → Lección | Recorrido completo de una actividad académica hasta convertirse en lección visible para los estudiantes: aprobación, programación y publicación. |
| `flujoActivityLessonPlanning.html` | `activity-lesson-planning` | Planificación en el Flujo Actividad / Lección | Planning · Actividad → Lección | Casos de uso de Planning en el recorrido de una actividad académica: carga académica, aprobación de la actividad, monitorización LMS y publicación de la lección. |
| `flujoConsejoDirectivo.html` | `consejo-directivo` | Informe al Consejo Directivo · CFLA 2026 | Consejo Directivo · 2026 | Puntos presentados ante el Consejo Directivo: propuestas tecnológicas (IA y correo institucional), continuidad de SAEF 25-26, renovación del dominio web y nuevos proyectos de innovación con el fundamento metodológico de Marco Lógico. |

### 2.4 Vista Blade (`resources/views/planning/flow.blade.php`)

**Layout:** `planning.layouts.app` (navbar compartido del rol Planificación, tema dark/light con toggle).

**Sección `navbar-info`:** muestra contador de diagramas:

```blade
{{ count($diagrams) === 1 ? '1 diagrama disponible' : count($diagrams).' diagramas disponibles' }}
```

**Contenido principal:**

1. **Breadcrumb** → `Planificación` / `Diagramas de Flujo`
2. **Encabezado** → título "Diagramas de Flujo" + subtítulo "Recursos visuales que explican los procesos académicos de la institución."
3. **Grilla responsiva** (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6`) de tarjetas con:
   - Enlace absoluto que cubre toda la tarjeta (`absolute inset-0`) abriendo en `target="_blank" rel="noopener"`.
   - Icono decorativo de flujo en esquina superior derecha.
   - Icono cyan en un `div` de 48x48.
   - Badge con el `$diagram['badge']`.
   - Título `$diagram['title']`.
   - Descripción `$diagram['description']`.
   - Botón "Ver Diagrama" (también `_blank`).
   - Hover effects con `group-hover`.
4. **Nota informativa** al final sobre cómo se publican los recursos (patrón `docs/infografia/flujo{Nombre}.html` → URL `/app/planning/diagram/flow/*`).
5. **Estado vacío**: mensaje "Aún no hay diagramas de flujo publicados." en un `div` col-span-full.

**Clases/temas usados:** dark-first (`bg-gray-900/40`, `text-white`, `border-white/5`), acentos cyan (`text-cyan-400`, `bg-cyan-500/10`, `border-cyan-500/20`) y emeralds (`hover:text-emerald-300`). Incluye `fade-in` (animación del layout) y `.diagnostic-card` (hover lift definido en el layout).

### 2.5 Cobertura de Tests (`tests/Feature/Planning/FlowDiagramTest.php`)

> Al día de hoy: **16 tests** (10 originales + 4 nuevos de alta prioridad + 2 nuevos para `activity-lesson-planning`). *(Se recomienda añadir el unit test del servicio `FlowDiagramService`)*

| # | Test | Assert clave |
|---|------|---------------|
| 1 | `test_planning_dashboard_links_to_flow_hub` | El dashboard de Planning contiene link a `route('app.planning.flow.index')`. |
| 2 | `test_flow_hub_requires_auth` | Usuario anónimo → redirect a login. |
| 3 | `test_flow_hub_requires_planner` | Usuario no-planner → 403. |
| 4 | `test_flow_hub_lists_activity_lesson_diagram` | Hub contiene título y URL de `activity-lesson`. |
| 5 | `test_flow_hub_lists_consejo_directivo_diagram` | Hub contiene título y URL de `consejo-directivo`. |
| 6 | `test_flow_hub_opens_diagram_in_new_tab` | Verifica `target="_blank"`, `rel="noopener"`, no usa modal ni iframe. |
| 7 | `test_activity_lesson_diagram_is_served` | `response()->file()` sirve `flujoActivityLesson.html`. |
| 8 | `test_consejo_directivo_diagram_is_served` | `response()->file()` sirve `flujoConsejoDirectivo.html`. |
| 9 | `test_unknown_diagram_returns_404` | Slug inexistente → 404. |
| 10 | `test_diagram_requires_auth` | Diagrama sin auth → redirect a login. |

---

## 3. Flujo de Datos (Ciclo de Vida)

```mermaid
graph TD
    A[Archivo docs/infografia/flujoNombre.html] --> B[FlowDiagramController@index]
    B --> C[Slug = kebab 'Nombre']
    C --> D[Vista planning.flow: tarjetas]
    D --> E[HUB /app/planning/flow]
    E --> F[Usuario clic en tarjeta o botón]
    F --> G[GET /app/planning/diagram/flow/{slug} with _blank]
    G --> H[FlowDiagramController@show]
    H --> I[Resuelve flujo{Studly}.html]
    I --> J[response()->file]
```

---

## 4. Fortalezas del Estado Actual

- **Publicación declarativa**: un simple archivo `.html` en `docs/infografia/` queda disponible sin tocar código.
- **Tests de regresión**: el módulo tiene cobertura sólida de rutas, permisos, contenido del hub y servicio de archivos.
- **UI consistente**: usa el layout de Planning, tema dark/light, tipografía y paleta del sistema.
- **Accesibilidad básica**: links con `aria-label`, `target="_blank"` y `rel="noopener"`.
- **Sin dependencias Livewire**: simple y veloz; no introduce estado en el servidor.

---

## 5A. Mejoras de Alta Prioridad — IMPLEMENTADAS

Los siguientes ítems de la sección 5 fueron implementados en esta sesión:

1. **Thumbnails / vista previa en las tarjetas** ✅
   - Cada tarjeta ahora tiene un bloque superior `h-36` con **mini-diagrama conceptual SVG** (3 nodos unidos por líneas de flujo) más acento de color.
   - Acento diferenciado por diagrama: `activity-lesson` → cyan; `consejo-directivo` → emerald. (`FlowDiagramController::describe()` expone `accent`).

2. **Orden personalizado de diagramas** ✅
   - El controlador ahora ordena por `order` en lugar de alfabético por `slug`.
   - `activity-lesson` (order=1), `consejo-directivo` (order=2) y `activity-lesson-planning` (order=3). Fallback desconocido: `order=999`.

3. **Metadatos de tarjeta (accesibilidad/SEO)** ✅
   - Añadido `label` descriptivo en `describe()` usado como `aria-label` del enlace absoluto de la tarjeta.
   - Añadido `title` con el título completo del diagrama en el enlace principal y en el botón "Ver Diagrama".
   - Añadidos `tags` por diagrama mostrados como chips en la tarjeta (`LMS`, `Publicación`, `Consejo Directivo`, `2026`...).
   - Los clases de acento (`text-*`, `bg-*`, `border-*`) ahora son dinámicas según `accent` (`cyan` o `emerald`) en lugar de siempre cyan.

**Archivos tocados:**

| Archivo | Cambio |
|---|---|
| `app/Http/Controllers/Planning/FlowDiagramController.php` | `describe()` añade `order`, `accent`, `tags`, `label`; `index()` ordena por `order`. |
| `resources/views/planning/flow.blade.php` | Thumbnail visual, tags chips, `aria-label`/`title`, acento dinámico, enlace duplicado eliminado. |
| `tests/Feature/Planning/FlowDiagramTest.php` | Se añadieron 4 tests: thumbnail preview, orden, accesibilidad labels, tags chips. |
| `blueprint/flow/modulo-flow-as-is.md` | Actualizado a v1.1 con sección 5A. |

---

## 5B. Mejoras de Prioridad Media — IMPLEMENTADAS

Los siguientes ítems de la sección 5 también fueron implementados en esta sesión:

4. **Modal / vista previa sin salir del hub** ✅
   - Cada tarjeta tiene un botón **"Vista previa"** que abre un modal con `<iframe>` (Alpine.js `x-show`, overlay con backdrop blur).
   - El modal incluye: título del diagrama, botón cerrar, y el iframe ocupa el ancho completo.
   - Se mantiene el botón **"Ver Diagrama"** con `target="_blank"` como acción principal.

5. **Búsqueda y filtro en el hub** ✅
   - Campo de búsqueda (Alpine `x-model`) que filtra por título, descripción, badge, categoría y tags.
   - Select de **filtro por categoría** (opciones deducidas de los diagramas: LMS, Informe, General...).
   - Mensaje de "no se encontraron diagramas" con botón para limpiar filtros.

6. **Más metadatos por diagrama** ✅
   - `FlowDiagramController::describe()` ahora incluye: `category`, `duration`, `audience`, `status`, y `updated_at` (este último se calcula con `filemtime()` del archivo HTML en `index()`).
   - La tarjeta muestra chips de categoría, fecha de actualización, duración estimada y audiencia.

7. **Estados visuales de diagramas** ✅
   - Badge de estado en cada tarjeta: **nuevo**, **actualizado**, **desactualizado**, **vigente** (fallback).
   - Clases de color según estado (emerald/cyan/red/gray).

**Archivos tocados (media prioridad):**

| Archivo | Cambio |
|---|---|
| `app/Http/Controllers/Planning/FlowDiagramController.php` | `describe()` añade `category`, `duration`, `audience`, `status`; `index()` calcula `updated_at` vía `filemtime()`. |
| `resources/views/planning/flow.blade.php` | Modal preview (Alpine + iframe), búsqueda/filtro client-side, chips de metadatos, badge de estado. |
| `tests/Feature/Planning/FlowDiagramTest.php` | Nuevos tests: search/filter, status badge, metadatos, preview modal. Test `test_flow_hub_opens_diagram_in_new_tab` actualizado (ya no asume no-iframe). |
| `blueprint/flow/modulo-flow-as-is.md` | Actualizado a v1.2 con sección 5B. |

---


**Bugfix registrado (dentro de esta sesión):**

- Se detectó temporalmente un `Invalid numeric literal` en `FlowDiagramController::describe()` cuando `updated_at` se definió literalmente como `2026-08-03` (PHP lo interpreta como resta aritmética). 
- **Solución aplicada**: `updated_at` ya NO se define como literal; se calcula desde `filemtime()` del archivo HTML en `index()` (`$described['updated_at'] = $mtime ? date('Y-m-d', $mtime) : null;`).
- Verificado con `php -l` y `artisan tinker` (index/show OK).
- Si el error persiste en el navegador, limpiar OPcache/restart PHP-FPM (el repo ya pasó `php artisan optimize:clear`).

---

## 5C. Mejoras de Prioridad Baja + Nueva Infografía Planning — IMPLEMENTADAS

Los siguientes ítems de baja prioridad fueron implementados junto con la nueva infografía con los casos de uso de **Planning**:

8. **Refactor a servicio reutilizable** ✅
   - Nueva clase `app/Services/Planning/FlowDiagramService.php` que centraliza descubrimiento, orden, metadatos y resolución de archivos.
   - `FlowDiagramController` inyecta el servicio por constructor (property promotion `private readonly FlowDiagramService`).
   - `describe()` vive ahora en el servicio; `list()` calcula `updated_at` vía `filemtime()`.

9. **Selector de orden en el hub** ✅
   - En la vista se agregó un selector **"Orden: por relevancia / más recientes / por categoría"**.
   - El estado Alpine `sortBy` alimenta el getter `allDiagrams` que reordena `recent`, `category` o `order`.

10. **Responsive de la nota final** ✅
    - Se agregó `break-all` al `<code>` de URLs en la nota "¿Cómo se publican estos recursos?".

11. **Nueva infografía: `activity-lesson-planning`** ✅
    - Archivo creado: `docs/infografia/flujoActivityLessonPlanning.html`.
    - Es un diagrama de **actores del proceso activity-lesson con enfoque en los casos de uso de Planning**: los 5 actores, una sección de casos de uso (carga académica, aprobación, programación/publicación, monitor y auditoría), el viaje de la actividad en 8 pasos, los 6 candados y los 4 estados.
    - Estructura markdown de diseño: `blueprint/flow/diagrama-activity-lesson-planning.md`.
    - Metadatos registrados en `FlowDiagramService::describe()` como 3er diagrama con `order=3`, `accent=cyan`, `category=Planning`, `status=nuevo`.

**Archivos tocados (baja prioridad + nueva infografía):**

| Archivo | Cambio |
|---|---|
| `app/Services/Planning/FlowDiagramService.php` | Nuevo servicio; metadatos incluyen `activity-lesson-planning`. |
| `app/Http/Controllers/Planning/FlowDiagramController.php` | Usa `FlowDiagramService`; `describe()` movida al servicio. |
| `resources/views/planning/flow.blade.php` | Selector de orden (`sortBy`), `break-all` en URLs. |
| `docs/infografia/flujoActivityLessonPlanning.html` | Nueva infografía de actores con enfoque en los casos de uso de Planning. |
| `blueprint/flow/diagrama-activity-lesson-planning.md` | Estructura markdown de la infografía (actualizada al enfoque de casos de uso de Planning). |
| `blueprint/flow/modulo-flow-as-is.md` | Actualizado a v1.3 con sección 5C. |

---

## 5. Oportunidades de Mejora (listado ordenado por prioridad de impacto)

> Los ítems están ordenados del mayor impacto/beneficio al menor, asumiendo esfuerzo razonable.

### 🔴 Alta prioridad (rápido, alto valor visual/funcional)

1. **Thumbnails / vista previa en las tarjetas**
   - Agregar una miniatura (imagen / screenshot / captura del HTML) a cada tarjeta del hub para que el usuario vea de qué trata el diagrama antes de abrirlo.
   - Archivos clave: `resources/views/planning/flow.blade.php`, `public/` (assets), posiblemente `FlowDiagramController::describe()`.
   - Impacto: alto — mejora visibilidad y decisión del usuario.

2. **Orden personalizado de diagramas**
   - Actualmente se ordena alfabéticamente por `slug` (`activity-lesson` antes que `consejo-directivo`).
   - Opciones: añadir campo `position`/`order` en `describe()`, o un archivo de metadatos (YAML/JSON en `docs/infografia/`).
   - Impacto: medio-alto — permite priorizar contenido (p. ej. mostrar el más usado primero).

3. **Optimización de metadatos de tarjeta (accesibilidad y SEO)**
   - `aria-label` descriptivo en cada enlace, `title` con texto completo, `<meta name="description">` opcional.
   - Impacto: medio — mejora accesibilidad y contexto.

### 🟡 Prioridad media (mejor experiencia, más trabajo)

4. **Modal / vista previa sin salir del hub**
   - Alternativa a abrir siempre `_blank`: botón "Vista previa" que abre un modal (Livewire/Blade + Alpine) cargando la infografía en un `<iframe>`.
   - Mantener el botón "Abrir en pestaña" como acción secundaria.
   - Impacto: medio-alto — reduce contexto-switching; requiere actualizar tests (el test que verifica no-iframe tendría que ajustarse).

5. **Búsqueda y filtro en el hub**
   - Campo de búsqueda por título/descripción/tags y filtro por categoría.
   - Implementación: puede ser client-side con Alpine.js (sin cambiar el controlador) o Livewire.
   - Impacto: medio — útil cuando crezca el catálogo de diagramas.

6. **Más metadatos por diagrama**
   - Extender `describe()` con: fecha de creación/actualización, duración estimada, audiencia objetivo, tags, categoría.
   - Impacto: medio — enriquece las tarjetas y permite filtros/estados.

7. **Estados visuales de diagramas**
   - Marcar como "🔴 Nuevo", "🟡 Actualizado", "🟢 Vigente", "⚪ Desactualizado".
   - Impacto: medio — comunica frescura del contenido.

### 🟢 Baja prioridad (pulido / mantenimiento)

8. **Ajuste responsive de la nota final**
   - Revisar que el bloque "¿Cómo se publican estos recursos?" se vea bien en móvil (break de `code` largos, word-break, wrap).
   - Impacto: bajo — pulido de calidad.

9. **Ordenar por fecha o categoría en el hub**
   - Añadir selector "Ordenar por: Más reciente / Alfabético / Categoría".
   - Impacto: bajo a medio — depende de implementar metadatos (ítem 6).

10. **Refactor del controlador hacia un servicio reutilizable** (opcional)
    - Extraer la lógica de descubrir/servir diagramas de `FlowDiagramController` a un `DiagramService` para facilitar tests unitarios y reutilización futura.
    - Impacto: bajo a medio — mejora testability; no cambia UI.

---

## 6. Checklist de Validación (comando rápido)

```bash
php artisan test --filter=FlowDiagramTest
```

Deben pasar los **16 tests** existentes.

---

## 7. Referencias Relacionadas

- `docs/infografia/flujoActivityLesson.html` — Infografía LMS (56 KB, 2026-08-03)
- `docs/infografia/flujoActivityLessonPlanning.html` — Infografía Planning en el flujo Actividad-Lección (nueva, 2026-08-06)
- `docs/infografia/flujoConsejoDirectivo.html` — Infografía Consejo Directivo (132 KB, 2026-08-04)
- `docs/infografia/` (folder) — Directorio con los documentos estáticos publicados
- `docs/superpowers/plans/2026-08-03-consejo-directivo-deck-design.md` — Spec de diseño del consejo directivo
- `blueprint/mermaid/README.md` — Notas sobre el paquete `laravel-mermaid`

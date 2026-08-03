# Diseño: Vista de impresión HTML de lecciones LMS (reemplaza el PDF)

**Fecha:** 2026-08-03
**Estado:** Aprobado
**Contexto:** Las lecciones LMS del profesor se exportaban a PDF con dompdf. dompdf no ejecuta JavaScript, por lo que los diagramas Mermaid no podían renderizarse de forma nativa (se intentó Graphviz server-side). La decisión es **abandonar el PDF** y renderizar todo en **una página HTML autónoma** donde Mermaid corre en el navegador.

## Objetivo

Reemplazar el botón "Exportar PDF" del listado LMS del profesor por una página HTML autónoma (estilo "vista de impresión") que renderice todas las lecciones visibles con sus secciones, contenidos, diagramas Mermaid nativos, matemáticas (KaTeX), recursos y enlaces. El profesor puede imprimir / guardar como PDF desde el navegador con `window.print()`, obteniendo los diagramas ya dibujados.

## Decisiones confirmadas con el usuario

1. **Forma:** Página HTML autónoma ("vista de impresión"), sin la navegación de la app.
2. **Código del PDF:** Eliminar por completo (controlador, servicio Graphviz, vista y ruta).

## Arquitectura

### 1. Ruta y controlador

- **Ruta nueva:** `GET /app/profesors/lms/lessons/print`
  → `LessonsPrintController@index`, middleware `auth` + `isProfesor`.
  Nombre: `app.profesors.lms.lessons.print`. **Reemplaza** `app.profesors.lms.lessons.pdf`.
- **`app/Http/Controllers/Profesor/Lms/LessonsPrintController.php`** (nuevo):
  - Reutiliza la query de `LessonsPdfController::export()`: filtros `lapso`, `pestudio`, `grado`, `seccion`, `search` sobre `Activity::whereHas('pevaluacion', …)` con eager-loads (`lmsPublication`, `lmsSections.contents`, `lmsHtmlEmbeds`, `lmsResources`, `lmsLinks`).
  - Conserva `prepareLesson()`, `estadoLabel()`, `estadoClass()` del controlador PDF.
  - Devuelve `view('profesor.lms.lessons-print', compact('lessons', 'institucion', 'filters', 'profesor') + ['fecha' => …])`.
  - Sin dompdf.

### 2. Vista autónoma `resources/views/profesor/lms/lessons-print.blade.php`

Documento HTML completo, **sin** layout de la app:

- **`<head>`:**
  - `@vite(['resources/css/app.css', 'resources/js/app.js'])` (mermaid + `mermaidEmbed`).
  - `@livewireScripts` (provee Alpine, necesario para `mermaidEmbed`; confirmado que Alpine **no** viene de `app.js` sino del bundle de Livewire).
  - `@livewireStyles`.
  - `<style>` de impresión: `@media print` oculta la barra de acciones, aplica `page-break-before` por lección, márgenes, tamaño `letter`.
- **Barra sticky superior** (oculta en `@media print`): encabezado institucional, profesor, fecha y botón **"Imprimir / Guardar PDF"** (`onclick="window.print()"`).
- **Por lección:** cabecera (tema + estado), fila de metadatos (asignatura, grado, sección, lapso, fechas, eje temático), luego secciones.
- **Cada bloque de contenido** (patrón `_full-preview-modal.blade.php:99-124`):
  - Detectar Mermaid: `class="… mermaid …"` **o** keyword inicial (`flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline`).
  - Si Mermaid → extraer código (`strip_tags` del div o del body) y envolver:
    ```html
    <div wire:ignore x-data="mermaidEmbed()" data-mermaid-code="{{ $code }}" class="…">
        <div x-ref="target" class="w-full"></div>
    </div>
    ```
  - Si no → `<x-lms.math-text :content="$renderer->renderContentBody($body)" … />` (maneja markdown y LaTeX/KaTeX).
- **Embeds (`lmsHtmlEmbeds`):**
  - `is_mermaid` → wrapper `mermaidEmbed()` con `data-mermaid-code="{{ $embed->html_content }}"`.
  - Si no → HTML sanitizado.
- **Recursos y enlaces** al final de cada lección (igual que el PDF).
- Sin interactividad Livewire: es una vista de controlador; solo necesita Alpine + mermaid del bundle.

### 3. Reutilización (sin cambios)

- `mermaidEmbed()` y `window._ensureMermaidReady`/`loadMermaid()` ya están en `app.js` (vía `lms-student-preview.js`) → disponibles con `@livewireScripts` + `@vite`.
- `LmsContentRendererService::renderContentBody()` para markdown/LaTeX.
- `x-lms.math-text` para matemáticas en navegador.
- La detección Mermaid se hace en la vista (patrón existente), sin tocar los servicios.

### 4. Eliminación (código del PDF)

- `app/Http/Controllers/Profesor/Lms/LessonsPdfController.php`
- `app/Services/Lms/LmsPdfContentRendererService.php` (todo el pipeline Graphviz)
- `resources/views/pdfs/profesor/lms/lessons.blade.php` (y `resources/views/pdfs/profesor/` si queda vacía)
- Ruta `lessons.pdf` en `routes/web.php`
- Botón del listado: `_list.blade.php` línea ~10 cambia a `route('app.profesors.lms.lessons.print')`, label **"Ver / Imprimir"**, ícono de impresora.

## Flujo de datos

```
_profesor/lms/_list.blade.php ("Ver / Imprimir")
  → GET /app/profesors/lms/lessons/print?lapso=1&…
  → LessonsPrintController@index  (query filtrada + eager-loads)
  → view profesor.lms.lessons-print
  → HTML con data-mermaid-code embebido
  → navegador: Alpine + mermaid renderiza cada diagrama a SVG
  → "Imprimir / Guardar PDF" (window.print) → PDF con diagramas
```

## Manejo de errores

- **Sin contenido:** mostrar mensaje "No hay lecciones que coincidan con los filtros aplicados" (como el PDF actual).
- **Filtros inválidos / actividad sin secciones:** se omiten secciones vacías (mismo comportamiento que `prepareLesson` actual).
- **Mermaid con error de sintaxis:** `mermaidEmbed` ya usa `suppressErrorRendering: true`; el bloque queda en blanco en vez de romper la página.

## Verificación

1. `npm run build` (o `npm run dev`) para compilar el bundle.
2. Autenticarse como `ccortez23` y abrir `GET /app/profesors/lms/lessons/print?lapso=1`:
   - Diagramas Mermaid visibles como SVG (no texto fuente).
   - Matemáticas renderizadas con KaTeX.
   - Botón "Imprimir / Guardar PDF" → el PDF generado contiene los diagramas.
3. `php artisan test` para no romper otros módulos.
4. Verificar que no quedan referencias a `lessons.pdf`, `LessonsPdfController`, `LmsPdfContentRendererService` ni `pdfs.profesor`.

## Fuera de alcance (YAGNI)

- No se conserva el PDF server-side ni Graphviz.
- No se añade un layout de la app a la vista de impresión.
- No se toca el renderizado de wizard/estudiante.

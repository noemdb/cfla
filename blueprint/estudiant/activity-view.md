# Vista de Actividad (Detalle de Lección)

**Detalle de una lección LMS — `/app/estudiante/activity/{activity}`**
_Última revisión:_ 2026-08-05

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Componente Livewire](#2-componente-livewire)
3. [Bloque P1 · Consistencia emerald + pulido](#3-bloque-p1--consistencia-emerald--pulido)
4. [Bloque P2 · Superficie blanca del contenido](#4-bloque-p2--superficie-blanca-del-contenido)
5. [Bloque P3 · Diagramas SVG](#5-bloque-p3--diagramas-svg)
6. [Bloque P4 · Navegación de lectura](#6-bloque-p4--navegación-de-lectura)
7. [Arquitectura de contenido (templates)](#7-arquitectura-de-contenido-templates)
8. [Paleta y convenciones](#8-paleta-y-convenciones)
9. [Seguridad (invariantes)](#9-seguridad-invariantes)

---

## 1. Visión General

La **Vista de Actividad** es la página donde el estudiante lee una lección LMS publicada (o su preview). Muestra el encabezado con metadatos de publicación, un banner de "vista previa" cuando la lección aún no se publica, las **secciones** de contenido (cada una con sus pasos tipados: texto, imagen, video, embed, file preview, audio, html), recursos descargables, enlaces, contenido embebido y comentarios.

### Propósito

- Leer el contenido de la lección de forma estructurada por **secciones** y **pasos numerados**
- Mostrar el **estado** de la lección (Publicada / Vista previa / Completada)
- Permitir **marcar como completada** y **comentar** la lección
- Presentar diagramas (Mermaid y SVG embebidos) con contenedor ligero (autores light)
- Dar **navegación de lectura**: tabla de contenido + barra de progreso de scroll + scroll-spy

### Arquitectura

```
ActivityView (Livewire full-page component)
├── routes/web.php → Route::prefix('app/estudiante') → name('student.lms.activity')
├── app/Livewire/Student/Lms/ActivityView.php
│   ├── mount(Activity $activity) → publicaciones visibles, preview, scope
│   ├── markComplete() → LmsActivityLog COMPLETE
│   ├── saveComment() → ActivityComment (validación + aprobación)
│   └── render() → $sections, $resources, $links, $htmlEmbeds, $comments…
└── resources/views/livewire/student/lms/activity-view.blade.php
    ├── [P4] Barra de progreso de lectura (sticky, scroll-spy)
    ├── Navegación "Volver a Lecciones"
    ├── Header (título, fechas publish_at/unpublish_at, badges)
    ├── Banner de vista previa (si $isPreview)
    ├── [P4] Tabla de contenido (si >1 sección)
    ├── Secciones (templates por tipo: prose/concept/list/quote/question/activity/mermaid)
    ├── Recursos / Enlaces / Embeds no vinculados
    ├── Comentarios (form + lista)
    ├── Footer "Volver / Marcar como completada"
    └── [P2][P3] <style> .lms-content (light, aplica siempre) + .lms-svg-diagram + scroll-margin
        └── [P4] @once <script> Alpine.readingNav (progreso + scroll-spy)
```

---

## 2. Componente Livewire

**Clase:** `app/Livewire/Student/Lms/ActivityView.php`
**Vista:** `resources/views/livewire/student/lms/activity-view.blade.php`
**Ruta:** `GET /app/estudiante/activity/{activity}` → `student.lms.activity` (middleware `auth` + `isStudent`)

Métodos relevantes:

| Método | Responsabilidad |
|--------|-----------------|
| `mount(Activity $activity)` | Resuelve `$isPreview` (now < publish_at), valida visibilidad para la sección del estudiante |
| `markComplete()` | Registra `LmsActivityLog` con evento `COMPLETE` |
| `saveComment()` | Crea `ActivityComment`, valida `newComment` (máx. 1000 chars), re-render |
| `render()` | Prepara `$sections` (con `visibleContents`), `$resources`, `$links`, `$htmlEmbeds`, `$comments` |

---

## 3. Bloque P1 · Consistencia emerald + pulido

**Objetivo:** unificar el acento visual de la app del estudiante (emerald) en el detalle de lección y pulir detalles.

### Cambios aplicados

1. **Reemplazo `mint` → `emerald`** en toda la vista (72 ocurrencias vía `sed`): clases `mint-*` inexistentes por las utilitarias `emerald-*` reales de Tailwind.
   - `$accentDot = 'bg-emerald-500'`, `$accentRing = 'ring-emerald-500/20'` (en vez de `mint`).
2. **Fechas localizadas** — `\Carbon\Carbon::parse($pub->publish_at)->translatedFormat('j M Y')` (y `unpublish_at`, y el mensaje del banner de preview con `H:i`). Evita `->format()` en inglés y respeta el locale `es`.
3. **Form de comentarios** — `textarea` estilizado (focus ring emerald, `placeholder`, `maxlength="1000"`) y botón con estado `wire:loading` ("Enviando…").
4. **Tarjetas con `dark:bg-gray-800/50`** — en P1 toda tarjeta llevaba fondo oscuro translúcido para el modo dark del layout (`.dark` forzado en `<html>`). **Superado para el cuerpo de sección por el Bloque P2 · Superficie blanca** (ver §4): las tarjetas de contenido y pasos ahora son `bg-white` permanente; `dark:bg-gray-800/50` sigue solo en tarjetas auxiliares standalone (TOC, recursos, referencias, embeds, comentarios, footer) y en la banda del header de sección.
5. **Imagen sin media → fallback** con detección `$isSvgBody` (ver Bloque P3) y label "Diagrama" / "Imagen no disponible".

### Resultado

- Acento visual coherente con navbar/home (emerald-500/600), badges "Publicada", "Completada", botones primarios.
- Fechas en español.
- Dark mode consistente en todas las superficies.

---

## 4. Bloque P2 · Superficie blanca del contenido

**Objetivo:** que el cuerpo de contenido renderizado (`x-lms.math-text` con clase `lms-content`) y todos sus bloques asociados tengan **fondo blanco (`#fff`) incluso en dark mode** — la lección se lee como una "hoja de documento" clara dentro del layout oscuro. La fuente se ajusta para **garantizar legibilidad** (texto oscuro sobre blanco).

### Cambio (sustituye la solución previa de overrides dark)

- El bloque **`.dark .lms-content`** (que en la versión anterior redefinía el contenido a slate oscuro) fue **ELIMINADO** de `activity-view.blade.php`.
- El bloque `.lms-content` **light** (heredado, `!important`) aplica **siempre**, también en dark mode: texto `#1e293b`, tablas `#DDF6D2`/`#ECFAE5`, `pre`/`code` claros.
- Las **tarjetas de los pasos** (`concept`, `list`, `quote`, `question`, `activity`, `prose`, IMAGE/AUDIO/HTML/embed/media, link y resource cards) pasan de `dark:bg-gray-800/50` a **`bg-white` permanente**, y sus textos pierden la variante dark: `text-gray-900`, `text-gray-600`, `text-blue-800`, `text-amber-700`, `text-sky-900`, `text-emerald-800`.
- `student-resource-card.blade.php`: también `bg-white` permanente + alineación `mint-*` → `emerald-*` (botones `bg-emerald-600`, iconos `text-emerald-700`, fondo `bg-emerald-100`).

### Regla (seguir)

- **La superficie de contenido es blanca siempre** — NO agregar `dark:bg-*` ni `dark:text-*` a ningún bloque del cuerpo de sección. Los `dark:` solo se admiten en el layout, en las tarjetas auxiliares que permanecen oscuras (ver abajo) y en la banda del header de sección.
- El `.lms-content` light no debe tocarse: sus hexes claros ya producen texto oscuro legible sobre blanco.
- **Legibilidad:** todo texto sobre la superficie blanca debe usar gris oscuro (`text-gray-900`/`text-gray-600`) o el tono light de su acento (`text-blue-800`, `text-amber-700`, `text-emerald-800`, `text-sky-900`); evitar `text-gray-500`/`dark:text-white` en párrafos de lectura.

### Superficies que permanecen oscuras (intencional)

- **Banda del header de sección** (`dark:bg-gray-800/70`) con título `dark:text-white` — es el marco/etiqueta de la sección (badges de rol, pill "N pasos").
- **Tarjetas standalone:** TOC, Recursos descargables, Referencias y enlaces, Contenido embebido no vinculado, Comentarios (chip `dark:bg-gray-800/50`, texto `dark:text-gray-100`), empty state y footer "Volver/Marcar completada".
- Contenedores de media: **Mermaid**, **IMAGE** y **EMBED** ya eran `bg-white` (se mantienen); **VIDEO** `bg-black` (se mantiene).

---

## 5. Bloque P3 · Diagramas SVG

**Objetivo:** mostrar bien los SVG embebidos en contenido (tipo `IMAGE` sin `media`) y hacerlos responsive.

### Detección

En el fallback de `IMAGE` sin `media`:

```php
$isSvgBody = str_contains($content->body ?? '', '<svg');
```

- Si es `true` → el cuerpo se interpreta como **diagrama**: label "Diagrama" (icono ámbar), clase `lms-svg-diagram overflow-x-auto`.
- Si es `false` → label "Imagen no disponible" + mensaje en itálica.

### CSS

```css
.lms-svg-diagram svg {
    max-width: 100% !important;
    height: auto !important;
}
```

- Impide que el SVG desborde el contenedor en móvil.
- `overflow-x-auto` permite scroll horizontal si el SVG aún es muy ancho.

---

## 6. Bloque P4 · Navegación de lectura

**Objetivo:** dar al estudiante orientación al leer lecciones largas: barra de progreso de scroll, tabla de contenido y scroll-spy.

### 6.1 Barra de progreso de lectura

- Insertada al inicio del root `<div>` (debajo del navbar sticky).
- `sticky top-14 z-20` (navbar es `top-0 z-30 h-14`), altura `3px`, gradiente `from-emerald-600 to-emerald-400`, ancho ligado a `progress`:
  ```html
  <div class="sticky top-14 z-20 -mx-3 sm:-mx-6 md:-mx-8 -mt-4 sm:-mt-6 md:-mt-8">
      <div class="h-[3px] bg-gradient-to-r from-emerald-600 to-emerald-400 transition-[width] duration-150 ease-out"
           :style="`width: ${progress}%`"></div>
  </div>
  ```
- Margen negativo a ancho completo dentro del contenedor `max-w-4xl`.
- `progress` = `scrollTop / (scrollHeight - innerHeight)` redondeado, con `min(100, …)`.

### 6.2 Tabla de contenido (TOC)

- Se muestra solo si `$sections->count() > 1`.
- Tarjeta `bg-white dark:bg-gray-800/50` con cabecera "Contenido" + conteo `{{ $sections->count() }} secciones`.
- Grid de enlaces `2-col` en `sm+`, cada uno con badge numerado y título truncado.
- Cada enlace:
  ```html
  <a href="#seccion-{{ $section->id }}" @click.prevent="goTo({{ $section->id }})"
     :class="activeId === {{ $section->id }} ? 'bg-emerald-50 dark:bg-emerald-500/10 …' : '…'">
  ```
- Estado activo destacado en emerald; `@click.prevent` delega el scroll a `goTo` (smooth) en vez del salto nativo.

### 6.3 Anclas de sección

- Cada `<section>` ahora lleva `id="seccion-{{ $section->id }}"`.
- Compensación del navbar sticky vía CSS (no depende de utilidad Tailwind que podría no estar compilada):
  ```css
  [id^="seccion-"] { scroll-margin-top: 5.5rem; }
  ```

### 6.4 Alpine `readingNav`

- Registro inline con guard (`Alpine._readingNavRegistered`), colocado DENTRO del root `<div>` (Livewire exige un solo root).
- Estado: `progress` (0–100) y `activeId` (id de sección activa).
- `init()` → `update()` inicial + listener `scroll` pasivo.
- **Scroll-spy:** recorre `[id^="seccion-"]`, toma la última sección cuyo tope pasó la línea de lectura (`offset = 120px`); si ninguna, cae a la primera.
- `goTo(id)` → `scrollIntoView({ behavior: 'smooth', block: 'start' })`. Se evitó `scroll-behavior: smooth` global para no afectar la navegación de Livewire.

### Comportamiento esperado

- Al cargar: barra en 0, primera sección activa.
- Al hacer scroll: la barra avanza y el TOC resalta la sección en pantalla.
- Clic en TOC: scroll suave hasta la sección (el `scroll-margin-top` evita que quede bajo el navbar).

---

## 7. Arquitectura de contenido (templates)

Cada `visibleContents` se tipa con `$tpl` (bloque `@php` en `@case('TEXT')`):

| Template | Detonante | Presentación |
|----------|-----------|--------------|
| `mermaid` | clase `mermaid` o sintaxis `flowchart/graph/…` | contenedor `mermaidEmbed()` (bg-white, zoom/fullscreen) |
| `activity` | verbos "actividad/ejercicio/resuelve…" y `textLen < 600` | borde dashed ámbar + label "Actividad" |
| `question` | `¿…?` / "pregunta/¿qué/¿cómo…" | borde sky + label "💭" |
| `quote` | `blockquote` o guiones `«»` con `textLen < 300` | borde ámbar + comilla serif |
| `list` | `<ul>`/`<ol>` | label "📋 Lista" |
| `concept` | párrafo corto (`10 < textLen < 250`, sin listas) | borde-l emerald + label "💡" |
| `prose` | default | tarjeta simple con `math-text` |

Todo el HTML de texto pasa por **`x-lms.math-text`** (seguridad: ver §9). Los tipos no-texto (`IMAGE`, `VIDEO`, `EMBED`, `FILE_PREVIEW`, `AUDIO`, `HTML`) se manejan en `@switch`.

---

## 8. Paleta y convenciones

- **Acento global estudiante:** emerald (navbar `bg-emerald-500/10 text-emerald-400`, botones `bg-emerald-600`, badges `bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300`).
- **Surfaces:** **contenido de sección → `bg-white` permanente** (superficie clara incluso en dark mode, ver §4), bordes `border-gray-200`; **tarjetas auxiliares standalone → `dark:bg-gray-800/50`**, bordes `dark:border-gray-700/50`.
- **Badges de sección** por rol (heuristicas regex): `INICIO` (blue), `DESARROLLO` (emerald), `CIERRE` (amber).
- **Tipografía:** títulos `font-bold`, contenido `text-[15px] leading-relaxed`, pasos numerados `text-[10px] font-bold`.
- **Interacciones:** transiciones 150–200ms, `ease-out`, hover en emerald translúcido.

---

## 9. Seguridad (invariantes)

⚠️ **NO modificar** el componente `resources/views/components/lms/math-text.blade.php`. Contiene el pipeline de sanitización crítico:

1. **DOMPurify** con `ADD_ATTR = ['x-data', 'x-ref', 'wire:ignore']` sobre el HTML del contenido.
2. **Strip de regex KaTeX CVE-2025-1390** (mitiga ReDoS del `\html` de KaTeX).
3. **`Alpine.initTree`** tras inyectar, para re-inicializar las directivas Alpine del contenido.

Cualquier cambio en la vista de actividad que toque contenido renderizado debe pasar por `math-text` y **nunca** evadir la sanitización con `{!! $content->body !!}` salvo en los tipos ya existentes y controlados (`IMAGE` fallback SVG con `$isSvgBody`, `EMBED`, `HTML`, `html_embed`), que corresponden a contenido autorado por el equipo (profesores/planificación), no por estudiantes.

### Validación

- Suite: `php artisan test` → **268 passed (709 assertions)**.
- `php artisan view:cache` falla a nivel repo por error preexistente (`heroicon-m::x-mark`), por lo que la validación de blades se hace vía tests, no con `view:cache`.

---

## Referencias cruzadas

- [Dashboard de Progreso](progress-dashboard.md) — página `/app/estudiante/home` (misma paleta emerald, mismos patrones de tarjetas).
- [activity-lifecycle.md](activity-lifecycle.md) — ciclo de vida de publicaciones (`publish_at`, `unpublish_at`, previews).
- [paletteColor.md](paletteColor.md) — historia de la paleta de colores del área estudiante.

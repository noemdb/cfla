# Modo Libro (StPageFlip) en ActivityView — Design

**Fecha:** 2026-08-06
**Origen:** `blueprint/estudiant/page-flip-adapted.md` (revisado y ajustado)
**Componente:** `app/Livewire/Student/Lms/ActivityView` + `resources/views/livewire/student/lms/activity-view.blade.php`
**Dependencia nueva:** `page-flip` (StPageFlip) — aún no instalada en `package.json`.

---

## 1. Resumen

Añadir una **vista alternativa "modo libro"** (flipbook con StPageFlip) al detalle de lección del estudiante. Es un **toggle opt-in** dentro de la página existente (`ActivityView`): el scroll (vista actual) sigue siendo el default y el modo libro es una capa de lectura en páginas que se voltean, con **una página por sección visible**.

El contenido interactivo (diagramas Mermaid) se muestra como **placeholder enlazado** dentro del libro: una tarjeta que ofrece saltar a la sección en modo scroll. Esta decisión mantiene intacta la invariante de seguridad de `math-text` (DOMPurify + KaTeX) y evita la pelea con MutationObserver al mover nodos del DOM.

### Decisiones confirmadas con el usuario

| Decisión | Elección |
|----------|----------|
| Contenido interactivo en páginas del libro | **Placeholder enlazado** (spec v1.1) |
| Entrada al modo libro | **Opt-in**: segmented control en la barra sticky (scroll sigue siendo default) |

---

## 2. Objetivos y no-objetivos

### Objetivos
- **Una página por sección visible** (`lmsSections()` con `is_visible = true`, ordenadas por `sort_order`), manteniendo el mapeo del spec original al modelo real.
- **Opt-in por toggle** con `$flipEnabled` calculado en `mount()`; el primer pintado y los 42 tests existentes no cambian.
- **Extraer el renderizado de contenido** a un partial compartido `_content-renderer.blade.php` para que los 7 tipos (`TEXT`, `IMAGE`, `VIDEO`, `EMBED`, `FILE_PREVIEW`, `AUDIO`, `HTML`) y las plantillas de `TEXT` (`prose/concept/list/quote/question/activity/mermaid`) vivan **una sola vez** y se reutilicen en scroll y libro.
- **Cerrar el hueco de UX**: completar la lección y el indicador de página deben existir en modo libro.
- **Accesibilidad**: teclado, `prefers-reduced-motion` y orientación/portada en móvil.

### No-objetivos (fuera de alcance)
- Persistencia del modo elegido (sin `localStorage`).
- Guardar la posición de lectura en `LmsActivityProgress`.
- Mermaid interactivo dentro de las páginas del libro (placeholder enlazado, ver §6).
- Modo libro como vista por defecto.
- Comentar / ver comentarios dentro del libro.
- Cambiar `math-text.blade.php`, `loaders.js`, `vite.config.js` más allá de añadir `page-flip`.

---

## 3. Arquitectura

```
ActivityView (Livewire full-page)
├── mount() → $sections, $resources, $links, $htmlEmbeds, $comments, $flipEnabled
├── markComplete() → + dispatch('activity-completed')  [nuevo]
└── render() → activity-view.blade.php
    ├── [scroll] barra sticky: toggle (si $flipEnabled) + barra de progreso
    ├── [scroll] TOC + secciones → _content-renderer ($mode='scroll')
    ├── [libro]  contenedor x-show mode==='book' → wire:ignore → _flipbook-page ($mode='book')
    └── @once <script> alpine:init → readingNav + store lmsView + lessonBook
```

### Estado del modo → Alpine store `lmsView` (corrección al spec §6.1)

El spec original proponía `window.__lmsView` + `dispatchEvent('lms-view-changed')`. **Se descarta**: Alpine no re-evalúa un `x-show` atado a una propiedad de `window` ante un evento sintético, y nada en el spec escuchaba ese evento. En su lugar:

```js
Alpine.store('lmsView', {
    mode: 'scroll',                 // 'scroll' | 'book'
    set(v) { this.mode = v; },
});
```

- Es **reactivo**: los `x-show` se re-evalúan solos al mutar el store.
- **Sobrevive a re-renders de Livewire** (el store vive en `Alpine.store`, fuera del DOM que se diffea).
- Los controles solo hacen `Alpine.store('lmsView').set('book')` — sin globals, sin eventos sueltos.

### Correcciones staff al spec original

| # | Spec original | Corrección | Por qué |
|---|---------------|------------|---------|
| 1 | `wire:ignore.self` en el root del libro | **`wire:ignore` completo** | StPageFlip reordena/mueve los hijos del contenedor; `.self` sigue diffeando los hijos contra el snapshot de Livewire y corrompe el libro. |
| 2 | `lessonBook` registrado sin wrapper | `document.addEventListener('alpine:init', …)` + guard `Alpine._lessonBookRegistered` | Es el patrón real del proyecto (`readingNav`, `lms-student-preview.js`). |
| 3 | `window.__lmsView` global + evento | **Alpine store `lmsView`** (ver arriba) | Reactividad real, sin listener fantasma. |
| 4 | Sin ruta para completar en libro | Barra final fuera del `wire:ignore` con CTA "Marcar como completada" | Cierra el hueco de UX. |
| 5 | Duplicar ~14 plantillas de contenido | Extraer `_content-renderer.blade.php` | Evita drift entre scroll y libro. |

---

## 4. Componente Livewire (`ActivityView.php`)

### Nuevo: `$flipEnabled`

```php
/** ¿Se ofrece el toggle de modo libro? (≥2 secciones, publicada, no modo lectura). */
public bool $flipEnabled = false;

// en mount(), tras resolver $sections y $isPreview:
$this->flipEnabled = $this->sections->count() >= 2
    && ! $this->isPreview
    && ! $this->modoLectura;
```

- `$isPreview` ya está resuelto en `mount()` (línea 74); `$sections` queda recortado a la 1ª sección en preview (líneas 104-111), así que el gate se evalúa **después** del recorte. Con 1 sola sección visible, no hay flipbook.
- `$modoLectura` (franja 5–8) se mantiene en scroll: el modo libro no aplica a la franja de lectura asistida.

### `markComplete()` → dispatch (cierre del hueco UX)

`markComplete()` (línea 162) gana **una** línea al final, para que el estado de completado llegue al `lessonBook` que vive dentro de `wire:ignore` (zona que Livewire no re-renderiza):

```php
$this->dispatch('activity-completed');
```

`lessonBook()` (ver §7) mantiene un `completed` local, inicializado desde el atributo `data-completed` que renderiza el servidor y actualizado vía `Livewire.on('activity-completed', …)`. El `wire:click` **sí** sigue funcionando dentro de `wire:ignore` (solo el diffeo está desactivado), por lo que el CTA de la barra final dispara `markComplete()` normalmente.

---

## 5. Vista: estructura y toggle

### 5.1 Root

El root `<div x-data="readingNav()">` se mantiene (Livewire exige un solo root). Se añaden dos hermanos de `x-show`:

```html
<div x-show="Alpine.store('lmsView').mode === 'scroll'">
    {{-- contenido actual: TOC + secciones, sin cambios --}}
</div>

@if($flipEnabled)
<div x-show="Alpine.store('lmsView').mode === 'book'" x-cloak>
    <div x-data="lessonBook()" data-completed="{{ $completed ? '1' : '0' }}">
        <div wire:ignore>
            <div id="lms-flipbook-root">
                {{-- Páginas renderizadas en servidor, una por sección (ver §7.1) --}}
                @foreach($sections as $section)
                    @include('livewire.student.lms._flipbook-page', ['section' => $section])
                @endforeach
            </div>
        </div>
        {{-- Barra final: fuera del wire:ignore → Livewire la re-renderiza tras markComplete() --}}
        <div class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4">
            {{-- indicador "Página X / N" + CTA (ver §7.2) --}}
        </div>
    </div>
</div>
@endif
```

> **Correcciones de implementación (Task 5)** — el bloque aplicado en producción difiere de la primera versión de este §5.1:
> 1. **Gate `@if($flipEnabled)`** (corrección del NEEDS_CONTEXT de Task 5): sin el gate, el footer "Marcar como completada" se filtraba al HTML de modo lectura infantil (`assertStringNotContainsString` en `StudentAccessTest.php:375`). Ahora `$flipEnabled` es una única fuente de verdad: sin toggle (§5.2) → tampoco hay DOM de libro.
> 2. **`x-data="lessonBook()"` fuera del `wire:ignore`** (C3): envuelve a la vez el árbol protegido Y la barra final. La barra usa `pageIndex`/`total`/`completed` de `lessonBook` → debe vivir dentro del `x-data`.
> 3. **Sin `x-init="init()"`** (C2): Alpine 3.15.12 auto-llama a `init()`. Con el atributo se duplicaría el listener `Livewire.on`.

Nota: el `wire:ignore` envuelve **solo** el árbol que StPageFlip muta (`#lms-flipbook-root` y sus hojas). La **barra final** y su botón `wire:click="markComplete"` viven fuera de la zona protegida — Livewire puede re-renderizarlos y el botón sigue disparando `markComplete()` normalmente.

### 5.2 Toggle en la barra sticky

Segmented control, solo cuando `$flipEnabled`. Se coloca junto a la barra de progreso (que **permanece exclusiva del modo scroll**):

```html
@if($flipEnabled)
<div class="flex items-center gap-1 rounded-full border border-gray-200 bg-white p-1"
     role="group" aria-label="Modo de lectura">
    <button type="button"
            :class="Alpine.store('lmsView').mode === 'scroll' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors"
            :aria-pressed="Alpine.store('lmsView').mode === 'scroll'"
            @click="Alpine.store('lmsView').set('scroll')">
        Deslizar
    </button>
    <button type="button"
            :class="Alpine.store('lmsView').mode === 'book' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-full px-3 py-1 text-xs font-semibold transition-colors"
            :aria-pressed="Alpine.store('lmsView').mode === 'book'"
            @click="Alpine.store('lmsView').set('book')">
        Libro
    </button>
</div>
@endif
```

### 5.3 `x-cloak`

El contenedor del libro lleva `x-cloak` y el CSS global de `x-cloak` (ya presente en el layout del estudiante) lo oculta hasta que Alpine arranca — evita el flash de contenido sin tamaño.

---

## 6. Renderizado de contenido: `_content-renderer.blade.php`

### Extracción (corrección staff: evita duplicar ~14 plantillas)

Se extrae el bloque `@foreach($section->visibleContents …) @switch($content->type)` de `activity-view.blade.php` (líneas 341-626) a un partial:

**`resources/views/livewire/student/lms/_content-renderer.blade.php`**
```php
@props([
    'content' => null,    // LmsActivityContent
    'mode' => 'scroll',   // 'scroll' | 'book'
    'stepNum' => 1,
    'isLast' => false,
    'sectionId' => null,  // id de la sección contenedora (placeholder enlazado, modo libro)
])
```

- El `@switch($content->type)` completo (7 casos) y las plantillas de `TEXT` viven **una sola vez** aquí.
- `$mode` cambia **solo el wrapper**:
  - `'scroll'` → tarjeta actual (`bg-white rounded-xl border border-gray-200`, paso numerado, `wire:key="content-{{ $content->id }}"`).
  - `'book'` → bloque de página sin tarjeta propia (el recuadro lo da la hoja del flipbook), paso numerado más compacto, **sin** `wire:key` (no se diffea; ver §7).
- El bucle de secciones en `activity-view.blade.php` queda:
  ```blade
  @foreach($section->visibleContents as $idx => $content)
      @include('livewire.student.lms._content-renderer', [
          'content' => $content,
          'mode' => 'scroll',
          'stepNum' => $idx + 1,
          'isLast' => $loop->last,
          'sectionId' => $section->id,
      ])
  @endforeach
  ```

  `_flipbook-page.blade.php` incluye el mismo partial en `mode='book'` dentro de un `@foreach($section->visibleContents …)`, pasando también `sectionId` — el placeholder enlazado necesita el id de la sección contenedora.
- **Equivalencia verificada por la suite existente**: los tests de `ActivityView`/`StudentAccessTest` (que ya cubren el scroll) deben pasar sin cambios tras la extracción.

### 6.1 Placeholder para Mermaid (decisión confirmada: placeholder enlazado)

Dentro de `_content-renderer`, la detección de Mermaid (`$isMermaid` por clase `mermaid` o sintaxis `flowchart/graph/…`) se calcula una vez y se comporta según `$mode`:

- `mode === 'scroll'` → contenedor `mermaidEmbed()` actual (sin cambios).
- `mode === 'book'` → **placeholder**:
  ```html
  <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-center">
      <p class="text-sm text-amber-800">📊 Este diagrama se ve mejor en modo deslizar.</p>
      <button type="button" @click="openSection({{ $sectionId }})"
              class="mt-2 text-sm font-semibold text-amber-700 underline hover:text-amber-900">
          Ir a la sección
      </button>
    </div>
  ```
  - `openSection(id)` (definido en `lessonBook`): `Alpine.store('lmsView').set('scroll')` + `document.getElementById('seccion-' + id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })`. El DOM del scroll **sí está presente** en la página (oculto por `x-show`, no eliminado), así que `scrollIntoView` funciona tras el cambio de modo.
  - Solo `TEXT` y `HTML` pasan por la detección Mermaid; ambos usan el mismo placeholder.

### 6.2 Qué se mantiene tal cual en el libro

- **Matemática KaTeX** (`x-lms.math-text`): se mantiene dentro de las páginas del libro. El rendering es **estático** (post-DOMPurify + strip CVE-2025-1390 + `Alpine.initTree`); mover el nodo no lo rompe porque no depende de un MutationObserver vivo sobre ese subárbol. La invariante de `math-text` **no se toca**.
- **IMAGE** (incluido el fallback SVG), **VIDEO** (local + YouTube), **AUDIO**, **EMBED**, **FILE_PREVIEW**: se mantienen en el libro tal cual. `EMBED` y el fallback SVG corresponden a contenido autorado por el equipo (tipos ya controlados, ver §9 del spec `activity-view.md`).

### 6.3 Sección: encabezado + badge + paso en el libro

La hoja de página del libro (`_flipbook-page.blade.php`) reutiliza las variables de acento del header de sección ya existentes (`$accentColor`/`$accentDot`/`$accentRing`/`$badgeLabel`/`$badgeClass`, INICIO/DESARROLLO/CIERRE) para mantener la coherencia visual. **Decisión:** el pequeño bloque `@php` (4 variables, regex sobre `$section->title`) se **replica** en `_flipbook-page` — es auto-contenido por página y evita acoplar `_content-renderer` a `_flipbook-page`. Si más adelante apareciera un tercer consumidor, se extrae a un partial compartido; hoy son dos consumidores y la duplicación es de ~4 líneas.

---

## 7. `lessonBook` (componente Alpine)

Registro **inline** en el `@once <script>` de `activity-view.blade.php`, junto a `readingNav`, dentro de `document.addEventListener('alpine:init', …)` con guard:

```js
if (Alpine._lessonBookRegistered) return;
Alpine._lessonBookRegistered = true;

Alpine.data('lessonBook', () => ({
    pageFlip: null,     // instancia StPageFlip (null hasta la 1ª entrada al libro)
    pageIndex: 0,       // 0-based, para "Página X / N"
    total: 0,           // nº de hojas = nº de secciones visibles (children del root)
    completed: false,
    init() {
        this.completed = this.$root.dataset.completed === '1';
        Livewire.on('activity-completed', () => { this.completed = true; });
    },
    // lazy: el libro está display:none al cargar → dimensiones 0.
    // Se construye en la PRIMERA entrada a modo libro, no en el primer paint.
    ensureFlipbook() { … },   // ver §7.1
    openSection(id) { … },    // §6.1
    setPage(index) { … },     // actualiza pageIndex para el indicador
}));

// Las páginas NO las construye JS: ya vienen renderizadas por Blade en
// #lms-flipbook-root (una .stf__item por sección, ver §5.1). StPageFlip
// opera sobre esos children existentes. El array `pages` del spec original
// se elimina: la fuente de verdad es el DOM servido.
```
```

### 7.1 Inicialización diferida (corrección staff: DOM oculto)

StPageFlip mide el contenedor en `getBoundingClientRect`; con el libro `display:none` al cargar, devuelve 0 y el libro no se construye o se degrada. Por eso:

- `ensureFlipbook()` se llama **la primera vez** que el usuario entra a modo libro (desde el `@click` del toggle o desde `set('book')` vía watcher), tras `$nextTick` para que el `x-show` ya haya mostrado el contenedor.
- Las páginas ya están en el DOM (renderizadas por Blade en §5.1). `ensureFlipbook()` resuelve `loadPageFlip()` (ver §8), mide `#lms-flipbook-root` (children `.stf__item` = hojas), `this.total = children.length`, y crea `new StPageFlip(elm, opts)` sobre el root.
- Al salir a scroll, la instancia se conserva (no se destruye) para que volver al libro sea instantáneo; `resize` → `update()`.
- Guard de idempotencia: `if (this.pageFlip) return;` — el `x-init="init()"` y la entrada repetida no duplican páginas.

### 7.2 Barra final e indicador

Fuera del `wire:ignore`, dentro del `x-data="lessonBook()"`:

```html
<div class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4"
     x-show="Alpine.store('lmsView').mode === 'book'">
    <p class="text-sm text-gray-600">
        Página <span x-text="pageIndex + 1"></span> de <span x-text="total"></span>
    </p>
    <button type="button"
            x-show="!completed"
            wire:click="markComplete"
            class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
        Marcar como completada
    </button>
    <span x-show="completed" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600">
        ✓ Completada
    </span>
</div>
```

- `x-show="!completed"` oculta el CTA tras el dispatch; `markComplete()` en el servidor también re-renderiza (el botón está fuera de `wire:ignore`), pero el estado local da respuesta inmediata sin esperar el round-trip.
- Tras completar, `Esc` (ver §7.3) lleva al scroll donde el footer "Volver / Marcar" ya refleja el estado completado y quedan los comentarios.

### 7.3 Accesibilidad (spec §11, concretado)

- **Flechas** ← / → → `pageFlip.flipPrev()` / `flipNext()` (mover página).
- **Home / End** → primera / última página.
- **Esc** → `Alpine.store('lmsView').set('scroll')` (vuelve al modo con TOC, comentarios y footer).
- Listener de teclado solo activo cuando `mode === 'book'` (se registra en `set()` / se comprueba en el handler).
- **`prefers-reduced-motion: reduce`** → `flippingTime: 0` (turnos instantáneos) y sin sombra/animación de página. Se detecta en `ensureFlipbook()` con `matchMedia('(prefers-reduced-motion: reduce)')`. Alineado con el bloque CSS existente que ya oculta la confeti bajo esa media query.
- **`usePortrait` en móvil**: una sola hoja en < `md`, spread de dos en ≥. El tamaño de página se calcula del viewport (`min(ancho, alto × ratio)`).
- **Rol `region` + `aria-label="Modo libro"`** en el contenedor; el toggle usa `aria-pressed` (ver §5.2).

### 7.4 Error handling

- `loadPageFlip()` falla (red / CDN / build) → `catch` → se muestra un mensaje en la barra final: "El modo libro no pudo cargarse. Usá el modo deslizar." y el toggle queda deshabilitado (`:disabled`), manteniendo el scroll 100% funcional. El modo libro **nunca** rompe el contenido: el fallo solo degrada el flipbook.
- `ensureFlipbook()` con dimensiones 0 (contenedor oculto por el `x-show` aún no aplicado) → reintento en `$nextTick`; si persiste 0, se muestra el fallback de §7.4.
- `openSection` con `id` no encontrado → no-op silencioso (el `?.` ya lo cubre).

---

## 8. Carga del paquete `page-flip`

### 8.1 `resources/js/loaders.js` — nuevo loader cacheado

Patrón idéntico a los loaders existentes (promesa cacheada en `window`):

```js
export function loadPageFlip() {
    if (!window._pageFlipPromise) {
        window._pageFlipPromise = import('page-flip')
            .catch((err) => {
                window._pageFlipPromise = undefined; // permite reintento
                throw err;
            });
    }
    return window._pageFlipPromise;
}
```

### 8.2 `vite.config.js` — `manualChunks`

Se añade `page-flip` al chunk propio (como `cytoscape`, `katex`, `d3-`), para que no inflen el bundle principal y se cargue solo cuando se entra al modo libro:

```js
if (id.includes('node_modules/page-flip') || id.includes('node_modules/st-page-flip')) {
    return 'page-flip';
}
```

### 8.3 `package.json`

`npm install page-flip` (StPageFlip). El import se hace dinámico desde `loaders.js`, así que no toca el bundle de arranque.

---

## 9. Seguridad (invariantes — se preservan)

1. **`math-text.blade.php` NO se toca.** El libro reutiliza `x-lms.math-text` para texto y matemática; no se introduce ningún `{!! $content->body !!}` nuevo.
2. **Sin bypass de sanitización.** Los únicos `{!! … !!}` siguen siendo los tipos ya controlados y autorados (`IMAGE` fallback SVG, `EMBED`, `HTML`), que pasan al partial tal cual estaban — sin cambios de ruta.
3. **Placeholder enlazado** significa que Mermaid **no** se renderiza dentro del libro: el contenido del diagrama no entra al DOM del flipbook, eliminando cualquier riesgo de MutationObserver/`Alpine.initTree` re-ejecutado sobre nodos movidos.
4. **`wire:ignore`** limita el diffeo de Livewire sobre el árbol que StPageFlip muta, evitando que el snapshot corrompa el DOM.

---

## 10. Pruebas

### Nuevo: `tests/Feature/Lms/ActivityViewBookModeTest.php` (PHP 8.2: `php8.2 artisan test`)

| Caso | Assert |
|------|--------|
| Gate: <2 secciones | `assertDontSee('Modo de lectura')` (toggle ausente) |
| Gate: preview (`now() < publish_at`) | toggle ausente |
| Gate: `modo_lectura` | toggle ausente |
| Gate: ≥2 secciones publicadas | toggle presente |
| Libro: nº páginas | `assertSee('Página 1 de ' . $n)` y `Página ' . $n . ' de ' . $n` → contenedor con `n` hojas |
| Libro: títulos de sección | cada título de sección aparece (dentro del bloque libro) |
| Libro: `wire:ignore` | root del flipbook bajo `wire:ignore` (comprobación del HTML) |
| Placeholder Mermaid | en modo libro, `assertSee('se ve mejor en modo deslizar')` + `assertDontSee` del script del diagrama dentro del bloque libro |
| Última página CTA | botón "Marcar como completada" presente en la barra final |
| `markComplete` → dispatch | `assertDispatched('activity-completed')` |

### Regresión (equivalencia de la extracción)

`php8.2 artisan test --filter="StudentHomeTest|StudentAccessTest"` debe seguir en verde: el modo scroll renderiza lo mismo que antes del refactor a `_content-renderer`.

### No tocar

- `tests/Feature/Planning/FlowDiagramTest.php` (fallos preexistentes no relacionados).
- `php artisan view:cache` (falla a nivel repo por `heroicon-m::x-mark` preexistente) — la validación de blades es vía tests.

---

## 11. Archivos afectados

| Archivo | Cambio |
|---------|--------|
| `package.json` | + `page-flip` |
| `vite.config.js` | manualChunks `page-flip` |
| `resources/js/loaders.js` | + `loadPageFlip()` |
| `app/Livewire/Student/Lms/ActivityView.php` | + `$flipEnabled`; `markComplete()` → `dispatch('activity-completed')` |
| `resources/views/livewire/student/lms/activity-view.blade.php` | Toggle sticky; 2 contenedores `x-show`; `wire:ignore`; barra final; `@once <script>` con store `lmsView` + `lessonBook`; bucle de secciones → partial |
| `resources/views/livewire/student/lms/_content-renderer.blade.php` | **Nuevo** — partial compartido (scroll + libro) |
| `resources/views/livewire/student/lms/_flipbook-page.blade.php` | **Nuevo** — hoja de página (encabezado + badge + `overflow-y:auto` + footer "Página X/N" sticky) |
| `tests/Feature/Lms/ActivityViewBookModeTest.php` | **Nuevo** |

---

## 12. Fuera de alcance (recordatorio de los no-objetivos)

Persistencia del modo, guardar posición, mermaid interactivo en el libro, libro por defecto, comentar en el libro, modo libro en la franja 5–8 (`modo_lectura`), 3D/depth effect extra de StPageFlip, mascota en modo libro.

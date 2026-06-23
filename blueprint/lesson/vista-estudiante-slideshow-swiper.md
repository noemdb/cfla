# Vista de Estudiante — Presentación por diapositivas con Swiper.js

## Contexto

El modal "Vista del Estudiante" en el wizard de lecciones (`LessonWizard`) renderizaba la lección completa en un layout vertical scrollable (`overflow-y-auto`). El profesor debía scrollear manualmente para ver cada sección, recursos, enlaces y embeds. Esto era poco práctico para lecciones con muchas secciones, obligando al docente a navegar linealmente sin contexto de progreso ni controles de presentación.

## Decisión técnica

Se migró a un formato de **presentación por diapositivas (slideshow)** donde cada bloque de contenido es un slide navegable.

**Stack:**
- **Swiper.js v12** — ya instalado como dependencia npm, importado en `resources/js/app.js` con módulos Navigation, Pagination, Autoplay, EffectFade expuestos en `window.Swiper*`
- **Alpine.js** — `Alpine.data()` registrado dentro de `@script` de Livewire 3, patrón existente para `mermaidEmbed`
- **Fullscreen API** — nativa del navegador, sin dependencias
- **Navegación personalizada** — botones prev/next con Alpine llamando a métodos del componente

## Arquitectura

### Slides

Cada bloque de contenido es un `.swiper-slide` dentro del modal:

| Slide | Contenido | ¿Condicional? |
|-------|-----------|---------------|
| 1 | **Header** — asignatura, título, descripción, fechas | Siempre |
| 2..N+1 | **Secciones** — una por sección, título + contenidos + embeds asociados | Siempre (al menos 1 sección o empty state) |
| N+2 | **Recursos descargables** | Solo si existen |
| N+3 | **Enlaces** | Solo si existen |
| N+4 | **HTML Embeds huérfanos** | Solo si existen |
| N+5 | **Resumen** — stats con contadores visuales (secciones, bloques, recursos, embeds, enlaces) | Siempre |

### Layout del modal

```
┌──────────────────────────────────────────────────────┐
│ Header: "Vista del Estudiante"           [×] Cerrar   │ ← shrink-0
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌──────────────────────────────────────────────┐   │
│  │  Swiper container (flex-1 min-h-0 w-full)    │   │ ← overflow-hidden
│  │  ┌────────────────────────────────────────┐  │   │
│  │  │  swiper-wrapper                        │  │   │
│  │  │  ┌────────────────────────────────┐    │  │   │
│  │  │  │  .swiper-slide w-full h-full   │    │  │   │
│  │  │  │  overflow-y-auto p-8           │    │  │   │
│  │  │  └────────────────────────────────┘    │  │   │
│  │  │  ┌────────────────────────────────┐    │  │   │
│  │  │  │  .swiper-slide w-full h-full   │    │  │   │
│  │  │  │  ...                            │    │  │   │
│  │  │  └────────────────────────────────┘    │  │   │
│  │  └────────────────────────────────────────┘  │   │
│  └──────────────────────────────────────────────┘   │
│                                                      │
├──────────────────────────────────────────────────────┤
│  [◀] [▶]    3 / 12    [⛶ Pantalla completa] [Cerrar]│ ← shrink-0
└──────────────────────────────────────────────────────┘
```

El card del modal usa `flex flex-col max-h-[90vh]`. El Swiper ocupa `flex-1 min-h-0 w-full` para estirarse entre header y footer fijos.

## Archivos modificados

| Archivo | Cambio |
|---------|--------|
| `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` | Restructuración completa del body del modal a Swiper + Alpine `lessonPreviewSwiper` + navegación personalizada |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | `$this->showPublishConfirm = false;` en `saveAndPublish()` antes de mostrar preview |

## Implementación detallada

### 1. Contenedor del modal

El card usa `flex flex-col max-h-[90vh]`. `x-data="lessonPreviewSwiper"` va en el card para que tanto el body Swiper como el footer compartan el mismo scope Alpine:

```blade
<div class="relative w-full max-w-7xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
     x-data="lessonPreviewSwiper"
     wire:key="swiper-{{ $listPreviewData['activity_id'] }}">
```

- `wire:key` asegura que Livewire re-renderice correctamente el Swiper cuando cambia la data
- `x-data` en el card contenedor permite que el footer (con prev/next y fracción) acceda al mismo componente Alpine

### 2. Body: contenedor Swiper

```blade
<div class="flex-1 min-h-0 w-full bg-slate-50 swiper overflow-hidden"
     x-ref="swiperContainer">
    <div class="swiper-wrapper">
        {{-- Slides --}}
    </div>
</div>
```

**Importante:** `w-full` es necesario para que Swiper mida correctamente el ancho de los slides. Sin `w-full`, los slides pueden tener ancho 0 y el contenido no se renderiza visualmente, aunque el estado interno de Swiper (activeIndex, slideCount) funcione correctamente.

### 3. Slides

Cada slide usa `swiper-slide overflow-y-auto w-full h-full p-8`:

- `w-full h-full` — necesario para que Swiper asigne dimensiones correctas a cada slide
- `overflow-y-auto` — scroll interno si el contenido excede la altura disponible
- `p-8` — padding interno del contenido

**Slide Header:**
```blade
<div class="swiper-slide overflow-y-auto w-full h-full p-8">
    <div class="pb-5">
        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">
            {{ $listPreviewData['subject'] }}
        </p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $listPreviewData['title'] }}</h1>
        @if($listPreviewData['description'])
            <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $listPreviewData['description'] }}</p>
        @endif
        {{-- Fechas --}}
    </div>
</div>
```

**Slide Resumen (último):**
```blade
<div class="swiper-slide overflow-y-auto w-full h-full p-8">
    <div class="flex items-center justify-center h-full">
        <div class="text-center max-w-md">
            {{-- Icono check --}}
            <h3 class="text-lg font-bold text-slate-800 mb-4">Resumen de la lección</h3>
            <div class="grid grid-cols-2 gap-3">
                {{-- Tarjetas con contadores --}}
            </div>
        </div>
    </div>
</div>
```

### 4. Footer: navegación personalizada

Los botones usan `x-on:click` llamando a métodos del componente Alpine. La fracción usa `x-text` con propiedades reactivas:

```blade
<div class="px-8 py-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between shrink-0">
    {{-- Prev/Next --}}
    <div class="flex items-center gap-2">
        <button x-on:click="prev()"
                class="w-9 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 ...">
            <svg>← flecha</svg>
        </button>
        <button x-on:click="next()"
                class="w-9 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 ...">
            <svg>→ flecha</svg>
        </button>
    </div>

    {{-- Fracción reactiva --}}
    <div class="swiper-pagination-fraction text-sm font-medium text-slate-500"
         x-text="currentSlide + ' / ' + totalSlides"></div>

    {{-- Fullscreen + Cerrar --}}
    <div class="flex items-center gap-2">
        <button x-on:click="toggleFullscreen" class="...">⛶ Pantalla completa</button>
        <button wire:click="closeListStudentPreview" class="...">Cerrar</button>
    </div>
</div>
```

**Alternativa descartada (navegación nativa Swiper):** Se intentó usar `.swiper-button-prev`/`.swiper-button-next` con `navigation.nextEl`/`navigation.prevEl`, pero Swiper v12 no encontraba los botones fuera del contenedor Swiper incluso con `uniqueNavElements: false`. Se optó por métodos Alpine que llaman `slidePrev()`/`slideNext()` a través del helper `_getSwiper()`.

## Alpine component `lessonPreviewSwiper`

Registrado dentro del bloque `@script` existente (junto a `mermaidEmbed`):

```js
Alpine.data('lessonPreviewSwiper', () => ({
    currentSlide: 1,
    totalSlides: 1,
    _wait: null,

    init() {
        // Poll hasta que el DOM del modal esté listo (slides renderizadas)
        this._wait = setInterval(() => {
            const el = this.$el.querySelector('.swiper');
            if (el && el.querySelectorAll('.swiper-slide').length > 0) {
                clearInterval(this._wait);
                this._wait = null;
                this.totalSlides = el.querySelectorAll('.swiper-slide').length;
                // Esperar al próximo frame para que el navegador haya completado el layout
                requestAnimationFrame(() => this.initSwiper(el));
            }
        }, 50);
    },

    initSwiper(el) {
        if (!el || el.swiper) return;

        try {
            const self = this;
            // Swiper v12 requiere modules explícitos
            new window.Swiper(el, {
                modules: [window.SwiperNavigation, window.SwiperPagination],
                slidesPerView: 1,
                spaceBetween: 0,
                direction: 'horizontal',
                speed: 350,
                keyboard: { enabled: true },
                mousewheel: { sensitivity: 1 },
                grabCursor: true,
                on: {
                    slideChange() {
                        self.currentSlide = this.activeIndex + 1;
                        self.$nextTick(() => self.triggerMermaid());
                    },
                    init() {
                        self.currentSlide = 1;
                        self.$nextTick(() => self.triggerMermaid());
                    }
                }
            });
        } catch (e) {
            console.warn('[LESSON_PREVIEW] Swiper init error:', e);
        }
    },

    /** Lee la instancia Swiper desde el DOM element donde Swiper se adjunta al inicializar */
    _getSwiper() {
        return this.$refs.swiperContainer?.swiper || null;
    },

    prev() { const s = this._getSwiper(); if (s) s.slidePrev(); },
    next() { const s = this._getSwiper(); if (s) s.slideNext(); },

    triggerMermaid() {
        const s = this._getSwiper();
        if (!s) return;
        const active = s.slides[s.activeIndex];
        if (active) {
            active.querySelectorAll('[data-mermaid-delay]').forEach(el => {
                el.dispatchEvent(new CustomEvent('slide-active'));
            });
        }
    },

    toggleFullscreen() {
        const el = this.$el.closest('.max-w-7xl');
        if (!document.fullscreenElement) {
            el?.requestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    },

    destroy() {
        if (this._wait) clearInterval(this._wait);
        const s = this._getSwiper();
        s?.destroy?.();
    }
}));
```

### Patrón clave: `_getSwiper()`

En lugar de almacenar la instancia de Swiper como `this.swiper = new Swiper(...)`, se lee directamente del DOM element:

```js
_getSwiper() {
    return this.$refs.swiperContainer?.swiper || null;
}
```

Swiper v12 se adjunta automáticamente como `.swiper` en el elemento contenedor al inicializar. Leerlo desde `$refs` evita problemas de:
- Reactividad de Alpine (Swiper es un objeto externo, no datos reactivos)
- `this` binding inconsistente entre `init()` y métodos de evento
- Silenciamiento de errores por `try/catch` (si el constructor falla, `el.swiper` simplemente no existe)

### Propiedades reactivas

| Propiedad | Tipo | Inicial | Descripción |
|-----------|------|---------|-------------|
| `currentSlide` | Number | 1 | Slide actual (1-indexed) |
| `totalSlides` | Number | 1 | Total de slides (se calcula en polling) |

### Eventos Swiper

- **`init`**: Establece `currentSlide = 1`, dispara render de Mermaid en slide inicial
- **`slideChange`**: Actualiza `currentSlide = activeIndex + 1`, dispara render de Mermaid en nuevo slide activo

### Timing de inicialización

El componente usa `setInterval` a 50ms para **polling del DOM** hasta que los slides existen en el DOM. Luego usa `requestAnimationFrame` para asegurar que el navegador ha completado el layout antes de inicializar Swiper:

```
init() → setInterval(50ms)
  → ¿`.swiper` existe en DOM?
    → NO → esperar 50ms más
    → SÍ → ¿`.swiper-slide` count > 0?
      → NO → esperar 50ms más
      → SÍ → clearInterval, requestAnimationFrame(initSwiper)
        → browser layout completado → new Swiper(el, {...})
```

Este patrón es necesario porque el modal se renderiza condicionalmente (`@if($showListStudentPreview)`) y Alpine puede inicializar el componente antes de que Livewire/morphdom complete la inserción del HTML.

## Lecciones aprendidas durante la implementación

### 1. `x-data` debe envolver tanto Swiper como footer

Inicialmente `x-data="lessonPreviewSwiper"` se puso solo en el contenedor Swiper. El footer con `x-text="currentSlide + ' / ' + totalSlides"` quedaba fuera del scope Alpine, causando `Alpine Expression Error: currentSlide is not defined`.

**Solución:** Mover `x-data` al card contenedor que envuelve tanto el Swiper body como el footer de navegación.

### 2. Swiper v12 requiere `modules` explícitos

Sin `modules: [window.SwiperNavigation, window.SwiperPagination]`, algunas funcionalidades fallan silenciosamente. El hero slider de la página principal ya seguía este patrón.

### 3. Slides sin `w-full h-full` no se renderizan visualmente

Si los slides no tienen dimensiones explícitas (`w-full h-full`), Swiper mide ancho/alto 0 en la inicialización. El estado interno (`activeIndex`, `slides.length`) funciona correctamente y los callbacks se disparan, pero `translate3d()` mueve 0px porque el ancho del slide es 0.

**Síntoma:** La fracción cambia al hacer clic en prev/next, pero el contenido visual no cambia.

### 4. Usar `$refs` en lugar de almacenar `this.swiper`

Almacenar la instancia como `this.swiper` falla si:
- El constructor lanza error capturado por `try/catch`
- Alpine no trackea correctamente el objeto externo

**Solución:** Leer `this.$refs.swiperContainer?.swiper` cada vez que se necesita.

### 5. Polling + `requestAnimationFrame` para modal condicional

`$nextTick` no es suficiente para garantizar que Livewire/morphdom haya insertado el HTML y el navegador haya calculado layout. `setInterval(50ms)` + `requestAnimationFrame` es más confiable.

### 6. `showPublishConfirm` debe cerrarse antes de mostrar preview

`saveAndPublish()` dejaba `showPublishConfirm = true`, lo que mantenía visible el modal de confirmación de publicación (z-[9999]) por encima del modal de preview.

## Render diferido de Mermaid

Los diagramas Mermaid dentro de slides no activos no se renderizan al cargar el modal. En su lugar, usan `data-mermaid-delay`:

```blade
<div wire:ignore x-data="mermaidEmbed()"
     data-mermaid-code="{{ $embed['html_content'] }}"
     data-mermaid-delay
     class="w-full">
    <div x-ref="target" class="w-full"></div>
</div>
```

En `mermaidEmbed.init()`:

```js
init() {
    const code = this.$el.getAttribute('data-mermaid-code') || '';
    // ...
    if (this.$el.hasAttribute('data-mermaid-delay')) {
        this.$el.addEventListener('slide-active', () => this.render(code), { once: true });
    } else {
        this.$nextTick(() => this.render(code));
    }
}
```

El evento `slide-active` es disparado por `lessonPreviewSwiper.triggerMermaid()` cada vez que cambia el slide activo (tanto en `init` como en `slideChange`).

**¿Por qué no renderizar todos al inicio?**
- Swiper calcula dimensiones de slides al inicializar. Si Mermaid inyecta SVGs de tamaño variable después, Swiper no los mide correctamente.
- Renderizar decenas de diagramas simultáneamente puede ser costoso en CPU.
- El slide activo es el único visible para el usuario; los demás se renderizan bajo demanda.

## Controles de navegación

| Control | Implementación | Descripción |
|---------|----------------|-------------|
| **Botón prev** | `x-on:click="prev()"` → `_getSwiper().slidePrev()` | Flecha izquierda |
| **Botón next** | `x-on:click="next()"` → `_getSwiper().slideNext()` | Flecha derecha |
| **Teclado ← →** | `keyboard: { enabled: true }` | Flechas del teclado |
| **Mouse wheel** | `mousewheel: { sensitivity: 1 }` | Scroll del mouse |
| **Touch/swipe** | Nativo de Swiper | Deslizar en pantalla táctil |
| **Fracción** | `x-text="currentSlide + ' / ' + totalSlides"` | Indicador "3 / 12" reactivo |
| **Fullscreen** | `x-on:click="toggleFullscreen"` → Fullscreen API | Alterna pantalla completa en `.max-w-7xl` |
| **Cerrar** | `wire:click="closeListStudentPreview"` | Livewire cierra el modal |

## Casos borde

| Caso | Comportamiento |
|------|----------------|
| **0 secciones** | Slide único con mensaje "No hay contenido disponible" |
| **1 slide total** | Fracción muestra "1 / 1", prev/next se deshabilitan (no hay más slides) |
| **Slide con mucho contenido** | `overflow-y-auto` interno en el slide permite scroll vertical |
| **Diagramas Mermaid** | Render diferido solo en slide activo |
| **Pantalla completa** | `requestFullscreen()` en `.max-w-7xl`, soporta `exitFullscreen()` |
| **Cerrar y reabrir modal** | `listPreviewData` cambia → Livewire re-renderiza → Swiper se reinicia |
| **Ventana redimensionada** | Swiper `update()` automático |
| **Embeds fuera del slideshow (export/import)** | Sin `data-mermaid-delay`, render inmediato como antes |
| **Recursos sin `allow_downloads`** | Slide recursos sin badge "Descargar" |
| **Múltiples slides condicionales ausentes** | Swiper maneja slides faltantes sin problema |
| **HTML embeds rotos** | Mismo manejo que antes (el docente es responsable del contenido) |
| **Modal de confirmación publicado abierto** | `showPublishConfirm = false` en `saveAndPublish()` antes de mostrar preview |

## Verificación

1. Abrir modal vista estudiante → slideshow se renderiza, slide 1 = header
2. Botones prev/next navegan entre slides con animación suave (350ms)
3. Flechas del teclado (←/→) avanzan y retroceden
4. Scroll con mousewheel cambia slides
5. Touch/swipe móvil cambia slides
6. Pantalla completa → toggle funciona, contenido se escala al viewport
7. Diagramas Mermaid se renderizan solo cuando su slide está activo
8. Slide con contenido extenso → scroll interno funciona
9. Cerrar modal → estado se limpia (`listPreviewData = null`)
10. Reabrir modal → Swiper se re-inicializa correctamente
11. Lección sin secciones → slide único con mensaje de empty state
12. Lección con recursos, enlaces y embeds → slides condicionales aparecen solo si hay datos
13. Slide resumen → contadores reflejan los valores reales
14. Fullscreen → botón alterna correctamente, sin errores en consola
15. **Publicar y luego ver preview** → modal de confirmación se cierra antes de mostrar preview

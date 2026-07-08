# ShowFullPreview Modal — Navegación + UX

**Fecha:** 2026-07-08
**Componente:** `App\Livewire\Profesor\Lms\LessonWizard`
**Vista:** `resources/views/livewire/profesor/lms/lesson-wizard.blade.php`
**Ruta:** `/app/profesors/lms/activity/lesson/new?activity_id={ID}`

## Resumen

Agregar navegación tipo "sidebar TOC" al modal de vista previa completa (`showFullPreview`) del asistente de lecciones LMS. La sidebar lista las secciones visibles de la lección con scroll suave, resaltado de sección activa vía IntersectionObserver, indicador de progreso, y colapso/expansión por sección. Adaptable a mobile.

## Cambios

Solo se modifica la **vista Blade** (`lesson-wizard.blade.php`). No hay cambios en el componente PHP ni en los modelos.

## Layout

El modal actual (contenedor `max-w-5xl`) se rediseña a un layout de 2 columnas:

```
┌──────────────────────────────────────────────────────┐
│  Header: "Vista Previa · 3/7 secciones"       [X]   │
├────────────┬─────────────────────────────────────────┤
│            │  Sección 1: Inicio                      │
│  📑 Índice │    ▼ colapsar/expandir                  │
│            │    [contenido renderizado...]            │
│  ● Inicio  │                                         │
│  ○ Desarr. │  ───────────────────────────             │
│  ○ Cont. 1 │  Sección 2: Desarrollo                  │
│  ○ Cierre  │    ▼                                   │
│            │    [contenido...]                       │
│            │                                         │
│            │  ───────────────────────────             │
│            │  Sección 3: Contenido 1                 │
│            │    ▼                                   │
│            │    [contenido...]                       │
│            │                                         │
│            │  ───────────────────────────             │
│            │  Sección N: Cierre                      │
│            │    ▼                                   │
│            │    [contenido + recursos...]            │
└────────────┴─────────────────────────────────────────┘
```

### Sidebar TOC (índice)

- **Ancho:** `w-56` fijo, `shrink-0`
- **Posición:** Columna izquierda del modal, sticky (`sticky top-0 self-start`)
- **Scroll:** `overflow-y-auto` independiente del contenido
- **Estilo:** Fondo `bg-slate-800/80`, borde derecho `border-r border-slate-700/50`, padding `p-3`
- **Encabezado:** "📑 Índice" con contador "N secciones"
- **Ítems:** Cada sección visible como un botón con:
  - Número de sección (01, 02...)
  - Título truncado a 1 línea
  - Indicador de contenido presente (punto verde si tiene contenido, gris si vacío)
  - Estado activo: círculo relleno ● vs círculo vacío ○
- **Altura:** `max-h-[calc(100vh-12rem)]` para no exceder el viewport

### Scroll activo (IntersectionObserver)

Cada sección tendrá un `x-ref` con su índice. Un Alpine controller `x-data="tocNavigation()"` en el contenedor del body usará `IntersectionObserver` para detectar qué sección está visible. La sección activa en la sidebar se resalta con fondo `bg-emerald-500/15`, texto `text-emerald-300` y círculo relleno.

**Implementación Alpine:**
```js
Alpine.data('tocNavigation', () => ({
    activeSection: 0,
    sectionElements: [],

    init() {
        this.sectionElements = this.$el.querySelectorAll('[data-section-index]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.activeSection = parseInt(entry.target.dataset.sectionIndex);
                }
            });
        }, { rootMargin: '-80px 0px -60% 0px' });

        this.sectionElements.forEach(el => observer.observe(el));
    },

    scrollTo(index) {
        const el = this.$el.querySelector(`[data-section-index="${index}"]`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}));
```

### Colapso/expansión por sección

Botón ▶/▼ al lado del título de cada sección (dentro del contenido, no en la sidebar). Controlado con Alpine:

```html
<button @click="expanded = !expanded"
        :class="expanded ? 'rotate-90' : ''"
        class="transition-transform">
    ▶
</button>
```

Por defecto todas las secciones **expandidas**. Al colapsar, se oculta todo el contenido de la sección (recursos, enlaces y embeds vinculados también).

### Indicador de progreso en header

En el header del modal, a la derecha del título "Vista Previa", un badge:
```
<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold
             bg-slate-700 text-slate-300 border border-slate-600">
    3/7 secciones
</span>
```

### Mobile (< 1024px)

- La sidebar TOC se **oculta**
- Aparece un botón flotante "📑" en la esquina inferior derecha (fijo, `fixed bottom-6 right-6`, `z-50`)
- Al hacer clic, se abre un **overlay TOC** modal simple con la misma lista de ítems
- El overlay se cierra al seleccionar una sección o tocar fuera
- Los ítems del TOC mobile tienen más tamaño táctil (`py-3`)

## Comportamiento detallado

### Apertura del modal
- Al abrirse (`$set('showFullPreview', true)`), el scroll empieza al inicio
- La primera sección se marca activa en el TOC
- El badge de progreso muestra el total de secciones visibles

### Scroll
- Mientras el usuario hace scroll por las secciones, el TOC resalta la sección actual
- El observer usa `rootMargin: '-80px 0px -60% 0px'` para activar la sección cuando está cerca del tope
- Hacer clic en un ítem del TOC hace scroll suave a esa sección

### Colapso
- Cada sección puede colapsarse/expandirse independientemente
- El estado de colapso es local (Alpine), no afecta al componente Livewire
- Al colapsar, el contenido se oculta con `x-show` y animación `x-transition`

### Cierre
- Al cerrar el modal, el estado del TOC y colapsos se reinicia (se pierde, es efímero)

## Sin cambios en el backend

- No se toca `LessonWizard.php`
- `previewSections` sigue siendo el computed property existente
- No hay nuevas rutas, eventos, ni queries

## Archivos modificados

Solo:
- `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` — región del modal `@if($showFullPreview)`

## Árbol de cambios (estimado)

```
lesson-wizard.blade.php
├── Header del modal
│   └── Badge "N/secciones"
├── Layout 2 columnas (flex)
│   ├── Sidebar TOC (w-56)
│   │   ├── Encabezado "📑 Índice"
│   │   └── Lista de ítems con scroll activo
│   └── Contenido (flex-1)
│       └── Por cada sección:
│           ├── data-section-index
│           ├── Botón colapsar ▶/▼ (Alpine)
│           ├── Título
│           ├── Contenido (x-show="expanded")
│           └── Recursos/Enlaces/Embeds (x-show="expanded")
├── Botón TOC flotante (mobile, < lg)
└── Scripts Alpine:
    ├── tocNavigation() — IntersectionObserver + scrollTo
    └── Manejo de colapso por sección
```

## Self-review

- ✅ Sin placeholders ni TODOs
- ✅ Consistente: solo toca la vista Blade, ningún cambio en PHP
- ✅ Scope enfocado: navegación + UX, no mezcla con otras mejoras
- ✅ Sin ambigüedad: cada comportamiento está especificado
- ✅ Mobile cubierto con TOC flotante overlay

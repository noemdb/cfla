# BtnInfoManualPanelDer — Botón + Panel Lateral Derecho (Slide-in)

## Propósito

Proveer al usuario acceso a documentación contextual, guía de estados, leyendas o manual de uso mediante un panel deslizable desde el borde derecho de la pantalla, sin perder el contexto de la vista principal.

A diferencia de BtnInfoManual (modal centrado), este patrón es ideal para contenido de referencia **consultivo** que el usuario necesita ojear mientras interactúa con la página subyacente — el panel no bloquea la vista completamente.

## Anatomía

```
┌──────────────────────────────────────────────────────┐
│  Vista principal                         ┌──────────┐│
│                                          │ PANEL    ││
│  ┌────────────────────────────────────┐  │ DERECHO  ││
│  │                                    │  │          ││
│  │   El contenido de la vista         │  │ Título   ││
│  │   permanece visible detrás         │  │ [X]      ││
│  │   (el panel se superpone)          │  ├──────────┤│
│  │                                    │  │ Sección  ││
│  │                                    │  │ 1        ││
│  │                                    │  │          ││
│  │                                    │  │ Sección  ││
│  │                                    │  │ 2        ││
│  │                                    │  │          ││
│  │                           ┌──────┐ │  │ Sección  ││
│  │                           │  ?   │ │  │ 3        ││
│  │                           │ FAB  │ │  │          ││
│  │                           └──────┘ │  ├──────────┤│
│  │                                    │  │ Footer   ││
│  └────────────────────────────────────┘  └──────────┘│
└──────────────────────────────────────────────────────┘
         ↓ click FAB
┌──────────────────────────────────────────────────────┐
│  Vista principal (oscurecida)           ┌──────────┐│
│                                         │ PANEL    ││
│  ┌── (overlay bg-black/50) ──────────┐ │ ABIERTO  ││
│  │                                    │ │          ││
│  │   La vista se oscurece con         │ │ ← slide  ││
│  │   backdrop translúcido             │ │ from     ││
│  │                                    │ │ right    ││
│  │   @click.away cierra el panel      │ │          ││
│  └────────────────────────────────────┘ └──────────┘│
└──────────────────────────────────────────────────────┘
```

## Variantes

| Variante | Ancho del panel | Cuándo usarla |
|----------|-----------------|---------------|
| **Angosto** | `w-80` / `max-w-sm` | Contenido ligero: leyenda de colores, estado breve, atajos |
| **Mediano** | `w-96` / `max-w-md` | Guía de estados con descripciones cortas, glosario |
| **Ancho** | `w-[480px]` / `max-w-lg` | Documentación detallada con secciones múltiples |
| **Adaptable** | `w-full sm:w-80 md:w-96` (responsive) | Mobile: panel completo; Desktop: ancho fijo |

## ADRs

| ADR | Regla |
|-----|-------|
| **PANEL-001** | El panel DEBE deslizarse desde la derecha con `translate-x-full → translate-x-0` |
| **PANEL-002** | El panel NUNCA debe cubrir más del 90% del ancho en mobile (dejar siempre un margen de la vista principal visible) |
| **PANEL-003** | DEBE incluir overlay con `@click.away` para cerrar + `@keydown.escape.window` |
| **PANEL-004** | NO usar Livewire — Alpine.js puro (`x-data`, `x-show`, `x-transition`) |
| **PANEL-005** | El botón disparador DEBE ocultarse cuando el panel está abierto (`x-show="!helpOpen"`) |
| **PANEL-006** | El contenido debe ser informativo, consultivo, de referencia rápida. Evitar texto extenso tipo tutorial |
| **PANEL-007** | El panel DEBE tener scroll interno (`overflow-y-auto`) con header y footer fijos |
| **PANEL-008** | En mobile, el panel DEBE ocupar `w-full max-w-sm` (no cortar contenido, pero dejar ~10% de la vista visible) |

## Patrón de implementación (Alpine.js)

### Estructura base

```blade
{{-- ═══════════════════════════════════════════════════════
     BOTÓN + PANEL LATERAL DERECHO
     ═══════════════════════════════════════════════════════ --}}
<div x-data="{ helpOpen: false }">
    {{-- Botón FAB — se oculta cuando el panel está abierto --}}
    <button @click="helpOpen = true"
        x-show="!helpOpen"
        x-cloak
        class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full
               bg-emerald-500/15 border border-emerald-500/30
               text-emerald-600 dark:text-emerald-400
               hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110
               flex items-center justify-center
               shadow-lg backdrop-blur-sm
               transition-all duration-300 group"
        title="{{ $tooltip }}"
        aria-label="{{ $tooltip }}">
        {!! $iconSvg !!}
    </button>

    {{-- ===== OVERLAY ===== --}}
    <div x-show="helpOpen"
         x-cloak
         x-transition.opacity.duration.200ms
         @click="helpOpen = false"
         class="fixed inset-0 z-30 bg-black/50 backdrop-blur-[2px]"
         aria-hidden="true">
    </div>

    {{-- ===== PANEL LATERAL ===== --}}
    <div x-show="helpOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         @keydown.escape.window="helpOpen = false"
         class="fixed top-0 right-0 z-40 h-full
                {{ $panelWidth }}
                bg-gray-900 dark:bg-gray-950
                border-l border-white/10
                shadow-2xl shadow-black/40
                flex flex-col"
         role="dialog"
         aria-modal="true"
         aria-label="{{ $ariaLabel }}">

        {{-- ===== HEADER ===== --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 shrink-0">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                {!! $headerIcon ?? '' !!}
                {{ $title }}
            </h3>
            <button @click="helpOpen = false"
                class="w-10 h-10 flex items-center justify-center rounded-lg
                       bg-white/5 hover:bg-white/10
                       text-gray-400 hover:text-white transition-all"
                aria-label="Cerrar panel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ===== BODY SCROLLABLE ===== --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-6">
            {{ $slot }}
        </div>

        {{-- ===== FOOTER (opcional) ===== --}}
        @if(isset($footerNote) || !empty($showFooter))
        <div class="px-5 py-3 border-t border-white/10 shrink-0">
            <button @click="helpOpen = false"
                class="w-full py-2.5 text-xs font-bold uppercase tracking-widest
                       bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white
                       rounded-lg border border-white/10 transition-all text-center">
                Cerrar
            </button>
            @if($footerNote ?? false)
                <p class="text-[10px] text-gray-600 mt-2 text-center">{{ $footerNote }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
```

### Bloque de contenido individual (sección)

```blade
<div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-2.5">
    {{-- Header con icono --}}
    <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 {{ $iconBg }} rounded-lg flex items-center justify-center {{ $iconColor }} shrink-0">
            {!! $iconSvg !!}
        </div>
        <h4 class="text-sm font-bold text-white">{{ $title }}</h4>
        @if($badge ?? false)
            <span class="ml-auto text-[10px] font-bold uppercase tracking-wider
                         {{ $badgeClass ?? 'text-gray-500 bg-white/5 px-2 py-0.5 rounded-full' }}">
                {{ $badge }}
            </span>
        @endif
    </div>

    {{-- Descripción --}}
    <p class="text-xs text-gray-400 leading-relaxed">
        {{ $description }}
    </p>

    {{-- Etiqueta / valor adicional (opcional) --}}
    @if($meta ?? false)
        <div class="flex items-center gap-2 text-[11px] text-gray-500">
            <span class="font-medium text-gray-400">{{ $metaLabel }}:</span>
            <span class="text-gray-300">{{ $metaValue }}</span>
        </div>
    @endif
</div>
```

### Ejemplo real: Guía de estados de lecciones (LMS Monitor)

Tomado de `resources/views/planning/lms/monitor.blade.php`:

```blade
{{-- Botón FAB con icono de ayuda (question mark circle) --}}
<button @click="helpOpen = true"
    class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full
           bg-emerald-500/15 border border-emerald-500/30
           text-emerald-600 dark:text-emerald-400
           hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110
           flex items-center justify-center shadow-lg backdrop-blur-sm
           transition-all duration-300 group"
    title="Guía de estados de lecciones"
    x-show="!helpOpen">
    <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none"
         stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025
                 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999
                 -1.45 1.827v.75M12 18h.01"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895
                 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0
                 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0
                 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0
                 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0
                 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0
                 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0
                 012.907-1.895A8.98 8.98 0 0112 3z"/>
    </svg>
</button>

{{-- Overlay + Panel --}}
<div x-show="helpOpen" x-cloak
     x-transition.opacity.duration.200ms
     @click="helpOpen = false"
     class="fixed inset-0 z-30 bg-black/50 backdrop-blur-[2px]">
</div>

<div x-show="helpOpen" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     @keydown.escape.window="helpOpen = false"
     class="fixed top-0 right-0 z-40 h-full w-96 bg-gray-950
            border-l border-white/10 shadow-2xl shadow-black/40
            flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025
                         1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999
                         -1.45 1.827v.75M12 18h.01"/>
            </svg>
            Guía de Estados
        </h3>
        <button @click="helpOpen = false"
            class="w-10 h-10 flex items-center justify-center rounded-lg
                   bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all"
            aria-label="Cerrar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
        {{-- Cada estado es un bloque de contenido --}}
        <div class="border border-white/10 rounded-xl p-4 space-y-2">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <h4 class="text-sm font-bold text-white">Publicado</h4>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed">
                La lección está <strong class="text-gray-300">visible para los estudiantes</strong>
                y disponible en el calendario académico.
            </p>
        </div>
        {{-- ... más estados --}}
    </div>
</div>
```

## Reglas de animación

```css
/* Panel slide: translate-x-full → translate-x-0 */
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="translate-x-full"
x-transition:enter-end="translate-x-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="translate-x-0"
x-transition:leave-end="translate-x-full"

/* Overlay: fade in/out */
x-transition.opacity.duration.200ms
```

| Propiedad | Valor | Motivo |
|-----------|-------|--------|
| Enter duration | `300ms` | Suave pero rápida, no frustra al usuario |
| Enter easing | `ease-out` | Natural, desacelera al final |
| Leave duration | `200ms` | Más rápida al cerrar (el usuario ya leyó) |
| Leave easing | `ease-in` | Acelera al salir, sensación de cierre rápido |
| Slide start | `translate-x-full` | Fuera de la pantalla a la derecha |
| Slide end | `translate-x-0` | Posición natural fijada a la derecha |

> **Nota**: Alpine.js aplica automáticamente `prefers-reduced-motion`. No se requiere código adicional.

## Reglas de contenido

1. El panel es **consultivo** — el contenido debe ser escaneable rápidamente
2. Cada sección debe ser autónoma (no depende de leer la sección anterior)
3. Usar **badges** para clasificar información (ej: estado, categoría, prioridad)
4. Texto conciso: máximo 3-4 líneas por descripción individual
5. Código fuente: `<code class="text-emerald-400">` para nombres de campo o estado
6. Énfasis: `<strong class="text-gray-300">` (no bold directo)

## Accesibilidad

| Requisito | Implementación |
|-----------|----------------|
| **WCAG 2.5.8** (Target Minimum) | FAB mínimo `w-12 h-12` (48×48px) |
| **Cierre** | 3 vías: [X] header, Escape, click en overlay |
| **ARIA** | `role="dialog"`, `aria-modal="true"`, `aria-label` en panel y botón |
| **Foco** | `x-trap.noscroll` opcional si hay navegación por teclado compleja |
| **Reduced motion** | Alpine respeta automáticamente `prefers-reduced-motion` |
| **Overlay cierre** | `@click="helpOpen = false"` en el overlay (click.away no funciona en overlay separado) |

## Mobile (ADR-005 compliance)

| Elemento | Mobile (base) | Desktop (sm/md/lg) |
|----------|---------------|-------------------|
| Ancho panel | `w-full max-w-sm` | `w-96` o `w-[480px]` |
| FAB posición | `bottom-6 right-6` | igual |
| FAB tamaño | `w-12 h-12` | igual |
| Padding body | `px-4 py-4` | `px-5 py-5` |
| Grillas internas | 1 columna | 1 columna (panel angosto no admite multi-col) |
| Overlay | `bg-black/60 backdrop-blur-[2px]` | igual |

> En mobile el overlay se vuelve más opaco (`/60` en lugar de `/50`) para compensar que el panel deja ver menos contexto de la vista principal.

## Diferencia clave vs BtnInfoManual (modal centrado)

| Aspecto | Modal Centrado (btnInfoManual) | Panel Derecho (btnInfoManualPanelDer) |
|---------|-------------------------------|---------------------------------------|
| Posición | Centro de pantalla | Borde derecho |
| Animación | Fade + scale (opacity-0 → 1, scale-95 → 100) | Slide horizontal (translate-x-full → 0) |
| Relación con vista | Bloquea completamente | Superposición parcial |
| Contenido | Extenso, secciones detalladas | Consultivo, referencia rápida |
| Ancho típico | `max-w-5xl` (XXL) | `w-96` (angosto a mediano) |
| Scroll | `max-h-[90vh]` con header/footer fijos | `h-full` con header/footer fijos |
| Caso de uso | Guía completa de indicadores | Leyenda de estados, glosario, colores |
| Botón FAB visible | Siempre visible | Se oculta cuando panel abierto (`x-show="!helpOpen"`) |

## Checklist de implementación

- [ ] ¿Usa Alpine.js puro (no Livewire)?
- [ ] ¿Animación slide desde la derecha con `translate-x-full`?
- [ ] ¿Overlay con `bg-black/50 backdrop-blur-[2px]` y `@click` para cerrar?
- [ ] ¿Botón se oculta cuando panel abierto (`x-show="!helpOpen"`)?
- [ ] ¿`x-cloak` presente en panel, overlay y botón?
- [ ] ¿`@keydown.escape.window` en el panel?
- [ ] ¿Header con título + botón [X]?
- [ ] ¿Body con `overflow-y-auto` scrollable?
- [ ] ¿Footer con botón Cerrar (opcional)?
- [ ] ¿Ancho panel: `w-96` desktop, `w-full max-w-sm` mobile?
- [ ] ¿`role="dialog"` y `aria-modal="true"`?
- [ ] ¿Target mínimo 44×44 en botón (FAB 48×48)?
- [ ] ¿Contenido consultivo y escaneable (no textos extensos)?
- [ ] ¿Cada sección es autónoma?
- [ ] ¿Sin `@media` queries custom?
- [ ] ¿`prefers-reduced-motion` manejado por Alpine?

## Referencias

- Blueprint principal: `blueprint/rulesUI/btnInfoManual.md` (estructura de contenido similar, modal centrado)
- Vista ejemplo: `resources/views/planning/lms/monitor.blade.php` (panel de guía de estados de lecciones)
- ADR-005 (container padding): `blueprint/mobile-firts/promptStaff.md`

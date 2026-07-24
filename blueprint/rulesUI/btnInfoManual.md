# BtnInfoManual — Botón de Información / Guía / Manual

## Propósito

Proveer al usuario un punto de entrada consistente para acceder a documentación contextual, explicación de indicadores, leyendas, o guías de uso directamente desde la vista activa, sin abandonar la página.

## Anatomía

Un BtnInfoManual se compone de dos partes:

```
┌─────────────────────────────────────────────────┐
│  Botón disparador (FAB o inline)                │
│  ┌───────────┐                                  │
│  │  🔍 / ?   │  ← Icono representativo         │
│  └───────────┘                                  │
│     + tooltip / aria-label descriptivo          │
└─────────────────────────────────────────────────┘
                        ↓ onClick
┌─────────────────────────────────────────────────┐
│  Modal XXL (o grande según densidad de info)    │
│  ┌───────────────────────────────────────────┐  │
│  │  Header: Título + [X] close              │  │
│  ├───────────────────────────────────────────┤  │
│  │  Cuerpo scrollable con secciones:        │  │
│  │  ┌─ Sección 1 ─────────────────────────┐ │  │
│  │  │  Icono + Título                     │ │  │
│  │  │  Descripción                        │ │  │
│  │  │  Interpretación                     │ │  │
│  │  │  Recomendación (opcional)           │ │  │
│  │  └─────────────────────────────────────┘ │  │
│  │  ┌─ Sección 2 ─────────────────────────┐ │  │
│  │  │  ...                                │ │  │
│  │  └─────────────────────────────────────┘ │  │
│  ├───────────────────────────────────────────┤  │
│  │  Footer: [Cerrar] + nota contextual       │  │
│  └───────────────────────────────────────────┘  │
│     @click.away · @keydown.escape.window        │
└─────────────────────────────────────────────────┘
```

## Variantes

| Variante | Clases base | Cuándo usarla |
|----------|-------------|---------------|
| **FAB** (Floating Action Button) | `fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full shadow-lg` | Vista densa con muchos indicadores, dashboard, panel principal |
| **Inline** | Posición relativa al layout, `w-10 h-10 rounded-lg` | Junto a un título, al lado de "Mi Perfil", toolbar contextual |
| **Mini** | `w-8 h-8 rounded` | Espacios reducidos, tablas, tarjetas individuales |
| **Card-footer** | `w-full text-left px-4 py-3 rounded-lg border-t` | Al final de una tarjeta como enlace a documentación |

## ADRs

| ADR | Regla |
|-----|-------|
| **BTN-INFO-001** | El contenido del modal DEBE ser informativo y descriptivo, nunca promocional ni vacío |
| **BTN-INFO-002** | Cada sección del modal DEBE incluir: qué es (descripción), cómo interpretarlo, y recomendación |
| **BTN-INFO-003** | El modal DEBE cerrarse con: [X] header, click outside (solo FAB/inline), Escape, y botón footer |
| **BTN-INFO-004** | NO usar Livewire para el modal — usar Alpine.js puro (`x-data`, `x-show`, `@click.away`) |
| **BTN-INFO-005** | El botón DEBE tener `aria-label` o `title` descriptivo (ej: "Guía de indicadores", "Manual de uso") |
| **BTN-INFO-006** | El modal DEBE ocupar `max-w-5xl` (XXL) o `max-w-3xl` (Large) según volumen de contenido, nunca menos |
| **BTN-INFO-007** | El trigger NO DEBE moverse al hacer scroll (FAB: `fixed`; Inline: posición natural) |
| **BTN-INFO-008** | Prohibido usar `@media` queries para responsive — usar clases base + `sm:`/`md:`/`lg:` Tailwind |

## Patrón de implementación (Alpine.js)

### Estructura base del contenedor

```blade
{{-- ══════════════════════════════════════════════
     BOTÓN + MODAL INFORMATIVO
     ══════════════════════════════════════════════ --}}
<div x-data="{ showGuide: false }">
    {{-- Botón disparador --}}
    <button @click="showGuide = true"
        class="{{ $btnClasses }}"
        title="{{ $tooltip }}"
        aria-label="{{ $tooltip }}">
        {!! $iconSvg !!}
    </button>

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="showGuide"
             x-cloak
             @click.away="showGuide = false"
             @keydown.escape.window="showGuide = false"
             class="fixed inset-0 z-[100] flex items-start justify-center pt-10 sm:pt-16 pb-10 px-4"
             role="dialog"
             aria-modal="true"
             aria-label="{{ $ariaLabel }}">
            {{-- Backdrop --}}
            <div x-show="showGuide" x-transition.opacity.duration.200ms
                 class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div x-show="showGuide"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative w-full max-w-5xl max-h-[90vh] bg-gray-900 border border-white/10 rounded-2xl shadow-2xl shadow-black/50 flex flex-col z-10">

                {{-- ===== HEADER ===== --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10 shrink-0">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        {!! $headerIcon !!}
                        {{ $title }}
                    </h3>
                    <button @click="showGuide = false"
                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all"
                        aria-label="Cerrar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- ===== BODY SCROLLABLE ===== --}}
                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">
                    {{ $slot }}
                </div>

                {{-- ===== FOOTER ===== --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-white/10 shrink-0">
                    <p class="text-xs text-gray-500">{{ $footerNote ?? '' }}</p>
                    <button @click="showGuide = false"
                        class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white rounded-lg border border-white/10 transition-all">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
```

### Bloque de indicador individual (sección)

Cada indicador dentro del modal sigue esta estructura:

```blade
<div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
    {{-- Header con icono --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 {{ $iconBg }} rounded-lg flex items-center justify-center {{ $iconColor }} shrink-0">
            {!! $iconSvg !!}
        </div>
        <h4 class="text-sm font-bold text-white">{{ $title }}</h4>
    </div>

    {{-- Descripción --}}
    <p class="text-sm text-gray-400 leading-relaxed">
        {{ $description }}
    </p>

    {{-- Interpretación --}}
    <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
        <p class="text-xs text-gray-300">{{ $interpretation }}</p>
    </div>

    {{-- Recomendación (opcional) --}}
    @if($recommendation)
    <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-lg px-3.5 py-2.5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-0.5">Recomendación</p>
        <p class="text-xs text-emerald-400/80">{{ $recommendation }}</p>
    </div>
    @endif
</div>
```

## Reglas de contenido

1. **Toda sección** debe responder: ¿Qué es?, ¿Cómo se interpreta?, ¿Qué acción tomar?
2. **Lenguaje**: tono institucional pero claro. Evitar jerga técnica no explicada.
3. **Código fuente**: si se menciona un campo de BD o propiedad, usar `<code>`:
   ```blade
   <code class="text-emerald-400">status = true</code>
   ```
4. **Strong**: usar `<strong class="text-gray-300">` para énfasis semántico (no bold directo).
5. **Extensión**: el modal puede ser tan largo como sea necesario. El scroll está controlado por `max-h-[90vh] overflow-y-auto`.

## Accesibilidad

| Requisito | Implementación |
|-----------|----------------|
| **WCAG 2.5.8** (Target Minimum) | Botón mínimo `w-10 h-10` (40×40) o `min-w-[44px] min-h-[44px]` |
| **Focus trap** | El modal captura foco dentro (Alpine `x-trap` si se requiere navegación por teclado compleja) |
| **ARIA** | `role="dialog"`, `aria-modal="true"`, `aria-label` en el modal y `aria-label` en el botón |
| **Dismiss** | 3 vías: botón X, Escape, click outside. Opcional: botón Cerrar en footer |
| **Reduced motion** | `prefers-reduced-motion` debe respetarse. Alpine respeta automáticamente con `x-transition` |
| **Contraste** | Texto informativo: `text-gray-400` sobre fondo oscuro. Strong: `text-gray-300`. Mínimo 4.5:1 |

## Mobile (ADR-005 compliance)

| Elemento | Mobile (base) | Desktop (sm/md/lg) |
|----------|---------------|-------------------|
| Modal padding vertical | `pt-10 pb-10 px-4` | `sm:pt-16` |
| Modal padding horizontal | `px-4` body | `sm:px-6` |
| Botón FAB | `bottom-6 right-6` | igual |
| Grillas de indicadores | 1 columna | `md:grid-cols-2`, `lg:grid-cols-3` |
| Footer | Stack vertical | `flex-row` |

## Checklist de implementación

- [ ] ¿El contenido es útil y descriptivo (no placeholder)?
- [ ] ¿Cada sección tiene descripción + interpretación + recomendación?
- [ ] ¿El modal tiene [X], Escape, click.away, y botón cerrar?
- [ ] ¿El botón tiene `aria-label` o `title`?
- [ ] ¿Target mínimo 44×44px (o 40×40 con padding)?
- [ ] ¿Usa Alpine.js puro (no Livewire)?
- [ ] ¿`x-cloak` presente?
- [ ] ¿`max-h-[90vh] overflow-y-auto` en el panel?
- [ ] ¿Backdrop con `bg-black/70 backdrop-blur-sm`?
- [ ] ¿`x-teleport="body"` para evitar problemas de z-index?
- [ ] ¿Sin `@media` queries custom?
- [ ] ¿Grillas responsive: 1 col → 2 cols → 3 cols?
- [ ] ¿`prefers-reduced-motion` respetado?

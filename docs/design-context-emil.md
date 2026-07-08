# Contexto de diseño inspirado en Emil Kowalski

> Filosofía y guía de estilo para componentes Livewire + Tailwind + Alpine.js.
> Inspirado en el trabajo de Emil Kowalski (animations.dev, vaul, sonner, input-otp, etc.).

---

## Identidad visual

### Paleta de colores

- **Acento principal**: amarillo "buttery" (`#f5d06a` o similar tono mantequilla).
- **Escala base**: Radix Colors (sand, neutral, gray) o Tailwind slate/stone/gray.
- **Fondo**: blanco ligeramente roto (`#fafaf9` → stone-50) en light mode; slate-950/gray-950 en dark.
- **Superficies**: stone-100/200 en light; stone-800/900 en dark.
- **Bordes**: stone-200/300 en light; stone-700/800 en dark.
- **Texto**: stone-900 para títulos, stone-600/500 para cuerpo secundario en light.
- **Semántica**: verde emerald para éxito, rojo red/rose para error, azul sky/blue para info.
- **Dark mode nativo**: todas las variables CSS con `var(--color-*)` o pares `dark:`.

### Tipografía

- **Sans-serif** (UI general): **Inter** — weights 400, 500, 600, 700.
- **Mono** (código, datos técnicos): **Commit Mono** o **JetBrains Mono**.
- **Escala**: Tailwind default (`text-sm`, `text-base`, `text-lg`, `text-xl`, etc.) con algún `text-xs` para metadatos.
- **Jerarquía**: títulos en weight 600–700, cuerpo en 400, labels en 500.

### Espaciado y layout

- Espaciado generoso: usar `space-y-6`, `space-y-8` entre secciones.
- Padding interno: `p-6` mínimo en tarjetas, `p-4` en compacto.
- Ancho máximo de contenido: `max-w-7xl` con `mx-auto`.
- Grids responsivos con Tailwind (`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6`).

---

## Estilo de componentes

### Formularios (Livewire + WireUI)

- **Labels**: siempre visibles, weight 500, `text-sm` o `text-base`, separados del input.
- **Inputs**: borde sutil (`border border-stone-200`), `rounded-lg`, `px-4 py-2.5`, foco con ring (`focus:ring-2 focus:ring-yellow-400/50 focus:border-yellow-400`).
- **Mensajes de error**: debajo del input en `text-sm text-red-600`, con Alpine `x-transition` para aparecer/desaparecer.
- **Placeholder**: `text-stone-400`, descriptivo pero conciso.
- **Botones**: `rounded-lg` con hover/active states; primary con bg amarrillo (`bg-yellow-400 hover:bg-yellow-500`), secondary en stone.
- **Selects y checkboxes**: mismo estilo de borde y ring que inputs.

### Tarjetas y contenedores

- **Nada de cards idénticas en filas de 3–4.** Variar layouts: algunas horizontales, otras verticales, tamaños distintos.
- **Borde**: `border border-stone-200`, `rounded-xl`, `shadow-sm` sutil.
- **Header de tarjeta**: separado con borde inferior o espaciado; título en `text-lg font-semibold`.
- **No usar gradientes decorativos genéricos.** Fondo sólido con sutiles variaciones de tono.

### Tablas (datos administrativos)

- Cabecera con fondo `bg-stone-50` o `bg-stone-100`, texto `text-xs font-semibold uppercase tracking-wider text-stone-500`.
- Celdas con `py-3 px-4`, alineación según contenido (números a la derecha).
- Filas con hover: `hover:bg-stone-50`.
- Responsive: overflow-x-auto para móvil.

### Navegación y sidebar

- Enlaces activos con indicador visual (barra izquierda o bg distinto).
- Iconos simples (Heroicons outline).
- Espaciado consistente entre items: `space-y-1`.

---

## Micro-interacciones con Alpine.js

### Transiciones de entrada/salida

```blade
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-1">
```

### Transiciones de lista (x-for)

```blade
<template x-for="item in items" :key="item.id">
  <div x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 scale-95">
  </div>
</template>
```

### Toggle / acordeón

```blade
<button @click="open = !open"
        x-bind:class="{ 'rotate-180': open }"
        class="transition-transform duration-200">
  <x-heroicon-o-chevron-down class="w-5 h-5" />
</button>
```

### Duración y easing

- Entrada: 200ms, `ease-out`
- Salida: 150ms, `ease-in`
- Transform/opacity: `duration-200 ease-out`
- Rotación/color: `duration-200`
- Altura (collapsibles): 300ms

---

## Filosofía de diseño

### Principios

1. **Construir plataformas que el propio desarrollador quiere usar.**
   - Priorizar usabilidad y claridad sobre efectos excesivos.
   - Cada elemento debe tener un propósito claro.

2. **Lanzar en "drops" (bloques temáticos).**
   - Componentes atómicos y reutilizables antes que páginas completas.
   - Iterar sobre un bloque hasta que esté sólido antes de pasar al siguiente.

3. **Jerarquía sobre decoración.**
   - La información más importante debe ser la más visible.
   - Usar tipografía, espaciado y color para establecer jerarquía, no cajas y sombras.

4. **Accesibilidad nativa.**
   - Labels correctos, roles ARIA donde necesarios, contraste suficiente.
   - Navegación por teclado en todos los componentes interactivos.
   - `focus-visible:ring-*` para indicar foco.

### Anti-patrones (evitar)

- ❌ Cards idénticas en filas de 3–4 con el mismo contenido.
- ❌ Gradientes genéricos de IA como fondos de hero.
- ❌ Paletas "beige premium" sin contraste.
- ❌ Placeholders como "Jane Doe" o "John Smith".
- ❌ Em-dashes excesivos — `—` .
- ❌ Sombras múltiples o exageradas.
- ❌ Animaciones sin propósito (que no guían la atención del usuario).
- ❌ Fondo blanco puro (#fff) sin textura o variación suficiente.

---

## Integración con Laravel + Livewire + Tailwind + Alpine

### Patrones Livewire recomendados

- Componentes full-page para vistas principales, inline para modales/tablas.
- Validación con `$this->validate()` y mensajes de error en `$errors`.
- Usar propiedades computadas (`getSomeProperty`) para lógica de presentación.
- Eventos Livewire (`$emit`, `$dispatch`) para comunicación entre componentes.

### Convenciones Blade

```blade
{{-- Label+Input reutilizable --}}
<div class="space-y-1">
    <label for="{{ $id }}" class="block text-sm font-medium text-stone-700">
        {{ $label }}
    </label>
    <input type="{{ $type ?? 'text' }}"
           id="{{ $id }}"
           wire:model="{{ $model }}"
           @error($model)
               class="block w-full rounded-lg border-red-300 shadow-xs focus:border-red-400 focus:ring-2 focus:ring-red-500/20"
           @else
               class="block w-full rounded-lg border-stone-200 shadow-xs focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50"
           @enderror
           placeholder="{{ $placeholder ?? '' }}">
    @error($model)
        <p class="text-sm text-red-600" x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-200">
            {{ $message }}
        </p>
    @enderror
</div>
```

### Dark mode

```css
/* En app.css o tailwind.config */
.dark .card {
  @apply bg-stone-800 border-stone-700;
}
.dark .input {
  @apply bg-stone-900 border-stone-700 text-stone-100;
}
```

---

## Referencias

- [animations.dev](https://animations.dev) — componentes interactivos de Emil Kowalski
- [vaul](https://vaul.emilkowal.ski) — drawer component
- [sonner](https://sonner.emilkowal.ski) — toast notifications
- [input-otp](https://input-otp.emilkowal.ski) — OTP input
- [Radix Colors](https://www.radix-ui.com/colors) — escala de colores base

---
name: crud-mode-toggle
description: Use when adding a Grid/Table (or List) view toggle that persists to localStorage and syncs across sibling Blade components rendered in the same page with Alpine.js
---

# CRUD Mode Toggle (Grid / Table)

## Overview

A persistent, cross-component-synchronized view-mode toggle using Alpine.js `x-data`, `localStorage`, and custom DOM events. The pattern has two parts:

1. **Toggle buttons** — read initial mode from localStorage, write + dispatch a custom event on change
2. **View container** — listen for that custom event, conditionally render grid/table views via `x-show`

## When to Use

- Users need to switch between a visual Grid and a Table/List view of the same data
- The choice must survive page reloads (persist to localStorage)
- Multiple Blade components on the same page need to sync their view mode (toggle in one, views rendered in another)
- You're using Alpine.js and don't want a full state library just for one preference

## Core Pattern

### 1. Toggle Buttons

Wrap two buttons in `x-data`. The initial value reads from `localStorage` with a fallback default.

```blade
<div x-data="{ mode: localStorage.getItem('YOUR-KEY') || 'table' }"
     x-init="$watch('mode', val => {
         localStorage.setItem('YOUR-KEY', val);
         window.dispatchEvent(new CustomEvent('YOUR-EVENT-NAME', { detail: { mode: val } }))
     })">
    <button @click="mode = 'grid'"
        :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
        {{-- grid icon SVG --}}
        <span class="hidden sm:inline">Grid</span>
    </button>
    <button @click="mode = 'table'"
        :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
        {{-- table/list icon SVG --}}
        <span class="hidden sm:inline">Tabla</span>
    </button>
</div>
```

### 2. View Container (sibling or parent)

Reads from localStorage independently (initial render) and listens for the custom event to stay in sync.

```blade
<div x-cloak
     x-data="{ mode: localStorage.getItem('YOUR-KEY') || 'table' }"
     x-init="() => { if (!localStorage.getItem('YOUR-KEY')) localStorage.setItem('YOUR-KEY', 'table') }"
     x-on:YOUR-EVENT-NAME.window="mode = $event.detail.mode">

    {{-- Grid Mode --}}
    <div x-show="mode === 'grid'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        {{-- grid content --}}
    </div>

    {{-- Table Mode --}}
    <div x-show="mode === 'table'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        {{-- table content --}}
    </div>
</div>
```

### 3. Masonry Grid CSS (for Grid mode)

Include a `<style>` block inside the Grid view for responsive masonry columns:

```blade
<style>
    .masonry-grid { --masonry-cols: 1; columns: var(--masonry-cols); column-gap: 0.625rem; }
    .masonry-item { break-inside: avoid; margin-bottom: 0.625rem; }
    .masonry-empty { break-inside: avoid; text-align: center; }
    @media (min-width: 640px)  { .masonry-grid { --masonry-cols: 2; } }
    @media (min-width: 1024px) { .masonry-grid { --masonry-cols: 3; } }
    @media (min-width: 1280px) { .masonry-grid { --masonry-cols: 4; } }
    @supports (grid-template-rows: masonry) {
        .masonry-grid { display: grid; gap: 0.625rem; columns: unset; grid-template-columns: repeat(var(--masonry-cols), 1fr); grid-template-rows: masonry; }
        .masonry-item { break-inside: unset; margin-bottom: unset; }
        .masonry-empty { grid-column: 1 / -1; }
    }
</style>
<div class="masonry-grid">
    {{-- cards here --}}
</div>
```

## Key Details

| Concern | How |
|---------|-----|
| **Storage key** | Unique per feature (e.g. `pevaluacions-view-mode`, `lessons-view-mode`) |
| **Custom event name** | Unique per feature (e.g. `pevaluacions-view-mode-changed`, `lessons-view-mode-changed`) |
| **Default mode** | `'table'` — the fallback in `localStorage.getItem(...) \|\| 'table'` |
| **x-cloak** | Prevents flash of wrong view before Alpine initializes |
| **x-transition:enter** | `duration-200` ease-out for smooth mode switching |
| **Active button style** | `bg-emerald-500/15 text-emerald-400 border-emerald-500/30` |
| **Inactive button style** | `bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300` |

## Common Mistakes

- **Using different localStorage keys in toggle vs view container** — both `x-data` expressions must use the same key: `localStorage.getItem('YOUR-KEY') || 'table'`
- **Event name mismatch** — the `CustomEvent` name dispatched by the toggle must match the `x-on:EVENT-NAME.window` listener exactly
- **Missing `x-cloak`** on the view container — without it, both views flash momentarily before Alpine renders
- **Forgetting `x-transition:enter` on initial render** — first load won't animate, but subsequent toggles will. This is expected; `x-transition:enter` only fires on DOM insertion, which happens once for `x-show`

## Real-World Example

This project uses the pattern in:

- **Toggle**: `resources/views/livewire/profesor/activity/pevaluacion-list.blade.php` (lines 82-99)
- **View container**: `resources/views/profesors/activities/table/index.blade.php` (lines 1-233)
- **Event**: `pevaluacions-view-mode-changed`
- **Key**: `pevaluacions-view-mode`

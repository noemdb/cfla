<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

/**
 * MenuBuilder - Genera menús de navegación desde config/menus.php
 *
 * Centraliza toda la lógica de:
 * - Filtrado por permisos
 * - Estado activo (routeIs)
 * - Renderizado desktop (dropdowns) y mobile (acordeones)
 * - Mega-menús multi-columna
 * - Badges Livewire
 */
class MenuBuilder
{
    protected array $config;
    protected string $layout;

    public function __construct(string $layout = 'admin')
    {
        $this->config = config('menus');
        $this->layout = $layout;
    }

    /**
     * Obtiene los grupos de menú para el layout actual
     */
    protected function getGroups(): array
    {
        $layoutConfig = $this->config['layouts'][$this->layout] ?? ['groups' => []];
        $groupKeys = $layoutConfig['groups'] ?? [];

        return collect($groupKeys)
            ->map(fn ($key) => $this->config['groups'][$key] ?? null)
            ->filter()
            ->filter(fn ($group) => $this->hasPermission($group['permission'] ?? null))
            ->values()
            ->toArray();
    }

    /**
     * Verifica si el usuario tiene el permiso requerido
     */
    protected function hasPermission(?string $permission): bool
    {
        if (!$permission) {
            return true;
        }

        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Permisos basados en métodos del User model
        return match ($permission) {
            'is_admin_or_diagnostic' => $user->is_admin || $user->is_diagnostic,
            'is_admin_or_coordinacion' => $user->is_admin || $user->isCoordinacion(),
            'is_profesor' => $user->isProfesor(),
            'is_director' => $user->is_director,
            'is_planner_or_admin_or_diagnostic' => $user->is_planner || $user->is_admin || $user->is_diagnostic,
            'is_student' => $user->isEstudiante() ?? false,
            default => true,
        };
    }

    /**
     * Verifica si una ruta está activa
     */
    protected function isActive(?string $activePattern): string
    {
        if (!$activePattern) {
            return '';
        }

        // Soporta múltiples patrones separados por || y negación con !
        $patterns = explode('||', $activePattern);
        foreach ($patterns as $pattern) {
            $pattern = trim($pattern);
            $negate = str_starts_with($pattern, '!');
            if ($negate) {
                $pattern = substr($pattern, 1);
            }
            $matches = Request::routeIs($pattern);
            if ($negate) {
                $matches = !$matches;
            }
            if ($matches) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Renderiza un icono SVG
     */
    protected function renderIcon(string $path, ?string $color = null, array $attributes = []): string
    {
        $colorClass = $color ? "text-{$color}-400" : '';
        $attrs = collect($attributes)->map(fn ($v, $k) => "$k=\"$v\"")->implode(' ');
        $attrs = $attrs ? ' ' . $attrs : '';

        return <<<SVG
<svg class="w-4 h-4 shrink-0 {$colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24" {$attrs}>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{$path}" />
</svg>
SVG;
    }

    /**
     * Renderiza un item individual (enlace)
     */
    protected function renderItem(array $item, bool $mobile = false): string
    {
        if (!empty($item['disabled'])) {
            return $this->renderDisabledItem($item, $mobile);
        }

        $activeClass = $this->isActive($item['active'] ?? null);
        $badge = '';
        if (!empty($item['badge'])) {
            $badge = "<livewire:{$item['badge']} />";
        }

        $icon = $this->renderIcon(
            $item['icon'] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            $item['icon_color'] ?? null
        );

        $classes = $mobile
            ? "flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg transition-colors {$activeClass}"
            : "flex items-center gap-2.5 px-3.5 py-2 text-sm transition-colors {$activeClass}";

        if ($mobile) {
            $baseClass = $activeClass
                ? 'bg-emerald-500/5 text-emerald-400'
                : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5';
            $classes .= " {$baseClass}";
        } else {
            $baseClass = $activeClass
                ? 'text-emerald-400 bg-emerald-500/5'
                : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5';
            $classes .= " {$baseClass}";
        }

        $href = $this->href($item);

        return <<<HTML
<a href="{$href}" class="{$classes}">
    {$icon}
    {$item['label']}
    {$badge}
</a>
HTML;
    }

    /**
     * Resuelve el href de un item: el valor de `route` es un NOMBRE de ruta
     * (config/menus.php), no una URL; si no existe la ruta se usa el literal.
     */
    protected function href(array $item): string
    {
        $route = $item['route'] ?? '#';

        if ($route === '#') {
            return '#';
        }

        return \Illuminate\Support\Facades\Route::has($route)
            ? route($route)
            : $route;
    }

    /**
     * Renderiza un item deshabilitado
     */
    protected function renderDisabledItem(array $item, bool $mobile = false): string
    {
        return '<span class="px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">' .
            $item['label'] .
            '</span>';
    }

    /**
     * Renderiza un grupo completo para desktop (dropdown)
     */
    protected function renderGroupDesktop(array $group): string
    {
        $items = $group['items'] ?? [];
        $isMegaMenu = !empty($group['mega_menu']);

        if ($isMegaMenu) {
            return $this->renderMegaMenu($group);
        }

        // Items normales + admin_only_items si es admin
        $allItems = $items;
        if (!empty($group['admin_only_items']) && Auth::user()?->is_admin) {
            $allItems = array_merge($allItems, $group['admin_only_items']);
        }

        if (empty($allItems)) {
            return '';
        }

        $color = $group['color'] ?? 'emerald';
        $icon = $this->renderIcon($group['icon'], $color);

        $itemsHtml = collect($allItems)->map(fn ($item) => $this->renderItem($item))->implode("\n");

        // Clases del botón trigger
        $btnActiveClass = $this->hasActiveItems($allItems) ? "bg-{$color}-500/10 text-{$color}-400" : "text-gray-400 hover:text-{$color}-300 hover:bg-white/5";

        return <<<HTML
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false"
        class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {$btnActiveClass}">
        {$icon}
        {$group['label']}
        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute left-0 mt-1 w-56 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-2 z-50">
        {$itemsHtml}
    </div>
</div>
HTML;
    }

    /**
     * Renderiza mega-menú (planificación) con 3 columnas
     */
    protected function renderMegaMenu(array $group): string
    {
        $columns = $group['columns'] ?? [];
        if (empty($columns)) {
            return '';
        }

        $color = $group['color'] ?? 'emerald';
        $icon = $this->renderIcon($group['icon'], $color);

        $columnsHtml = '';
        foreach ($columns as $colKey => $col) {
            $colItemsHtml = collect($col['items'])->map(fn ($item) => $this->renderItem($item))->implode("\n");

            $columnsHtml .= <<<HTML
<div class="min-w-max space-y-0.5">
    <div class="text-[10px] font-bold uppercase tracking-widest text-{$color}-400/60 px-3 py-1.5">{$col['title']}</div>
    {$colItemsHtml}
</div>
HTML;
        }

        $btnActiveClass = $this->hasActiveMegaMenu($group) ? "bg-{$color}-500/10 text-{$color}-400" : "text-gray-400 hover:text-{$color}-300 hover:bg-white/5";

        return <<<HTML
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false"
        class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {$btnActiveClass}">
        {$icon}
        {$group['label']}
        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute left-1/2 -translate-x-1/2 mt-1 w-max max-w-[calc(100vw-2rem)] bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-5 z-50 overflow-hidden">

        <div class="mb-3 pb-2 border-b border-white/5">
            <a href="{{ route('app.planning.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-{$color}-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.index') ? 'text-{$color}-400 bg-{$color}-500/5' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>
        </div>

        <div class="flex gap-x-6">
            {$columnsHtml}
        </div>
    </div>
</div>
HTML;
    }

    /**
     * Verifica si algún item del grupo está activo
     */
    protected function hasActiveItems(array $items): bool
    {
        foreach ($items as $item) {
            if ($this->isActive($item['active'] ?? null)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si mega-menú tiene items activos
     */
    protected function hasActiveMegaMenu(array $group): bool
    {
        $columns = $group['columns'] ?? [];
        foreach ($columns as $col) {
            foreach ($col['items'] as $item) {
                if ($this->isActive($item['active'] ?? null)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Renderiza un grupo para mobile (acordeón)
     */
    protected function renderGroupMobile(array $group): string
    {
        $items = $group['items'] ?? [];
        $isMegaMenu = !empty($group['mega_menu']);

        if ($isMegaMenu) {
            return $this->renderMegaMenuMobile($group);
        }

        // Items normales + admin_only_items si es admin
        $allItems = $items;
        if (!empty($group['admin_only_items']) && Auth::user()?->is_admin) {
            $allItems = array_merge($allItems, $group['admin_only_items']);
        }

        if (empty($allItems)) {
            return '';
        }

        $color = $group['color'] ?? 'emerald';
        $icon = $this->renderIcon($group['icon']);

        $itemsHtml = collect($allItems)->map(fn ($item) => $this->renderItem($item, true))->implode("\n");

        $id = strtolower(str_replace(' ', '-', $group['label'])) . '-submenu';

        return <<<HTML
<div x-data="{ open: false }">
    <button @click="open = !open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{$id}"
            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-{$color}-400/40">
        <span class="flex items-center gap-2">
            {$icon}
            {$group['label']}
        </span>
        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" id="{$id}" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
        {$itemsHtml}
    </div>
</div>
HTML;
    }

    /**
     * Renderiza mega-menú para mobile (versión simplificada)
     */
    protected function renderMegaMenuMobile(array $group): string
    {
        $columns = $group['columns'] ?? [];
        if (empty($columns)) {
            return '';
        }

        $color = $group['color'] ?? 'emerald';
        $icon = $this->renderIcon($group['icon']);

        // En mobile aplanamos las columnas en una lista simple
        $allItems = [];
        foreach ($columns as $col) {
            if (!empty($col['items'])) {
                $allItems = array_merge($allItems, $col['items']);
            }
        }

        $itemsHtml = collect($allItems)->map(fn ($item) => $this->renderItem($item, true))->implode("\n");

        $id = 'planning-submenu';

        return <<<HTML
<div x-data="{ open: false }">
    <button @click="open = !open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{$id}"
            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-{$color}-400/40">
        <span class="flex items-center gap-2">
            {$icon}
            {$group['label']}
        </span>
        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" id="{$id}" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
        {$itemsHtml}
    </div>
</div>
HTML;
    }

    /**
     * Renderiza todos los grupos para desktop
     */
    public function renderDesktop(): HtmlString
    {
        $groups = $this->getGroups();
        $html = collect($groups)->map(fn ($group) => $this->renderGroupDesktop($group))->implode("\n");
        return new HtmlString($html);
    }

    /**
     * Renderiza todos los grupos para mobile
     */
    public function renderMobile(): HtmlString
    {
        $groups = $this->getGroups();
        $html = collect($groups)->map(fn ($group) => $this->renderGroupMobile($group))->implode("\n");
        return new HtmlString($html);
    }
}
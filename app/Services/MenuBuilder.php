<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * MenuBuilder - Genera menús de navegación desde config/menus.php
 *
 * Responsabilidades:
 * - Resolver el layout/menú primario a partir del rol del usuario autenticado
 *   (navbar por rol, no por archivo de layout).
 * - Filtrar grupos e ítems por permisos.
 * - Resolver el estado activo de cada ítem (routeIs, con soporte para
 *   operadores &&, || y negación !).
 * - Resolver hrefs con route().
 * - Delegar el renderizado a vistas/componentes Blade.
 */
class MenuBuilder
{
    /**
     * Metadatos (subtítulo) por layout.
     */
    public static array $roleMeta = [
        'admin' => 'Panel Administrativo',
        'director' => 'Panel de Dirección',
        'leadership' => 'Seguimiento · Jefes de Área',
        'coordinacion' => 'Coordinación de Programas Educativos',
        'planning' => 'Planificación Académica',
        'profesor' => 'Panel del Profesor',
        'student' => 'Portal Estudiante',
    ];

    protected array $config;

    protected string $layout;

    public function __construct(string $layout = 'admin')
    {
        $this->config = config('menus');
        $this->layout = $layout;
    }

    /**
     * Resuelve el layout primario a partir del rol del usuario.
     * El navbar es por rol: un mismo usuario ve SIEMPRE su menú,
     * independientemente del archivo de layout que envuelva la página.
     */
    public static function resolveLayoutForUser(?object $user = null): string
    {
        $user ??= Auth::user();

        if (! $user) {
            return 'admin';
        }

        return match (true) {
            (bool) $user->is_admin => 'admin',
            (bool) $user->is_director => 'director',
            (bool) $user->is_leadership => 'leadership',
            (bool) $user->isCoordinacion() => 'coordinacion',
            (bool) $user->is_planner => 'planning',
            (bool) $user->isProfesor() => 'profesor',
            (bool) $user->is_student => 'student',
            default => 'admin',
        };
    }

    public static function subtitleForLayout(string $layout): string
    {
        return static::$roleMeta[$layout] ?? 'Panel';
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
     * Verifica si el usuario tiene el permiso requerido.
     * Delega en los atributos/accessors del modelo User.
     */
    protected function hasPermission(?string $permission): bool
    {
        if (! $permission) {
            return true;
        }

        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return match ($permission) {
            'is_admin_or_diagnostic' => $user->is_admin || $user->is_diagnostic,
            'is_admin_or_diagnostic_or_profesor' => $user->is_admin || $user->is_diagnostic || (bool) $user->isProfesor(),
            'is_admin_or_coordinacion' => $user->is_admin || $user->isCoordinacion(),
            'is_admin_or_coordinacion_or_director_or_profesor_or_planner' => $user->is_admin || $user->isCoordinacion() || (bool) $user->is_director || (bool) $user->isProfesor() || (bool) $user->is_planner,
            'is_profesor' => (bool) $user->isProfesor(),
            'is_profesor_or_planner' => (bool) $user->isProfesor() || (bool) $user->is_planner,
            'is_director' => (bool) $user->is_director,
            'is_leadership' => (bool) $user->is_leadership,
            'is_planner_or_admin_or_diagnostic' => $user->is_planner || $user->is_admin || $user->is_diagnostic,
            'is_planner_or_admin_or_diagnostic_or_director' => $user->is_planner || $user->is_admin || $user->is_diagnostic || (bool) $user->is_director,
            default => true,
        };
    }

    /**
     * Evalúa una expresión de patrones activos.
     * Soporta operadores `&&`, `||` y negación con `!`.
     * Ej.: 'app.planning.diagnostico* && !app.planning.diagnostico.referents*'
     */
    protected function matches(string $expression): bool
    {
        foreach (explode('||', $expression) as $orPart) {
            $orPart = trim($orPart);
            if ($orPart === '') {
                continue;
            }
            if ($this->matchesAnd(explode('&&', $orPart))) {
                return true;
            }
        }

        return false;
    }

    protected function matchesAnd(array $parts): bool
    {
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $negate = str_starts_with($part, '!');
            if ($negate) {
                $part = substr($part, 1);
            }

            $result = $part !== '' && Request::routeIs($part);
            if ($negate) {
                $result = ! $result;
            }

            if (! $result) {
                return false;
            }
        }

        return true;
    }

    protected function isActive(?string $pattern): bool
    {
        if (! $pattern) {
            return false;
        }

        return $this->matches($pattern);
    }

    /**
     * Resuelve el href de un ítem. Los valores de 'route' del config son
     * nombres de ruta; se resuelven con route(). Si ya es una URL o '#' se usa tal cual.
     */
    protected function resolveHref(array $item): string
    {
        $route = $item['route'] ?? '#';

        if ($route === '' || $route === '#') {
            return '#';
        }

        if (Str::startsWith($route, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])) {
            return $route;
        }

        try {
            return route($route);
        } catch (\Throwable) {
            return '#';
        }
    }

    /**
     * Construye el árbol de menú resuelto para el layout actual:
     * grupos filtrados por permiso, admin_only_items fusionados,
     * ítems con 'active' (bool) y 'href' calculados.
     */
    public function resolveGroups(): array
    {
        $groups = [];

        foreach ($this->getGroups() as $group) {
            if (! empty($group['mega_menu'])) {
                $hasActive = false;

                foreach ($group['columns'] as $colKey => $col) {
                    foreach ($col['items'] as $itemKey => $item) {
                        $item['active'] = $this->isActive($item['active'] ?? null);
                        $item['href'] = $this->resolveHref($item);
                        $hasActive = $hasActive || $item['active'];
                        $group['columns'][$colKey]['items'][$itemKey] = $item;
                    }
                }

                $group['active'] = $hasActive;
                $group['dashboard_route'] = $group['dashboard_route'] ?? null;
                $group['dashboard_href'] = $group['dashboard_route']
                    ? $this->resolveHref(['route' => $group['dashboard_route']])
                    : null;
                $group['dashboard_active'] = $group['dashboard_route']
                    ? $this->isActive($group['dashboard_route'])
                    : false;

                $groups[] = $group;

                continue;
            }

            $allItems = $group['items'] ?? [];
            if (! empty($group['admin_only_items']) && Auth::user()?->is_admin) {
                $allItems = array_merge($allItems, $group['admin_only_items']);
            }

            if (empty($allItems)) {
                continue;
            }

            $hasActive = false;
            foreach ($allItems as $itemKey => $item) {
                $item['active'] = $this->isActive($item['active'] ?? null);
                $item['href'] = $this->resolveHref($item);
                $hasActive = $hasActive || $item['active'];
                $allItems[$itemKey] = $item;
            }

            $group['items'] = $allItems;
            $group['active'] = $hasActive;
            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Renderiza todos los grupos para desktop
     */
    public function renderDesktop(): HtmlString
    {
        return new HtmlString(view('menu.desktop', ['groups' => $this->resolveGroups()])->render());
    }

    /**
     * Renderiza todos los grupos para mobile
     */
    public function renderMobile(): HtmlString
    {
        return new HtmlString(view('menu.mobile', ['groups' => $this->resolveGroups()])->render());
    }
}

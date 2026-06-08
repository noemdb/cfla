<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogRouteAccess
{
    /**
     * Rutas y patrones excluidos del registro de actividad
     */
    protected $excludedRoutes = [
        // Rutas específicas
        // 'login',
        // 'logout',
        'register',
        'password/reset',
        'activity-logs',
        'app/admon/ajax/fill',

        // Rutas de sistema y frameworks
        'livewire',
        'horizon',
        'telescope',
        'nova',
        'sanctum',
        'broadcasting',
        '_ignition',
        '_debugbar',

        // Rutas de recursos
        'storage',
        'vendor',
        'node_modules',
    ];

    /**
     * Extensiones de archivo excluidas del registro
     */
    protected $excludedExtensions = [
        'css',
        'js',
        'jpg',
        'jpeg',
        'png',
        'gif',
        'ico',
        'svg',
        'woff',
        'woff2',
        'ttf',
        'eot',
        'pdf',
        'doc',
        'docx'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Verificar si la ruta debe ser excluida
        if ($this->shouldExcludeRoute($request)) {
            return $next($request);
        }

        // Solo registrar actividad si el usuario está autenticado
        if (Auth::check()) {
            // Registrar la actividad
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'route' => $path,
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('Acceso a ruta');
        }

        return $next($request);
    }

    /**
     * Determina si una ruta debe ser excluida del registro
     */
    protected function shouldExcludeRoute(Request $request): bool
    {
        $path = $request->path();

        // Verificar si es una ruta API
        if ($request->is('api/*')) {
            return true;
        }

        // Verificar extensiones excluidas
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($extension, $this->excludedExtensions)) {
            return true;
        }

        // Verificar rutas excluidas
        foreach ($this->excludedRoutes as $excludedRoute) {
            if (str_starts_with($path, $excludedRoute)) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Acceso restringido a estudiantes.');
        }

        $user = auth()->user();

        // Admin bypass: administradores pueden previsualizar vistas de estudiante
        if ($user->is_admin) {
            return $next($request);
        }

        if (!$user->isStudent()) {
            abort(403, 'Acceso restringido a estudiantes.');
        }

        return $next($request);
    }
}

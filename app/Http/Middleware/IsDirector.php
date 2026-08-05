<?php
// app/Http/Middleware/IsDirector.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsDirector
{
    /**
     * Acepta admins y usuarios con is_director. A diferencia de los otros
     * roles de seguimiento, este módulo es 100% read-only: el middleware
     * solo protege la VISUALIZACIÓN, nunca acciones de escritura (no existen).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_director) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de dirección.');
    }
}

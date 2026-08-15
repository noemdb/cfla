<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Matriz RBAC BINNACLE-001 §6: acceso al panel de bitácora
 * (lista, dashboard, timeline). Roles: admin, is_director, is_leadership.
 */
class CanViewBinnacle
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ($user->is_admin || $user->is_director || $user->is_leadership)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al panel de bitácora.');
    }
}

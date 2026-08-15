<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Matriz RBAC BINNACLE-001 §6: exportación CSV/PDF de la bitácora.
 * Solo admin e is_director; is_leadership NO puede exportar.
 */
class CanExportBinnacle
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ($user->is_admin || $user->is_director)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para exportar la bitácora.');
    }
}

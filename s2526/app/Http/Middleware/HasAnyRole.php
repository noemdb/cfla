<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasAnyRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'No autenticado.');
        }

        // Verificar si el usuario tiene ALGUNO de los roles especificados
        foreach ($roles as $role) {
            // Convertir a formato de método: control -> IsControl
            $methodName = 'Is' . ucfirst($role);
            
            if (method_exists($user, $methodName) && $user->$methodName()) {
                return $next($request);
            }
        }

        abort(403, 'No tienes los permisos necesarios para acceder a esta página.');
    }
}
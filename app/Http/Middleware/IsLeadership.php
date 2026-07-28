<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsLeadership
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_leadership) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de seguimiento.');
    }
}

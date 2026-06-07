<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsPlanner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_planner || Auth::user()->is_diagnostic)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de planificación.');
    }
}

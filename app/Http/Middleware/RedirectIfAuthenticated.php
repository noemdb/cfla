<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->is_admin || $user->is_diagnostic) {
                    return redirect()->to('/admin');
                }

                if ($user->is_planner) {
                    return redirect()->route('app.planning.index');
                }

                if ($user->is_coordinacion) {
                    return redirect()->route('app.coordinacion.index');
                }

                if ($user->is_leadership) {
                    return redirect()->route('app.leadership.dashboard');
                }

                if ($user->isDirector()) {
                    return redirect()->route('app.director.index');
                }

                if ($user->isProfesor()) {
                    return redirect()->to('/app/profesors/home');
                }

                if ($user->is_student) {
                    return redirect()->to('/app/estudiante/home');
                }

                return redirect()->to('/');
            }
        }

        return $next($request);
    }
}

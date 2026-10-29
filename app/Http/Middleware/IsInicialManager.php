<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Acceso al seguimiento de Educación Inicial (solo lectura).
 *
 * Blueprint: blueprint/inicial · doc 11 · ADR-11.4.
 *
 * A diferencia de IsInicial, NO deja pasar a is_admin: el acceso a los datos
 * pedagógicos de todos los docentes exige una asignación explícita. Si soporte
 * necesita entrar, se marca el flag temporalmente y queda auditado en `users`.
 *
 * El 403 es explícito (no un retorno nulo): un usuario sin permiso debe ver un
 * 403 con mensaje, no un 500.
 */
class IsInicialManager
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->isInicialManager()) {
            abort(403, 'No tienes permiso para acceder al seguimiento de Educación Inicial.');
        }

        return $next($request);
    }

    /**
     * Diagnóstico de despliegue: la columna aún no existe.
     *
     * Mismo patrón que IsInicial::migracionPendiente(): permite que las
     * pruebas y los comandos de diagnóstico den un mensaje accionable en vez
     * de un 403 silencioso para todos.
     */
    public static function migracionPendiente(): bool
    {
        return ! Schema::hasColumn('users', 'is_inicial_manager');
    }
}

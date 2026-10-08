<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Acceso al módulo de Educación Inicial (pestudio 6), perspectiva del docente.
 *
 * Blueprint: blueprint/inicial · decisión D2 · fase F1.
 *
 * Sustituye a los 4 checks `Is{Inicial,Evaluacion,Planning,Academico}` del
 * legacy, que además de estar apoyados en la tabla `rols` se solapaban entre sí:
 * cualquier usuario con rol SISTEMA/ADMINISTRADOR pasaba los cuatro.
 *
 * Dos correcciones deliberadas frente al legacy:
 *
 *  1. `abort(403)` EXPLÍCITO. El legacy devolvía `null` silencioso cuando no
 *     encontraba la autoridad asociada, lo que degradaba a un error 500 en
 *     lugar de un 403 con mensaje. Aquí no hay retorno nulo posible.
 *
 *  2. `is_admin` SÍ accede, a conciencia. Es la convención del resto del
 *     proyecto (`IsProfesor`, `IsPlanner`, `IsCoordinacion` la usan) y hace
 *     falta para soporte. No es el problema que se señaló en el legacy: allí la
 *     fuga era que un docente común colaba por los cuatro chequeos; aquí el
 *     acceso de admin es deliberado y explícito.
 */
class IsInicial
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'No tienes permiso para acceder al módulo de Educación Inicial.');
        }

        if ($user->is_admin || $user->isInicial()) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de Educación Inicial.');
    }

    /**
     * Diagnóstico de despliegue: la columna `users.is_inicial` todavía no
     * existe hasta que se corra la migración `add_is_inicial_to_users_table`.
     *
     * While esté pendiente, `$user->is_inicial` devuelve `null` para todo el
     * mundo y este middleware rechaza a los DOCENTES LEGÍTIMOS con un 403
     * (fail-closed, que es lo correcto desde el punto de vista de seguridad,
     * pero silencioso para quien depura). Este helper convierte ese caso en un
     * mensaje accionable.
     */
    public static function migracionPendiente(): bool
    {
        return ! Schema::hasColumn('users', 'is_inicial');
    }
}

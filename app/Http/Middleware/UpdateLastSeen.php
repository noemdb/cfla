<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Heartbeat de presencia (chart "sesiones activas" del dashboard /admin).
 *
 * Refresca `users.last_seen_at` en requests autenticadas, como máximo una
 * vez cada 5 minutos por usuario. Usa query builder a propósito: un update
 * Eloquent dispararía UserObserver@updated y llenaría la bitácora.
 * Silencioso por diseño: si la columna aún no existe (migración pendiente)
 * o la BD falla, la request sigue su curso.
 */
class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $user = $request->user();

            if ($user === null || ! Schema::hasColumn('users', 'last_seen_at')) {
                return $response;
            }

            $lastSeen = $user->getAttribute('last_seen_at');

            if ($lastSeen === null || $lastSeen->lt(now()->subMinutes(5))) {
                // whereKey() solo existe en Eloquent: aquí va columna explícita.
                DB::table('users')
                    ->where($user->getKeyName(), $user->getKey())
                    ->update(['last_seen_at' => now()]);
            }
        } catch (\Throwable $e) {
            // Silencioso: la presencia nunca rompe la request.
        }

        return $response;
    }
}

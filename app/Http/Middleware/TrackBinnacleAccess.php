<?php

namespace App\Http\Middleware;

use App\Services\Binnacle;
use Closure;
use Illuminate\Http\Request;

/**
 * Registra accesos a rutas marcadas explícitamente (Spec BINNACLE-001 §5.5).
 *
 * Se aplica SOLO donde se indique, nunca globalmente:
 *   ->middleware('binnacle.track')          // category = user_action
 *   ->middleware('binnacle.track:security') // category = security
 *
 * Registra event_type=access con el usuario autenticado como sujeto (system
 * para invitados), código de estado y tiempo de respuesta en metadata.
 */
class TrackBinnacleAccess
{
    public function handle(Request $request, Closure $next, ?string $category = null)
    {
        $start = hrtime(true);

        try {
            $response = $next($request);
        } finally {
            $durationMs = (hrtime(true) - $start) / 1e6;

            Binnacle::log('access', [
                'title' => 'Acceso a ruta protegida',
                'category' => $category ?? 'user_action',
                'severity' => 'info',
                'subject' => auth()->user() ?? Binnacle::systemSubject(),
                'metadata' => [
                    'response_status' => isset($response) ? $response->getStatusCode() : null,
                    'response_ms' => round($durationMs, 1),
                ],
            ]);
        }

        return $response;
    }
}
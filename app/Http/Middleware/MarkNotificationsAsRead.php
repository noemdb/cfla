<?php

namespace App\Http\Middleware;

use App\Services\NotificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marca como leídas las notificaciones cuyo destino es la ruta que se está
 * visiting (ítem 12).
 *
 * Si el usuario abrió la pantalla donde se resolverían sus avisos, ya los vio:
 * el badge debe bajar sin clics manuales. La configuración vive en
 * `config/notifications.php#auto_read` (nombre de ruta → tipos a marcar).
 *
 * Va en un middleware y no en el `mount()` de cada componente a propósito: los
 * listados de Livewire se vuelven a renderizar en cada actualización, y aquí solo
 * se actúa en la petición GET inicial de la ruta (los POST a
 * /livewire/update no pasan por esta ruta).
 */
class MarkNotificationsAsRead
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if ($routeName && $request->user()) {
            // OJO: los nombres de ruta contienen puntos ('app.planning.activities.index')
            // y `config('notifications.auto_read.'.$routeName)` los interpretaría
            // como anidamiento y no encontraría nada. Hay que indexar el array
            // completo por el nombre exacto.
            $map = (array) config('notifications.auto_read', []);
            $types = (array) ($map[$routeName] ?? []);

            if ($types !== []) {
                $service = app(NotificationService::class);

                foreach ($types as $type) {
                    $service->markTypeAsRead($request->user()->id, $type);
                }
            }
        }

        return $next($request);
    }
}

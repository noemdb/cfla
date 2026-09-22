<?php

namespace App\Services;

use App\Models\User;

/**
 * Resuelve el destino de una notificación según el rol del usuario
 * (blueprint/notifications, hallazgo N3): para `lesson_scheduled`, la URL
 * almacenada apunta al monitor de planificación, pero los destinatarios
 * incluyen coordinación, liderazgo y dirección, que tienen sus propios
 * listados de lecciones. Para el resto de notificaciones se respeta la URL
 * almacenada (`url`/`action_url`), con el índice como último recurso.
 */
class NotificationTargetResolver
{
    public function resolveFor(User $user, array $data): string
    {
        // URL almacenada en la notificación (url para las nuevas, action_url
        // para las históricas como SubstituteAssigned/TimetableChanged).
        $stored = $data['url'] ?? $data['action_url'] ?? null;

        // Solo las notificaciones de lección programada redirigen por rol:
        // cada responsable (planificación/coordinación/liderazgo/dirección)
        // tiene su propio listado de lecciones. El resto de notificaciones
        // conserva su enlace almacenado, con el índice como último recurso.
        if (($data['type'] ?? null) === 'lesson_scheduled') {
            if ($user->is_planner) {
                return route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']);
            }

            if ($user->isCoordinacion()) {
                return route('app.coordinacion.lessons');
            }

            if ($user->isLeadership()) {
                return route('app.leadership.lessons');
            }

            if ($user->isDirector()) {
                return route('app.director.lessons');
            }
        }

        return $stored ?? route('app.notifications.index');
    }
}

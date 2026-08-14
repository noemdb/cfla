<?php

namespace App\Services;

use App\Models\User;

/**
 * Resuelve el destino de una notificación según el rol del usuario
 * (blueprint/notifications, hallazgo N3): la URL almacenada en `data` apunta
 * al monitor de planificación (`isPlanner`), pero los destinatarios de
 * `lesson_scheduled` incluyen coordinación, liderazgo y dirección, que tienen
 * sus propios listados de lecciones. El `url` almacenado queda como fallback.
 */
class NotificationTargetResolver
{
    public function resolveFor(User $user, array $data): string
    {
        // Admin / Planner (accessor is_planner ya incluye a los admins):
        // el monitor de planificación es su herramienta canónica.
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

        // Profesor / estudiante / usuario sin rol responsable: el enlace
        // almacenado en la notificación (o el índice, como último recurso).
        return $data['url'] ?? route('app.notifications.index');
    }
}

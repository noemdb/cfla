<?php

namespace App\Services;

use App\Models\User;

/**
 * Resuelve el destino de una notificación según el rol del usuario
 * (blueprint/notifications, hallazgo N3): para `lesson_scheduled`, la URL
 * almacenada apunta al monitor de planificación, pero los destinatarios
 * incluyen coordinación, liderazgo y dirección, que tienen sus propios
 * listados de lecciones; para `activity_created`, la URL almacenada apunta
 * listados de lecciones; para `activity_created`, la URL almacenada apunta
 * a jefatura, pero los planners puros van a su listado de actividades y la
 * coordinación en ámbito al suyo.
 * Para el resto de notificaciones se respeta la URL
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

        // Las notificaciones de actividad creada se almacenan con la URL de
        // jefatura (app.leadership.activities): los planners puros (sin
        // jefatura cruda) no pasan el middleware isLeadership (403), así que
        // van al listado de actividades de planificación, con visión global;
        // la coordinación en ámbito va a su propio listado de actividades.
        // Jefatura y admin conservan la URL almacenada (su scope por áreas).
        if (($data['type'] ?? null) === 'activity_created') {
            $isLeadership = ! empty($user->getAttributes()['is_leadership'])
                || ! empty($user->is_admin);

            if (! $isLeadership && $user->is_planner) {
                return route('app.planning.activities.index');
            }

            if (! $isLeadership && $user->isCoordinacion()) {
                return route('app.coordinacion.activities');
            }
        }

        return $stored ?? route('app.notifications.index');
    }
}

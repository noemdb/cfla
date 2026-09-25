<?php

namespace App\Services;

use App\Models\User;

/**
 * Resuelve el destino de una notificación según el rol del usuario
 * (blueprint/notifications, hallazgo N3).
 *
 * Dos familias de notificación se dirigen por rol, porque cada rol tiene su
 * propio listado y el mismo dato se ve distinto en cada uno:
 *
 * - `lesson_scheduled`: la URL almacenada apunta al monitor de planificación,
 *   pero coordinación, liderazgo y dirección tienen sus propios listados.
 * - `activity_created`: se almacena con URL neutra y cada rol va al suyo
 *   (jefatura con scope por áreas, planificación global, coordinación en
 *   ámbito, dirección y profesorado(read-only)).
 *
 * Para el resto se respeta la URL almacenada (`url`, o `action_url` de las
 * filas históricas), con el índice de notificaciones como último recurso.
 *
 * Orden de prioridad de roles (el primero que el usuario tenga gana):
 * is_admin → is_planner → is_director → is_diagnostic → is_coordinacion →
 * is_leadership → is_profesor → is_student.
 *
 * Los roles se leen del atributo CRUD, no de los accesores: `is_planner`,
 * `is_leadership` e `is_director` tienen accesores que además incluyen
 * `is_admin`, y usarlos rompería el orden (un admin+planner se iría al
 * destino de liderazgo en lugar del de planificación).
 */
class NotificationTargetResolver
{
    /**
     * @var array<int, string>
     */
    public const ROLE_PRIORITY = [
        'is_admin',
        'is_planner',
        'is_director',
        'is_diagnostic',
        'is_coordinacion',
        'is_leadership',
        'is_profesor',
        'is_student',
    ];

    /**
     * Destino por tipo de notificación y rol: [route, parámetros].
     * `null` = ese rol no tiene pantalla propia para ese tipo, así que se
     * sigue al siguiente rol de la lista; si ninguno encaja, se usa la URL
     * almacenada en la notificación.
     *
     * `is_admin` y `is_diagnostic` apuntan al módulo de planificación porque
     * el middleware `IsPlanner` los acepta (admin || planner || diagnostic).
     *
     * @var array<string, array<string, array{0: string, 1: array<string, mixed>}|null>>
     */
    private const DESTINATIONS = [
        'lesson_scheduled' => [
            'is_admin' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_planner' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_director' => ['app.director.lessons', []],
            'is_diagnostic' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_coordinacion' => ['app.coordinacion.lessons', []],
            'is_leadership' => ['app.leadership.lessons', []],
            // El profesorado no tiene un listado de lecciones que aprobar.
            'is_profesor' => null,
            'is_student' => ['student.lms.lessons', []],
        ],
        'activity_created' => [
            'is_admin' => ['app.planning.activities.index', []],
            'is_planner' => ['app.planning.activities.index', []],
            'is_director' => ['app.director.activities', []],
            'is_diagnostic' => ['app.planning.activities.index', []],
            'is_coordinacion' => ['app.coordinacion.activities', []],
            'is_leadership' => ['app.leadership.activities', []],
            'is_profesor' => ['app.profesors.activities.index', []],
            // El alumnado no gestiona actividades.
            'is_student' => null,
        ],
    ];

    public function resolveFor(User $user, array $data): string
    {
        // URL almacenada: `url` en las actuales, `action_url` en las
        // históricas (TimetableChanged/SubstituteAssigned y filas previas).
        $stored = $data['url'] ?? $data['action_url'] ?? null;

        // `type` es la clave canónica; `event_type` la de las históricas.
        $type = $data['type'] ?? $data['event_type'] ?? null;

        if (is_string($type) && isset(self::DESTINATIONS[$type])) {
            foreach (self::ROLE_PRIORITY as $flag) {
                $destination = self::DESTINATIONS[$type][$flag] ?? null;

                if ($destination === null || ! $this->hasRole($user, $flag)) {
                    continue;
                }

                return route($destination[0], $destination[1]);
            }
        }

        return $stored ?? route('app.notifications.index');
    }

    /**
     * Lee el flag crudo del modelo: los accesores de is_planner/is_leadership/
     * is_director suman `is_admin` y falsearían la prioridad.
     */
    private function hasRole(User $user, string $flag): bool
    {
        return (bool) ($user->getAttributes()[$flag] ?? false);
    }
}

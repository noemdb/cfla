<?php

namespace App\Services;

use App\Models\app\Academy\Activity;
use App\Models\User;
use App\Services\Lms\CoordinacionScopeService;
use Illuminate\Support\Collection;

/**
 * Destinatarios de los avisos sobre una actividad (usado por el observer de
 * `Activity` y el de `LmsActivityPublication`).
 *
 * Un aviso de este tipo va a cuatro grupos:
 *   - jefatura del área a la que pertenece la asignatura;
 *   - administración y planificación (alcance global);
 *   - coordinación, solo si la pevaluacion cae en su ámbito;
 *   - el profesor de la pevaluacion (opcional: en la creación de la actividad
 *     es el autor y no tiene sentido avisarle).
 *
 * Los conjuntos se UNEN sin filtros cruzados: exigir un rol "puro" dejaba
 * fuera a los usuarios multi-rol (p. ej. quien es planner y coordinación a la
 * vez no entraba en ningún conjunto y nunca recibía nada). `unique('id')`
 * evita el duplicado y el destino del clic lo decide
 * `NotificationTargetResolver` según el rol de mayor prioridad.
 */
class ActivityAudienceResolver
{
    /**
     * @param  bool  $includeProfessor  incluir al profesor de la pevaluacion
     *                                  (false cuando es el autor del evento)
     * @param  bool  $includeGlobal  incluir administración y planificación
     * @return Collection<int, User>
     */
    public function forActivity(Activity $activity, ?User $actor = null, bool $includeProfessor = true, bool $includeGlobal = true): Collection
    {
        $pevaluacion = $activity->pevaluacion;

        $leaderIds = $activity->areaLeaderIds();
        $leaderUsers = $leaderIds === []
            ? collect()
            : User::whereIn('id', $leaderIds)
                ->where('is_active', 'enable')
                ->where(fn ($q) => $q->where('is_leadership', true)->orWhere('is_admin', true))
                ->get();

        $globalUsers = collect();
        if ($includeGlobal) {
            $globalUsers = User::where('is_active', 'enable')
                ->where(fn ($q) => $q->where('is_admin', true)->orWhere('is_planner', true))
                ->get();
        }

        $coordinacionUsers = collect();
        if ($pevaluacion) {
            $coordinacionUsers = User::where('is_coordinacion', true)
                ->where('is_active', 'enable')
                ->get()
                ->filter(fn (User $u) => app(CoordinacionScopeService::class, ['user' => $u])
                    ->pevaluacionIsInScope($pevaluacion->id));
        }

        $profesorUser = collect();
        if ($includeProfessor && $pevaluacion) {
            $profesor = $pevaluacion->profesor;
            $user = $profesor?->user;

            // Sin usuario linked no hay a quién avisar (profesores dados de
            // alta en la BD sin cuenta): se sigue con el resto.
            if ($user && $user->is_active === 'enable') {
                $profesorUser = collect([$user]);
            }
        }

        return $leaderUsers
            ->concat($globalUsers)
            ->concat($coordinacionUsers)
            ->concat($profesorUser)
            ->unique('id')
            ->reject(fn (User $u) => $this->isExcludedActor($u, $actor))
            ->values();
    }

    /**
     * El actor no se auto-notifica por lo que acaba de hacer, SALVO que
     * además tenga un rol de supervisión: entonces el aviso le sirve en esa
     * capacidad (el mismo usuario entra como profesor a registrar algo, pero
     * como planificador tiene que enterarse de que hay algo nuevo que
     * revisar). Los profesores "puros" sí quedan fuera.
     */
    public function isExcludedActor(User $user, ?User $actor): bool
    {
        if (! $actor || (int) $user->id !== (int) $actor->id) {
            return false;
        }

        return ! $this->hasSupervisoryRole($user);
    }

    /**
     * Roles desde los que se sigue lo ajeno a la acción del propio usuario
     * (los mismos que usa `NotificationTargetResolver` para dirigir el clic).
     * Se leen del atributo CRUD: los accesores de is_planner/is_leadership/
     * is_director suman `is_admin` y falsearían el criterio.
     */
    public function hasSupervisoryRole(User $user): bool
    {
        foreach (['is_admin', 'is_planner', 'is_director', 'is_diagnostic', 'is_coordinacion', 'is_leadership'] as $flag) {
            if (! empty($user->getAttributes()[$flag])) {
                return true;
            }
        }

        return false;
    }
}

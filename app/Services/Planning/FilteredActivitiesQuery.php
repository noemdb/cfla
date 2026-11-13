<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Builder;

/**
 * Consulta compartida entre /app/planning/activities y el wizard de plan de clases.
 *
 * Mantener una sola definición de filtros evita que la página y el wizard
 * devuelvan universos distintos.
 */
class FilteredActivitiesQuery
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function pevaluacions(array $filters): Builder
    {
        $query = Pevaluacion::query()
            ->with([
                'pensum.asignatura',
                'pensum.pestudio',
                'seccion.grado',
                'profesor',
                'lapso',
                'grupoEstable',
            ])
            ->with('activities.lmsPublication')
            ->with('activities.lmsSections')
            ->with('activities.supplement')
            ->withCount([
                'activities',
                'activities as activities_revision_count' => fn ($q) => $q->where('status', 0),
                'activities as activities_approved_count' => fn ($q) => $q->where('status', 1),
                'activities as activities_lessons_count' => fn ($q) => $q->whereHas('lmsPublication'),
                'activities as activities_supplements_count' => fn ($q) => $q->whereHas('supplement'),
            ])
            ->whereHas('pensum.pestudio', fn ($q) => $q->where('planning_module', true))
            ->whereNull('pevaluacions.deleted_at');

        if (! empty($filters['pestudio_id'])) {
            $query->whereHas('pensum.pestudio', fn ($q) => $q->where('id', $filters['pestudio_id']));
        }
        if (! empty($filters['grado_id'])) {
            $query->whereHas('seccion.grado', fn ($q) => $q->where('id', $filters['grado_id']));
        }
        if (! empty($filters['seccion_id'])) {
            $query->where('seccion_id', $filters['seccion_id']);
        }
        if (! empty($filters['lapso_id'])) {
            $query->where('lapso_id', $filters['lapso_id']);
        }
        if (! empty($filters['profesor_id'])) {
            $query->where('profesor_id', $filters['profesor_id']);
        }

        if (! empty($filters['status_activities'])) {
            if ($filters['status_activities'] === 'SI') {
                $query->having('activities_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO') {
                $query->having('activities_count', '=', 0);
            } elseif ($filters['status_activities'] === 'SI_LE') {
                $query->having('activities_lessons_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO_LE') {
                $query->having('activities_lessons_count', '=', 0);
            } elseif ($filters['status_activities'] === 'SI_SUP') {
                $query->having('activities_supplements_count', '>', 0);
            }
        }

        if (! empty($filters['filter_observations'])) {
            $query->whereNotNull('pevaluacions.observations')
                ->where('pevaluacions.observations', '!=', '');
        }

        if (! empty($filters['filter_status'])) {
            if ($filters['filter_status'] === 'pending') {
                $query->whereHas('activities', fn ($q) => $q->where('status', 0));
            } elseif ($filters['filter_status'] === 'approved') {
                $query->has('activities')
                    ->whereDoesntHave('activities', fn ($q) => $q->where('status', 0));
            }
        }

        if (! empty($filters['search'])) {
            $term = trim((string) $filters['search']);
            $query->where(function ($q) use ($term): void {
                $q->whereHas('pensum.asignatura', fn ($q) => $q
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%"))
                    ->orWhereHas('profesor', fn ($q) => $q
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%"))
                    ->orWhereHas('seccion', fn ($q) => $q->where('name', 'like', "%{$term}%"))
                    ->orWhere('pevaluacions.id', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Actividades del universo para una sección y una semana.
     *
     * @param  array<string, mixed>  $filters
     */
    public function activities(
        array $filters,
        int $seccionId,
        ?string $weekStart = null,
        ?string $weekEnd = null
    ): Builder {
        $pevaluacionIds = $this->pevaluacions($filters)
            ->where('seccion_id', $seccionId)
            ->pluck('pevaluacions.id')
            ->all();

        $query = Activity::query()
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'achievements'])
            ->whereIn('pevaluacion_id', $pevaluacionIds);

        if ($weekStart !== null && $weekEnd !== null) {
            $query->whereNotNull('finicial')
                ->where('finicial', '!=', '0000-00-00')
                ->where('finicial', '<=', $weekEnd)
                ->whereRaw(
                    "COALESCE(NULLIF(ffinal, '0000-00-00'), finicial) >= ?",
                    [$weekStart]
                );
        }

        return $query->orderBy('finicial')->orderBy('id');
    }

    /**
     * Búsqueda de contenido del wizard, acotada al universo ya filtrado.
     *
     * @param  array<string, mixed>  $filters
     */
    public function activitySearch(
        array $filters,
        int $seccionId,
        string $term,
        ?string $weekStart = null,
        ?string $weekEnd = null
    ): Builder {
        $query = $this->activities($filters, $seccionId, $weekStart, $weekEnd);
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term): void {
            $q->where('topic', 'like', "%{$term}%")
                ->orWhere('thematic', 'like', "%{$term}%")
                ->orWhere('teaching', 'like', "%{$term}%")
                ->orWhere('learning', 'like', "%{$term}%")
                ->orWhere('observations', 'like', "%{$term}%");
        });
    }

    /**
     * @param  array<int, int|string>  $activityIds
     */
    public function universeHash(array $activityIds): string
    {
        $ids = array_values(array_unique(array_map('intval', $activityIds)));
        sort($ids, SORT_NUMERIC);

        return hash('sha256', implode(',', $ids));
    }
}

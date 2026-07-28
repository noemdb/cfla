<?php

namespace App\Livewire\Planning\Leadership;

use App\Livewire\Planning\Activities\IndexComponent;
use App\Models\app\Academy\Pevaluacion;
use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;

class ActivityOverview extends IndexComponent
{
    protected function getPevaluaciones(array $filters)
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $query = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
        ])
        ->with('activities')
        ->withCount('activities')
        ->whereHas('pensum.pestudio', fn($q) => $q->where('planning_module', true))
        ->whereNull('pevaluacions.deleted_at');

        // Apply leadership scope before filters
        $query = $service->scopePevaluacions($query);

        if (isset($filters['pestudio_id'])) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('id', $filters['pestudio_id']));
        }
        if (isset($filters['grado_id'])) {
            $query->whereHas('seccion.grado', fn($q) => $q->where('id', $filters['grado_id']));
        }
        if (isset($filters['seccion_id'])) {
            $query->where('seccion_id', $filters['seccion_id']);
        }
        if (isset($filters['lapso_id'])) {
            $query->where('lapso_id', $filters['lapso_id']);
        }
        if (isset($filters['profesor_id'])) {
            $query->where('profesor_id', $filters['profesor_id']);
        }
        if (isset($filters['status_activities'])) {
            if ($filters['status_activities'] === 'SI') {
                $query->having('activities_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO') {
                $query->having('activities_count', '=', 0);
            }
        }
        if (!empty($filters['filter_observations'])) {
            $query->whereNotNull('pevaluacions.observations')
                  ->where('pevaluacions.observations', '!=', '');
        }
        if (!empty($filters['filter_revision'])) {
            $query->whereHas('activities', fn($q) => $q->where('status', 0));
        }

        $query->orderBy('created_at', 'desc');

        if ((int) $this->paginate === 9999) {
            $all = $query->get();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $all,
                $all->count(),
                max($all->count(), 1),
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return $query->paginate($this->paginate);
    }

    public function saveComent(...$args)
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $service->assertCanAccessAsignatura(
            $this->activity->pevaluacion->pensum->asignatura_id
        );

        return parent::saveComent(...$args);
    }
}

<?php
namespace App\Models\app\Pescolar\Functions\Pestudio;

use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;

trait ActivitiesTrait {

    public function getActivities($lapso_id=null)
    {
        $activities =
        Activity::select('activities.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')

            ->where('pestudios.id',$this->id)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('grados.deleted_at')

            ->groupby('activities.id');

            $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id',$lapso_id) : $activities ;

            $activities = $activities->get();

        return $activities;
    }

    public function getAvgActivitiesPerPlan($lapso_id = null)
    {
        $avgActivities = Activity::selectRaw('AVG(activity_count) as avg_activities_per_plan')
            ->from(function ($query) use ($lapso_id) {
                $query->selectRaw('pevaluacions.id as pevaluacion_id, COUNT(activities.id) as activity_count')
                    ->from('activities')
                    ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
                    ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
                    ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                    ->where('pestudios.id', $this->id)
                    ->whereNull('pestudios.deleted_at')
                    ->whereNull('seccions.deleted_at')
                    ->whereNull('pevaluacions.deleted_at')
                    ->whereNull('grados.deleted_at')
                    ->when($lapso_id, function ($q) use ($lapso_id) {
                        $q->where('pevaluacions.lapso_id', $lapso_id);
                    })
                    ->groupBy('pevaluacions.id');
            }, 'subquery')
            ->value('avg_activities_per_plan');

        return $avgActivities;
    }

    public function getActiveProfesors($lapso_id = null)
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->where('pestudios.id', $this->id) // Filtrar por el ID del plan de estudio actual
            ->whereNull('pestudios.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('grados.deleted_at')
            ->whereNull('profesors.deleted_at')
            ->groupBy('profesors.id');
        $profesors = ($lapso_id) ? $profesors->where('pevaluacions.lapso_id',$lapso_id) : $profesors ;
        $profesors = $profesors->get();
        return $profesors;
    }

    public function getActiveTeachersCount($lapso_id = null)
    {
        return $this->getActiveProfesors($lapso_id)->count();
    }

    public function getTeachersCount($lapso_id = null)
    {
        return $this->getProfesors($lapso_id)->count();
    }

    public function getPevaluacionsCount($lapso_id = null)
    {
        return $this->getPevaluacions($lapso_id)->count();
    }

    public function getActivitiesCount($lapso_id = null)
    {
        return $this->getActivities($lapso_id)->count();
    }

}

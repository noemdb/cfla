<?php
namespace App\Models\app\Pescolar\Functions\Pestudio;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\DB;

trait EvaluacionTrait {

    public function getEvaluacions($lapso_id=null)
    {
        $evaluacions =
            Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pestudios.id',$this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

        $evaluacions = (!empty($lapso_id)) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions  ;

        $evaluacions = $evaluacions->get();

        return $evaluacions;
    }

    public function getProfesorEvaluacions()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('profesors.status_active','true')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('profesors.id')
            // ->where('estudiants.status_active','true')
            ->get();
        //dd($evaluaciones);
        return $profesors;
    }

}

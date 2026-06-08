<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\DB;

trait Evaluacions
{
    public function getEvaluacionsPensumLapso($pensum_id=null,$lapso_id=null)
    {
        $seccion = $this->seccion; 
        $seccion_id = ($seccion) ? $seccion->id : null ;

        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->orderby('evaluacions.created_at')
            ->groupby('evaluacions.id');

            $evaluacions = ($seccion) ? $evaluacions->where('pevaluacions.seccion_id',$seccion->id) : $evaluacions ;
            $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;
            $evaluacions = ($pensum_id) ? $evaluacions->where('pensums.id',$pensum_id) : $evaluacions ;

            $evaluacions = $evaluacions->get(); //dd($evaluacions);

        return ($evaluacions->isNotEmpty()) ? $evaluacions : collect();
    }

    public function getEvaluacionsPensumLapsoBoletin($pensum_id=null,$lapso_id=null)
    {
        $seccion = $this->seccion;
        $evaluacions = Evaluacion::select('evaluacions.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('boletins', 'evaluacions.id', '=', 'boletins.evaluacion_id')

        ->where('pevaluacions.seccion_id',$seccion->id)
        ->where('boletins.estudiant_id',$this->id)

        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('boletins.deleted_at')
        ->wherenull('pensums.deleted_at');

        $evaluacions = ($pensum_id) ? $evaluacions->where('pensums.id',$pensum_id) : $evaluacions ;
        $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;

        $evaluacions = $evaluacions->get(); //dd($evaluacions);

        return ($evaluacions->isNotEmpty()) ? $evaluacions : collect();
    }

}

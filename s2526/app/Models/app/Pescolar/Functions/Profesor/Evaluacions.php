<?php
namespace App\Models\app\Pescolar\Functions\Profesor;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\DB;

trait Evaluacions {

    public function getActivitiesPestudioLapso($pestudio_id=null,$lapso_id=null)
    {
        $activities = Activity::select('activities.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')

        ->where('pevaluacions.profesor_id',$this->id)

        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('grados.deleted_at')
        ->wherenull('pestudios.deleted_at');

        $activities = ($pestudio_id) ? $activities->where('pestudios.id',$pestudio_id) : $activities ;
        $activities = ($lapso_id) ? $activities->where('pevaluacions.lapso_id',$lapso_id) : $activities ;

        $activities = $activities->get(); //dd($this,$activities,$pestudio_id,$lapso_id);

        return $activities;
    }

    public function getPevaluacionsPestudioLapso($pestudio_id=null,$lapso_id=null)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

        ->where('pevaluacions.profesor_id',$this->id)

        ->wherenull('pensums.deleted_at')
        ->wherenull('pestudios.deleted_at');

        $pevaluacions = ($pestudio_id) ? $pevaluacions->where('pestudios.id',$pestudio_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        $pevaluacions = $pevaluacions->get();

        return $pevaluacions;
    }

    public function getPevaluacionsAreaConocimientoLapso($area_conocimiento_id=null,$lapso_id=null)
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

        ->where('pevaluacions.profesor_id',$this->id)

        ->wherenull('pensums.deleted_at');

        $pevaluacions = ($area_conocimiento_id) ? $pevaluacions->where('area_conocimientos.id',$area_conocimiento_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        $pevaluacions = $pevaluacions->get();

        return $pevaluacions;
    }

    public function getBoletinsPestudioLapso($pestudio_id=null,$lapso_id=null)
    {
        $boletins = Boletin::select('boletins.*')
                ->join('evaluacions','boletins.evaluacion_id','=','evaluacions.id')
                ->join('pevaluacions','evaluacions.pevaluacion_id','=','pevaluacions.id')
                ->join('profesors','pevaluacions.profesor_id','=','profesors.id')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
                ->where('profesors.id',$this->id)
                ->wherenull('pestudios.deleted_at')
                ->wherenull('pensums.deleted_at')
                ->wherenull('profesors.deleted_at')
                ->wherenull('evaluacions.deleted_at')
                ->wherenull('pevaluacions.deleted_at');

        $boletins = ($pestudio_id) ? $boletins->where('pestudios.id',$pestudio_id) : $boletins ;
        $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id',$lapso_id) : $boletins  ;

        $boletins = $boletins->get();
        return $boletins;
    }

    public function getBoletinsAreaConocimientoLapso($area_conocimiento_id=null,$lapso_id=null)
    {
        $boletins = Boletin::select('boletins.*')
                ->join('evaluacions','boletins.evaluacion_id','=','evaluacions.id')
                ->join('pevaluacions','evaluacions.pevaluacion_id','=','pevaluacions.id')
                ->join('profesors','pevaluacions.profesor_id','=','profesors.id')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
                ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
                ->where('profesors.id',$this->id)
                ->wherenull('pensums.deleted_at')
                ->wherenull('profesors.deleted_at')
                ->wherenull('evaluacions.deleted_at')
                ->wherenull('pevaluacions.deleted_at');

        $boletins = ($area_conocimiento_id) ? $boletins->where('area_conocimientos.id',$area_conocimiento_id) : $boletins ;
        $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id',$lapso_id) : $boletins  ;

        $boletins = $boletins->get();
        return $boletins;
    }

    public function getEvaluacions($pestudio_id=null,$lapso_id=null)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
            ->join('profesors','profesors.id','=','pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('profesors.id',$this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('profesors.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('pestudios.deleted_at')
            ;           

        $evaluacions = ($pestudio_id) ? $evaluacions->where('pestudios.id',$pestudio_id) : $evaluacions ;
        $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions  ;

        $evaluacions = $evaluacions->get();

        return $evaluacions;
    }

}

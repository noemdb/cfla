<?php

namespace App\Models\sys\Functions\User;

use App\Models\app\Assistcontrol\AssitAttendance;
use App\Models\app\Learning\Lesson;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EvaluacionFunctions {

    public function getIsLessonAttribute()
    {
        return ($this->evaluacion_lessons->isNotEmpty()) ? true : false;
    }

    public function getEvaluacionLessonsAttribute()
    {
        return Lesson::query()
        ->select('lessons.*','pevaluacions.id as pevaluacion_id')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')        
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        ->join('peducativos', 'peducativos.id', '=', 'area_conocimientos.peducativo_id')
        ->join('users', 'users.id', '=', 'peducativos.manager_id')
        ->where('users.id',$this->id)
        ->groupby('lessons.id')
        ->get();
    }

    public function getPevaluacions($lapso_id=null,$profesor_id=null,$paginate=false)
    {
        $pevaluacions = DB::table('users')
        ->select('pevaluacions.id','pevaluacions.id as pevaluacion_id','pevaluacions.description','profesors.name as profesor_name','profesors.id as profesor_id','asignaturas.name as asignatura_code_name')
        ->selectRaw("CONCAT('[',asignaturas.code_sm,'] ',grados.code,' ',seccions.name,' - ',lapsos.name,' - ',profesors.name) as asignatura_name_full")
        ->join('peducativos', 'users.id', '=', 'peducativos.manager_id')
        ->join('area_conocimientos', 'peducativos.id', '=', 'area_conocimientos.peducativo_id')
        ->join('campo_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('users.id', $this->id)
        ->where('seccions.status_inscription_affects', true)
        ->whereNull('pevaluacions.deleted_at')
        ->orderBy('asignatura_name_full');

        $pevaluacions = ($lapso_id) ? $pevaluacions->where('lapsos.id',$lapso_id) : $pevaluacions ;
        $pevaluacions = ($profesor_id) ? $pevaluacions->where('profesors.id',$profesor_id) : $pevaluacions ;
        $pevaluacions = ($paginate) ? $pevaluacions->paginate($paginate) : $pevaluacions->get() ;

        return $pevaluacions;
    }

    public function list_evaluacion_pevaluacions($lapso_id=null,$profesor_id=null)
    {
        return $this->getPevaluacions($lapso_id,$profesor_id)->pluck('asignatura_name_full','pevaluacion_id');
    }

    public function list_evaluacion_profesors($lapso_id=null,$profesor_id=null)
    {
        return $this->getPevaluacions($lapso_id,$profesor_id)->pluck('profesor_name','profesor_id');
    }

}
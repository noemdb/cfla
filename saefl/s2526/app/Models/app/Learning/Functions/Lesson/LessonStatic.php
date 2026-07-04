<?php
namespace App\Models\app\Learning\Functions\Lesson;

use App\Models\app\Learning\Lesson;
use Illuminate\Support\Collection;

trait LessonStatic
{
    public static function getLessonsByStatus($status): Collection
    {
        return Lesson::where('status', $status)->get();
    }

    public static function getLessonsByActivityType($activity_type): Collection
    {
        return Lesson::where('activity_type', $activity_type)->get();
    }

    public static function getLessonsByLevel($level): Collection
    {
        return Lesson::where('level', $level)->get();
    }
    
    public static function searchLessons($search_term): Collection
    {
        return Lesson::where('title', 'like', "%$search_term%")->orWhere('description', 'like', "%$search_term%")->get();
    }

    public static function getForProfesorId($profesor_id): Collection
    {
        return Lesson::query()
        ->select('lessons.*')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('profesors.id', $profesor_id)
        ->get();
    }


    public static function getForLeaderId($leader_id): Collection
    {
        return Lesson::query()
        ->select('lessons.*')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        ->where('area_conocimientos.leader_id',$leader_id)
        ->groupBy('lessons.id')
        ->get();
    }

    public static function getForManagerId($manager_id): Collection
    {
        return Lesson::query()
        ->select('lessons.*')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
        ->where('pestudios.manager_id',$manager_id)
        ->get();
    }

    public static function getForPestudioId($pestudio_id): Collection
    {
        return Lesson::query()
        ->select('lessons.*')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
        ->where('pestudios.id',$pestudio_id)
        ->get();
    }
    
    
}

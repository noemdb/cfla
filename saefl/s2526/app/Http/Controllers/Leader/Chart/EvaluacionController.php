<?php

namespace App\Http\Controllers\Leader\Chart;

use App\Http\Controllers\Controller;
use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_leader']);
    }

    public function actividades(Request $request)
    {
        $user = User::find(Auth::id());
        $lapso_id = (!empty($request->range)) ? $request->range : null;
        $lapso = ($lapso_id) ? Lapso::Where('id',$lapso_id)->first() : null; //dd($lapso);

        $pescolar = Pescolar::OrderBy('created_at','DESC')->first();

        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial ;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal ;

        $data = Evaluacion::select('evaluacions.id',DB::raw('count(evaluacions.id) as value'),
            DB::raw('DAY(evaluacions.fecha) as day'),
            DB::raw("DATE_FORMAT(evaluacions.fecha, '%m-%d') as date"))
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.leader_id',$user->id)
            // ->where('pestudios.status_active','true')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('date')
            ->orderBy('evaluacions.fecha', 'asc');

        if ($lapso_id) {
            $data = $data->Where('pevaluacions.lapso_id',  $lapso_id)->wheredate('evaluacions.fecha', '>=', $finicial)->wheredate('evaluacions.fecha', '<=', $ffinal);
        }

        $data = $data->get(); //dd();

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('value','date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d', $date);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        // dd($labels, $label_month, $values,$finicial,$ffinal);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Núm. Evaluaciones",
                    "backgroundColor"=>"rgba(100, 20, 150,0.2)",
                    "borderColor"=>"rgba(100, 20, 150,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function lessons_diaries(Request $request)
    {
        $user = User::find(Auth::id());
        $lapso_id = (!empty($request->range)) ? $request->range : null;
        $lapso = ($lapso_id) ? Lapso::Where('id',$lapso_id)->first() : null; //dd($lapso);

        $pescolar = Pescolar::OrderBy('created_at','DESC')->first();

        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial ;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal ;

        $data = Lesson::select('lessons.id',DB::raw('count(lessons.id) as value'),
            DB::raw('DAY(lessons.finished) as day'),
            DB::raw("DATE_FORMAT(lessons.finished, '%m-%d') as date"))
            ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.leader_id',$user->id)
            // ->where('pestudios.status_active','true')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('date')
            ->orderBy('lessons.finished', 'asc');

        if ($lapso_id) {
            $data = $data->Where('pevaluacions.lapso_id',  $lapso_id)->wheredate('lessons.finished', '>=', $finicial)->wheredate('lessons.finished', '<=', $ffinal);
        }

        $data = $data->get(); //dd();

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('value','date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d', $date);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Núm. Lecciones",
                    "backgroundColor"=>"rgba(20, 210, 170,0.2)",
                    "borderColor"=>"rgba(20, 210, 170,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}

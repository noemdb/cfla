<?php

namespace App\Http\Controllers\Evaluacion\Chart;

use App\Http\Controllers\Controller;
use App\Models\app\Learning\Lesson;
use Illuminate\Http\Request;

use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;

class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'has_any_role:control,evaluacion,planning']);
    }

    public function actividades(Request $request)
    {
        $user = User::find(Auth::id());
        $lapso_id = (!empty($request->range)) ? $request->range : null;
        $lapso = Lapso::Where('id', $lapso_id)->first();

        $pescolar = Pescolar::OrderBy('created_at', 'DESC')->first();

        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal;

        $data = Evaluacion::select(
            'evaluacions.id',
            DB::raw('count(evaluacions.id) as value'),
            DB::raw('DAY(evaluacions.fecha) as day'),
            DB::raw("DATE_FORMAT(evaluacions.fecha, '%m-%d') as date")
        )
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.manager_id', $user->id)
            ->where('pestudios.status_active', 'true')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('date')
            ->orderBy('evaluacions.fecha', 'asc');

        if ($lapso_id) {
            $data = $data->Where('pevaluacions.lapso_id',  $lapso_id)->wheredate('evaluacions.fecha', '>=', $finicial)->wheredate('evaluacions.fecha', '<=', $ffinal);
        }

        $data = $data->get();

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('value', 'date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d', $date);
            $str_date = $dateObj->format('j') . ' ' . ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v, 2);
        }
        //FIN nombre de los meses en español

        // dd($labels, $label_month, $values,$finicial,$ffinal);

        $ChartDataSQL = [
            'labels' => $label_month,
            'datasets' => [
                [
                    "label" => "Núm. Evaluaciones",
                    "backgroundColor" => "rgba(100, 20, 150,0.2)",
                    "borderColor" => "rgba(100, 20, 150,1)",
                    "borderWidth" => 2,
                    "data" => $values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function lessons(Request $request)
    {
        $user = User::find(Auth::id());
        $lapso_id = (!empty($request->range)) ? $request->range : null;
        $lapso = Lapso::Where('id', $lapso_id)->first();

        $pescolar = Pescolar::OrderBy('created_at', 'DESC')->first();

        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal;

        $data = Lesson::select(
            'lessons.id',
            DB::raw('count(lessons.id) as value'),
            DB::raw('DAY(lessons.created_at) as day'),
            DB::raw("DATE_FORMAT(lessons.created_at, '%m-%d') as date")
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.manager_id', $user->id)
            ->where('pestudios.status_active', 'true')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')

            ->groupby('date')
            ->orderBy('lessons.created_at', 'asc');

        if ($lapso_id) {
            $data = $data->Where('pevaluacions.lapso_id',  $lapso_id)->wheredate('lessons.created_at', '>=', $finicial)->wheredate('lessons.created_at', '<=', $ffinal);
        }

        $data = $data->get(); //dd($data);

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('value', 'date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d', $date);
            $str_date = $dateObj->format('j') . ' ' . ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v, 2);
        }
        //FIN nombre de los meses en español

        $ChartDataSQL = [
            'labels' => $label_month,
            'datasets' => [
                [
                    "label" => "Núm. Lecciones",
                    "backgroundColor" => "rgba(150, 10, 15,0.2)",
                    "borderColor" => "rgba(150, 10, 15,1)",
                    "borderWidth" => 2,
                    "data" => $values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}

<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;


use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Session\Session;

class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // $this->middleware(['is_planning']);
    }

    public function actividades(Request $request)
    {
        // $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $lapso_id = (!empty($request->range)) ? $request->range : null;
        $lapso = Lapso::Where('id', $lapso_id)->first();

        $pescolar = Pescolar::OrderBy('created_at', 'DESC')->first();

        // $finicial = $pescolar->finicial;
        // $ffinal = $pescolar->ffinal;
        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal;

        $data = Evaluacion::select(
            'evaluacions.id',
            DB::raw('count(evaluacions.id) as value'),
            DB::raw('DAY(evaluacions.fecha) as day'),
            DB::raw("DATE_FORMAT(evaluacions.fecha, '%m-%d') as date")
        )
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('date')
            ->orderBy('evaluacions.fecha', 'asc');
        // ->orderBy('value', 'asc');

        if ($lapso_id) {
            $data = $data->Where('pevaluacions.lapso_id',  $lapso_id)->wheredate('evaluacions.fecha', '>=', $finicial)->wheredate('evaluacions.fecha', '<=', $ffinal);
        }

        $data = $data->get();

        // dd($lapso_id,$data,$finicial,$ffinal);

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
}

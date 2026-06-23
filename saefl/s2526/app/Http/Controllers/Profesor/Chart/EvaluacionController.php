<?php

namespace App\Http\Controllers\Profesor\Chart;

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
        $this->middleware(['auth','is_profesor']);
    }

    public function actividades(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $lapso = Lapso::Where('id',$range)->first(); //dd($range,$lapso);
        $profesor = Profesor::where('user_id',Auth::user()->id)->first(); //dd($profesor);

        $pescolar = Pescolar::OrderBy('created_at','DESC')->first();

        // $finicial = $pescolar->finicial;
        // $ffinal = $pescolar->ffinal;
        $finicial = (empty($lapso->finicial)) ? '0000-00-00' : $lapso->finicial ;
        $ffinal = (empty($lapso->ffinal)) ? '0000-00-00' : $lapso->ffinal ;

        $data = Evaluacion::select(DB::raw('count(evaluacions.id) as value'),
            DB::raw('DAY(evaluacions.fecha) as day'),
            DB::raw("DATE_FORMAT(evaluacions.fecha, '%m-%d') as date"))
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->Where('evaluacions.fecha', '>=', $finicial)
            ->Where('evaluacions.fecha', '<=', $ffinal)
            ->Where('pevaluacions.profesor_id', $profesor->id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('date')
            ->orderBy('date', 'asc')
            ->get();
        //dd($finicial,$ffinal,$data);

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('date');
        foreach ($labels as $key => $value) {
            // $dateObj   = Date::createFromFormat('!m', $value);
            $dateObj   = Date::createFromFormat('m-d', $value);
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
                    "backgroundColor"=>"rgba(200, 150, 100,0.2)",
                    "borderColor"=>"rgba(200, 150, 100,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

}

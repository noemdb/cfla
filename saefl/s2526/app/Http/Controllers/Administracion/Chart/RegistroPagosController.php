<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;


use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Session\Session;

class RegistroPagosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function activitiesXMonth(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $label_month = Array();

        $pescolar = Pescolar::OrderBy('created_at','DESC')->first();
        $date_init = Carbon::createFromFormat('Y-m-d',$pescolar->finicial);

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = $date_init->clone()->addMonths($range)->firstOfMonth();
            $ffinal = $date_init->clone()->addMonths($range)->endOfMonth();
        }

        $data = RegistroPago::select('registro_pagos.id',DB::raw('count(registro_pagos.id) as value'),
            DB::raw('DAY(registro_pagos.created_at) as day'),
            DB::raw("DATE_FORMAT(registro_pagos.created_at, '%m-%d') as date"))
            ->groupby('date')
            ->orderBy('registro_pagos.created_at', 'asc');

        $data = ($finicial) ? $data->whereDate('registro_pagos.created_at','>=',$finicial) : $data ;
        $ffinal = ($finicial) ? $data->whereDate('registro_pagos.created_at','<=',$ffinal) : $data ;

        $data = $data->get(); //dd($data);

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
                    "label"=>"Núm. de Registros",
                    "backgroundColor"=>"rgba(90, 180, 32,0.2)",
                    "borderColor"=>"rgba(90, 180, 32,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }


    public function actividades(Request $request)
    {
        $lapso_id = (!empty($request->range)) ? $request->range:null;
        $lapso = Lapso::Where('id',$lapso_id)->first(); //dd($lapso);
        $finicial = ($lapso) ? $lapso->finicial : null;
        $ffinal = ($lapso) ? $lapso->ffinal : null; //dd($finicial,$ffinal);


        $data = RegistroPago::select('registro_pagos.id',DB::raw('count(registro_pagos.id) as value'),
            DB::raw('DAY(registro_pagos.created_at) as day'),
            DB::raw("DATE_FORMAT(registro_pagos.created_at, '%m-%d') as date"))
            ->groupby('date')
            ->orderBy('registro_pagos.created_at', 'asc');

        $data = ($finicial) ? $data->whereDate('registro_pagos.created_at','>=',$finicial) : $data ;
        $ffinal = ($finicial) ? $data->whereDate('registro_pagos.created_at','<=',$ffinal) : $data ;

        $data = $data->get(); //dd($data);

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
                    "label"=>"Núm. de Registros",
                    "backgroundColor"=>"rgba(52, 101, 164,0.2)",
                    "borderColor"=>"rgba(52, 101, 164,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}

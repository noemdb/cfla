<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Helpers
use Jenssegers\Date\Date;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// Modelos adicionadas
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar;

class InscripcionController extends Controller
{
    public function index()
    {
        return view('administracion.inscripcion.chart');
    }

    public function time(Request $request)
    {
        // $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $lapso_id = (!empty($request->range)) ? $request->range:null;
        $lapso = Lapso::Where('id',$lapso_id)->first();

        $pescolar = Pescolar::find(1); //dd($lapso,$pescolar);

        $finicial = (empty($lapso->finicial)) ? $pescolar->finicial : $lapso->finicial ;
        $ffinal = (empty($lapso->ffinal)) ? $pescolar->ffinal : $lapso->ffinal ; //dd($finicial,$ffinal);

        $data = Inscripcion::select('inscripcions.id',DB::raw('count(inscripcions.id) as value'),
            DB::raw('DAY(inscripcions.created_at) as day'),
            DB::raw("DATE_FORMAT(inscripcions.created_at, '%m-%d') as date"))
            ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->where('seccions.status_active','true')
            ->groupby('date')
            ->orderBy('inscripcions.created_at', 'asc');

        if ($lapso_id) {
            $data = $data->wheredate('inscripcions.created_at', '>=', $finicial)->wheredate('inscripcions.created_at', '<=', $ffinal);
        }

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
                    "label"=>"Cant. Inscripciones",
                    "backgroundColor"=>"rgba(100, 20, 150,0.2)",
                    "borderColor"=>"rgba(100, 20, 150,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ]; //dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }


    public function gender(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $fecha = Carbon::now();
        }else{
            $fecha = Carbon::now()->subDays($range);
        }

        $inscripcions = Inscripcion::select('estudiants.gender',DB::raw('count(estudiants.gender) as gender_count'))
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('estudiants.gender')
            ->orderBy('gender_count', 'desc')
            ->get();

        $labels = $inscripcions->pluck('gender');
        $values = $inscripcions->pluck('gender_count');
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Género M/F",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function genderxplan(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        $inscripcions = Inscripcion::getPECodeID($limit); // return code,id,value
        // dd($inscripcions);

        $labels = $inscripcions->pluck('code');
        $ids = $inscripcions->pluck('id');

        // dd($labels,$ids);

        $inc_masculinos = Inscripcion::getCountGenderTotal($ids,'Masculino');

        $inc_femeninos = Inscripcion::getCountGenderTotal($ids,'Femenino');

        // $inc_total = Inscripcion::getCountGenderTotal($ids,'');

        // dd($inc_masculinos,$inc_femeninos,$inc_total );

        unset($ChartDataSQL);
        $ChartDataSQL = [
			'labels'=>$labels,
			'datasets'=>[
				[
                    "label"=>"Masculinos",
                    "backgroundColor"=>"rgba(0,123,255,1)",
	                "borderColor"=>"rgba(0,123,255,1)",
                    "borderWidth"=>1,
	                "data"=>$inc_masculinos
                ],
                [
                    "label"=>"Femeninos",
	                "backgroundColor"=>"rgba(232,62,140,1)",
	                "borderColor"=>"rgba(232,62,140,1)",
                    "borderWidth"=>1,
	                "data"=>$inc_femeninos
                ],
                // [
	            //     "label"=>"Total",
	            //     "backgroundColor"=>"rgba(0,64,0,1)",
	            //     "borderColor"=>"rgba(0,64,0,1)",
                //     "borderWidth"=>1,
	            //     "data"=>$inc_total
                // ]
            ]
        ];

        // dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }

    public function genderxgrado(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        $inscripcions = Inscripcion::getGRNameID($limit); // return code,id,value

        // dd($inscripcions);

        $labels = $inscripcions->pluck('name');
        $ids = $inscripcions->pluck('id');

        // dd($labels,$ids);

        $inc_masculinos = Inscripcion::getCountGRTotal($ids,'Masculino');

        $inc_femeninos = Inscripcion::getCountGRTotal($ids,'Femenino');

        // $inc_total = Inscripcion::getCountGRTotal($ids,'');

        // dd($inc_masculinos,$inc_femeninos,$inc_total );

        unset($ChartDataSQL);
		$ChartDataSQL = [
			'labels'=>$labels,
			'datasets'=>[
				[
                    "label"=>"Masculinos",
                    "backgroundColor"=>"rgba(0,123,255,1)",
	                "borderColor"=>"rgba(0,123,255,1)",
                    "borderWidth"=>1,
	                "data"=>$inc_masculinos
                ],
                [
                    "label"=>"Femeninos",
	                "backgroundColor"=>"rgba(232,62,140,1)",
	                "borderColor"=>"rgba(232,62,140,1)",
                    "borderWidth"=>1,
	                "data"=>$inc_femeninos
                ],
                // [
	            //     "label"=>"Total",
	            //     "backgroundColor"=>"rgba(0,64,0,1)",
	            //     "borderColor"=>"rgba(0,64,0,1)",
                //     "borderWidth"=>1,
	            //     "data"=>$inc_total
                // ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}

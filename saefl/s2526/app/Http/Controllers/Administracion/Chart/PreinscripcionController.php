<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Preinscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PreinscripcionController extends Controller
{
    public function index()
    {
        return view('administracion.preinscripcions.chart');
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

        $preinscripcions = Preinscripcion::select('estudiants.gender',DB::raw('count(estudiants.gender) as gender_count'))
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->where('estudiants.status_active','true')
            ->groupby('estudiants.gender')
            ->orderBy('gender_count', 'desc')
            ->get();

        $labels = $preinscripcions->pluck('gender');
        $values = $preinscripcions->pluck('gender_count');
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

        $inscripcions = Preinscripcion::getPECodeID($limit); // return code,id,value

        $labels = $inscripcions->pluck('code');
        $ids = $inscripcions->pluck('id');


        $inc_masculinos = Preinscripcion::getCountGenderTotal($ids,'Masculino');

        $inc_femeninos = Preinscripcion::getCountGenderTotal($ids,'Femenino');

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

    public function genderxgrado(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        $preinscripcions = Preinscripcion::getGRNameID($limit); // return code,id,value

        // dd($inscripcions);

        $labels = $preinscripcions->pluck('name');
        $ids = $preinscripcions->pluck('id');

        // dd($labels,$ids);

        $inc_masculinos = Preinscripcion::getCountGRTotal($ids,'Masculino');

        $inc_femeninos = Preinscripcion::getCountGRTotal($ids,'Femenino');

        // $inc_total = Preinscripcion::getCountGRTotal($ids,'');

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

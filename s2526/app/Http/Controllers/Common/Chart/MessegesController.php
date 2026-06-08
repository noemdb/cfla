<?php

namespace App\Http\Controllers\Common\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos adicionadas
use App\User;
use App\Models\sys\Messege;

class MessegesController extends Controller
{
    public function index()
    {        
        return view('admin.messeges.charts');
    }

	public function MessegesMonth(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';

        if ($range=='Todos') {
            $finicial = Carbon::now()->SubYear(10);
            $ffinal = Carbon::now()->AddYear(10);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $usersmonth = Messege::select(DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('month')
            ->orderBy('month', 'asc')
            ->get();

        //dd($usersmonth);

        //INI nombre de los meses en español
        $labels = $usersmonth->pluck('month');
        $label_month = array();
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('!m', $value);
            $label_month[] = ucfirst($dateObj->format('F'));
        }
        $values = $usersmonth->pluck('value');
        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Mensajes Registrados",
                    "backgroundColor"=>"rgba(192, 57, 43,0.2)",
                    "borderColor"=>"rgba(192, 57, 43,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function MessegesUsers(Request $request)
	{

		$range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

		if($range=='Todos'){
			$finicial = Carbon::now()->SubYear(10);
			$ffinal = Carbon::now()->AddYear(10);
		}else{
			$finicial = Carbon::now()->subMonth($range);
			$ffinal = Carbon::now();
		}		

		$usersmesseges = Messege::getUserMesseges($finicial,$ffinal,$limit); // return username,user_id,value

        $labels = $usersmesseges->pluck('username');
		$users_id = $usersmesseges->pluck('user_id');

        $messeges_enviado = Messege::getCountTotal($users_id,$finicial,$ffinal,'Enviado');
        $messeges_entregado = Messege::getCountTotal($users_id,$finicial,$ffinal,'Entregado');
        $messeges_asignadas = Messege::getCountTotal($users_id,$finicial,$ffinal,'');

        // dd($messeges_enviado,$messeges_entregado,$messeges_asignadas);

		unset($ChartDataSQL);
		$ChartDataSQL = [
			'labels'=>$labels,
			'datasets'=>[
				[
	                "label"=>"Enviados",
	                "backgroundColor"=>"rgba(255,105,84,1)",
	                "borderColor"=>"rgba(255,105,84,1)",
                    "borderWidth"=>1,
	                "data"=>$messeges_enviado
                ],
                [
	                "label"=>"Entregados",
	                "backgroundColor"=>"rgba(100,166,90,1)",
	                "borderColor"=>"rgba(100,166,90,1)",
                    "borderWidth"=>1,
	                "data"=>$messeges_entregado
                ],
                [
	                "label"=>"Total",
	                "backgroundColor"=>"rgba(0,192,239,1)",
	                "borderColor"=>"rgba(0,192,239,1)",
                    "borderWidth"=>1,
	                "data"=>$messeges_asignadas
                ]
            ]
        ];

		// dd($tasks);

		return json_encode($ChartDataSQL);
	}

    public function MessegesTypes(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $finicial = Carbon::now()->SubYear(1000);
            $ffinal = Carbon::now()->AddYear(1000);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }       

        $rols = Messege::select('tipo', DB::raw('count(tipo) as value'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('tipo')
            ->orderBy('tipo', 'asc')
            ->get()
            ->take($limit);

        $labels = $rols->pluck('tipo');
        $values = $rols->pluck('value');

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de Registros",
                    "backgroundColor"=>"rgba(80,187,205,0.2)",
                    "borderColor"=>"rgba(80,187,205,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}

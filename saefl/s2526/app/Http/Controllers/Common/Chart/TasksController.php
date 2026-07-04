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
use App\Models\common\Task;

class TasksController extends Controller
{

    public function index()
    {        
        return view('admin.tasks.charts');
    }

	public function TasksMonth(Request $request)
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

        $usersmonth = Task::select(DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
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
                    "label"=>"Tareas Registrados",
                    "backgroundColor"=>"rgba(192, 57, 43,0.2)",
                    "borderColor"=>"rgba(192, 57, 43,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function TasksUsers(Request $request)
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

		$userstasks = Task::getUserTasks($finicial,$ffinal,$limit); // return username,user_id,value

		// dd($userstasks);

        $labels = $userstasks->pluck('username');
		$users_id = $userstasks->pluck('user_id');

        // dd($userstasks->toarray(),$labels , $users_id);

        $tasks_iniciadas = Task::getCountTotal($users_id,$finicial,$ffinal,'INICIADA');
        $tasks_finalizadas = Task::getCountTotal($users_id,$finicial,$ffinal,'FINALIZADA');
        $tasks_reprogramadas = Task::getCountTotal($users_id,$finicial,$ffinal,'REPROGRAMADA');
        $tasks_asignadas = Task::getCountTotal($users_id,$finicial,$ffinal,'');

		// dd($tasks_iniciadas,$tasks_finalizadas,$tasks_asignadas);

		unset($ChartDataSQL);
		$ChartDataSQL = [
			'labels'=>$labels,
			'datasets'=>[
				[
	                "label"=>"Iniciadas",
	                "backgroundColor"=>"rgba(245,105,84,1)",
	                "borderColor"=>"rgba(245,105,84,1)",
                    "borderWidth"=>1,
	                "data"=>$tasks_iniciadas
                ],
                [
	                "label"=>"Finalizadas",
	                "backgroundColor"=>"rgba(0,166,90,1)",
	                "borderColor"=>"rgba(0,166,90,1)",
                    "borderWidth"=>1,
	                "data"=>$tasks_finalizadas
                ],
                [
	                "label"=>"Reprogramadas",
	                "backgroundColor"=>"rgba(0,50,100,1)",
	                "borderColor"=>"rgba(0,50,100,1)",
                    "borderWidth"=>1,
	                "data"=>$tasks_reprogramadas
                ],
                [
	                "label"=>"Asignadas",
	                "backgroundColor"=>"rgba(0,192,239,1)",
	                "borderColor"=>"rgba(0,192,239,1)",
                    "borderWidth"=>1,
	                "data"=>$tasks_asignadas
                ]
            ]
        ];

		// dd($tasks);

		return json_encode($ChartDataSQL);
	}

    public function TasksTypes(Request $request)
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

        $tipos = Task::select('tipo', DB::raw('count(tipo) as value'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('tipo')
            ->orderBy('tipo', 'asc')
            ->get()
            ->take($limit);

        $labels = $tipos->pluck('tipo');
        $values = $tipos->pluck('value');

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de Registros",
                    "backgroundColor"=>"rgba(151,187,205,0.2)",
                    "borderColor"=>"rgba(151,187,205,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

}

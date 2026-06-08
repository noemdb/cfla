<?php

namespace App\Http\Controllers\Admin\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos adicionadas
use App\User;
use App\Models\sys\Logdb;

class LogdbsController extends Controller
{
    public function index()
    {
        return view('admin.logdbs.charts');
    }

	public function LogdbsMonth(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $usersmonth = Logdb::select(DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('month')
            ->orderBy('month', 'asc')
            ->get();

        // dd($range,$usersmonth);

        //INI nombre de los meses en español
        $labels = $usersmonth->pluck('month');
        $label_month = array();
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('!m', $value);
            $label_month[] = ucfirst($dateObj->format('F'));
        }
        $values = $usersmonth->pluck('value');

        $bGcolors = null;
        $bcolors = null;
        for ($i=0; $i < count($labels) ; $i++) {
            $c1 = rand(0,255); $c2 = rand(0,255); $c3 = rand(0,255);
            $bGcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 0.5)';
            $bcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 1)';
        }

        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Consultas BD Registradas",
                    "backgroundColor"=>$bGcolors,
                    "borderColor"=>$bGcolors,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function LogdbsUsers(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $finicial = Carbon::now()->subYear(1000);
            $ffinal = Carbon::now()->addYear(1000);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        $loginouts = Logdb::select('users.username', DB::raw('count(users.username) as value'))
            ->join('users', 'users.id', '=', 'logdbs.user_id')
            ->Where('logdbs.created_at', '>=', $finicial)
            ->Where('logdbs.created_at', '<=', $ffinal)
            ->where('users.id','<>',1)
            ->groupby('logdbs.user_id')
            ->orderBy('value', 'desc')
            ->get()
            ->take($limit);

        $labels = $loginouts->pluck('username');
        $values = $loginouts->pluck('value');

        $bGcolors = null;
        $bcolors = null;
        for ($i=0; $i < count($labels) ; $i++) {
            $c1 = rand(0,255); $c2 = rand(0,255); $c3 = rand(0,255);
            $bGcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 0.5)';
            $bcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de consultas",
                    "backgroundColor"=>$bGcolors,
                    "borderColor"=>$bcolors,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function LogdbsActions(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $finicial = Carbon::now()->subYear(1000);
            $ffinal = Carbon::now()->addYear(1000);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        $rols = Logdb::select('action', DB::raw('count(action) as value'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('action')
            ->orderBy('action', 'asc')
            ->get()
            ->take($limit);

        $labels = $rols->pluck('action');
        $values = $rols->pluck('value');

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de Actions",
                    "backgroundColor"=>"rgba(80,187,205,0.2)",
                    "borderColor"=>"rgba(80,187,205,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function LoginoutsRols(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $finicial = Carbon::now()->subYear(1000);
            $ffinal = Carbon::now()->addYear(1000);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        $logdbs = Logdb::select('users.username','rols.rol as rol', DB::raw('count(users.username) as value'))
            ->join('users', 'users.id', '=', 'logdbs.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->WhereDate('logdbs.created_at', '>=', $finicial)
            ->WhereDate('logdbs.created_at', '<=', $ffinal)
            ->where('users.id','<>',1)
            ->groupby('rols.rol')
            // ->orderBy('logdbs.user_id', 'asc')
            ->orderBy('value', 'desc')
            ->get()
            ->take($limit);

        $labels = $logdbs->pluck('rol');
        $values = $logdbs->pluck('value');

        $bGcolors = null;
        $bcolors = null;
        for ($i=0; $i < count($labels) ; $i++) {
            $c1 = rand(0,255); $c2 = rand(0,255); $c3 = rand(0,255);
            $bGcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 0.5)';
            $bcolors[] = 'rgba('.$c1.', '.$c2.', '.$c3.', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de accesos al sistema",
                    "backgroundColor"=>$bGcolors,
                    "borderColor"=>$bcolors,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

}

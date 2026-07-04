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
use App\Models\sys\Loginout;

class LoginoutsController extends Controller
{
    public function index()
    {
        return view('admin.loginouts.charts');
    }

	public function LoginoutsMonth(Request $request)
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

        $usersmonth = Loginout::select(DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('month')
            ->orderBy('month', 'desc')
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
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }
        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Cantidad de accesos al sistema",
                    "backgroundColor"=>$colors,
                    "borderColor"=>$colors,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function LoginoutsTypes(Request $request)
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

        $rols = Loginout::select('action', DB::raw('count(action) as value'))
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

        $loginouts = Loginout::select('users.username','rols.rol as rol', DB::raw('count(users.username) as value'))
            ->join('users', 'users.id', '=', 'loginouts.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->WhereDate('loginouts.created_at', '>=', $finicial)
            ->WhereDate('loginouts.created_at', '<=', $ffinal)
            ->where('loginouts.action','LogSuccessfulLogin')
            ->where('users.id','<>',1)
            ->groupby('rols.rol')
            // ->orderBy('loginouts.user_id', 'asc')
            ->orderBy('value', 'desc')
            ->get()
            ->take($limit);

        $labels = $loginouts->pluck('rol');
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

    public function LoginoutsUsers(Request $request)
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

        $loginouts = Loginout::select('users.username', DB::raw('count(users.username) as value'))
            ->join('users', 'users.id', '=', 'loginouts.user_id')
            ->WhereDate('loginouts.created_at', '>=', $finicial)
            ->WhereDate('loginouts.created_at', '<=', $ffinal)
            ->where('loginouts.action','LogSuccessfulLogin')
            ->where('users.id','<>',1)
            ->groupby('loginouts.user_id')
            // ->orderBy('loginouts.user_id', 'asc')
            ->orderBy('value', 'desc')
            ->get()
            ->take($limit);

        $labels = $loginouts->pluck('username');
        $values = $loginouts->pluck('value');

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Cantidad de accesos al sistema",
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

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
use App\Models\sys\Profile;


class ProfilesController extends Controller
{

    public function index()
    {
        
        return view('admin.profiles.charts');
    }

	public function ProfilesMonth(Request $request)
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

        $usersmonth = Profile::select(DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->groupby('month')
            ->orderBy('month', 'asc')
            ->get();

        //dd($usersmonth);

        //INI nombre de los meses en español
        $labels = $usersmonth->pluck('month');
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
                    "label"=>"Perfiles Registrados",
                    "backgroundColor"=>"rgba(192, 57, 43,0.2)",
                    "borderColor"=>"rgba(192, 57, 43,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function ProfilesUsers(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $fecha = Carbon::now();
        }else{
            $fecha = Carbon::now()->subDays($range);
        }

        
        $users_wo_profile = User::select('users.id')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
                ->whereNull('profiles.user_id')
                // ->Where('profiles.created_at', '<=', $fecha)
                // ->whereNotNull('profiles.deleted_at')
                ->orderby('users.username','asc')
                ->count();
                // ->pluck('username', 'id');

        $tot_user = User::count();

        $users_w_profile = $tot_user - $users_wo_profile;

        // dd($users_wo_profile,$tot_user);

        $labels =['Usuarios con Perfíl', 'Usuarios sin Perfíl'];
        $values = [$users_w_profile, $users_wo_profile];
        for ($i=0; $i < count($labels) ; $i++) { 
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        // dd($labels , $values, $colors);

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Usuarios Act/Des",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        // dd($tasks);

        return json_encode($ChartDataSQL);
    }

}

<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    public $user,$director,$list_comment;

    public function __construct()
    {
        $this->middleware(['auth','is_academico', function ($request, $next) {
            $this->director = Autoridad::where('user_id',Auth::user()->id)->first();
            $this->user = User::find(Auth::id());
            $this->list_comment = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function home()
    {
        $director = $this->director; //dd($director);
        $user = $this->user; //dd($director);
        $list_comment = $this->list_comment; //dd($list_comment);
        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $representants = Representant::representantFormaly();
        $peducativos = Peducativo::Orderby('id','asc')->where('status_active','true')->where('show_quantitative_indicators','true')->get(); 
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get(); //dd($pestudios);
        $lapso_active = Lapso::current();
        $now = Carbon::now()->format('Y-m-d');
        $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();
        $retiros = DB::table('estudiants')
            ->select('estudiants.*')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->join('registro_pagos', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->GroupBy('estudiants.id')
            ->get();
        $profesors = Profesor::asignado('true')->get();

        $list_comment = Autoridad::COLUMN_COMMENTS;
        $indicadores = Pestudio::getIndicadores(); //dd($indicadores);
        $lapsos = Lapso::all();
        $grados = Grado::all();
        $area_conocimientos = AreaConocimiento::all();
        $profesors = Profesor::asignado('true')->get();

        $compact = ['user','director','estudiants','representants','peducativos','pestudios','lapsos','lapso_active','list_comment','plan_beneficos','profesors','retiros','indicadores','grados','area_conocimientos','profesors'];

        return view('academicos.home',compact($compact));
    }
}

<?php

namespace App\Http\Controllers\Controls;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    PUBLIC $director,$list_comment;
    public function __construct()
    {
        $this->middleware(['auth','is_controls', function ($request, $next) {
            $this->director = Autoridad::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function home()
    {
        $director = $this->director; //dd($director);
        $list_comment = $this->list_comment; //dd($list_comment);
        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $now = Carbon::now()->format('Y-m-d');
        $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();
        $retiros = DB::table('estudiants')
            ->select('estudiants.*')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->join('registro_pagos', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->GroupBy('estudiants.id')
            ->get();
        $profesors = Profesor::asignado('true')->get();

        return view('controls.home',compact('director','estudiants','pestudios','list_comment','plan_beneficos','profesors','retiros'));
    }
}

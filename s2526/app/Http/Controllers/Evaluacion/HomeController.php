<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$peducativos,$pestudios;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            // $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();
            $this->pestudios = Pestudio::getPestudios($user->id); //dd($this->pestudios);
            $this->peducativos = Peducativo::getPeducativos($user->id);
            return $next($request);
        }]);
    }

    public function home()
    {
        $user=$this->user;
        $autoridad=$this->autoridad;
        $pestudios=$this->pestudios;
        $peducativos=$this->peducativos; //dd($user,$peducativos,$pestudios);
        $list_comment_autoridad=$this->list_comment_autoridad;  
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        
        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $now = Carbon::now()->format('Y-m-d');
        $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();
        $profesors = Profesor::asignado('true')->get();

        $retiros = DB::table('estudiants')
            ->select('estudiants.*')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->join('registro_pagos', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->GroupBy('estudiants.id')
            ->get();

        $compact = Array('user','autoridad','list_comment_autoridad','estudiants','pestudios','plan_beneficos','profesors','retiros','lapsos','lapso_active');

        return view('evaluacions.home',compact($compact));
    }
}

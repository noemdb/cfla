<?php

namespace App\Http\Controllers\Leader\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfesorController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad;
    
    public function __construct()
    {
        $this->middleware(['auth','is_leader', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first(); //dd(Auth::id(),$this->autoridad);
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $user=$this->user;
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;

        $profesors = Profesor::getProfesorForLeaderId($user->id); //dd($profesors);


        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();        
        $area_conocimientos = AreaConocimiento::where('leader_id',$user->id)->get();

        $compact = [
            'profesors',
            'user','autoridad','list_comment_autoridad','area_conocimientos','lapsos','lapso_active',
        ];

        return view('leaders.profesors.index',compact($compact));
    }

    public function evaluacions(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        $user=$this->user;
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;

        $profesors = Profesor::getProfesorForLeaderId($user->id); //dd($profesors);       
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();        
        $area_conocimientos = AreaConocimiento::where('leader_id',$user->id)->get();
        $evaluacions = AreaConocimiento::getEvaluacionsForLeader($user->id);

        $compact = [
            'profesors',
            'user','autoridad','list_comment_autoridad','area_conocimientos','lapsos','lapso_active','evaluacions',
        ];

        return view('leaders.pevaluacions.evaluacions.index',compact($compact));
    }
}

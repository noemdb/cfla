<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePlanningController extends Controller
{
    use UserDataInitializer;
    
    public function __construct()
    {
        $this->middleware(['auth', 'is_planning', function ($request, $next) {
            $this->initializeUserData();
            return $next($request);
        }]);
    }

    public function home()
    {
        $user=$this->user; //dd($user);
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->listCommentAutoridad;  
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        
        return view('plannings.home',compact('user','autoridad','list_comment_autoridad','lapsos','lapso_active'));
    }
    public function indicators()
    {
        $user = $this->user;
        $pestudios = $this->pestudios; //dd($pestudios);
        $peducativos = $this->peducativos;
        $autoridad = $this->autoridad;
        $profesors = Profesor::getProfesorForLeaderId($user->id); 
        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        $now = Carbon::now()->format('Y-m-d');
        $list_comment_autoridad=$this->listCommentAutoridad;         
        return view('plannings.indicators',compact('user','autoridad','list_comment_autoridad','pestudios','pestudios','lapsos','lapso_active','estudiants','now','profesors'));
    }

}

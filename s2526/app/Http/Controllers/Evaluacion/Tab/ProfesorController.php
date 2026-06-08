<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfesorController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$pestudios,$peducativos;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            // $this->pestudios = Pestudio::getPestudios($user->id); //dd($this->pestudios);
            $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();
            $this->peducativos = Peducativo::getPeducativos($user->id);
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $user = $this->user;
        $pestudios = $this->pestudios;
        $peducativos = $this->peducativos;
        $profesors = Profesor::getProfesorForManagerIdEducativo(null,$user->id);
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current(); 

        return view('evaluacions.profesors.index', compact('pestudios','peducativos','profesors','lapsos','lapso_active'));
    }
}

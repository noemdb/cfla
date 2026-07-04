<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;

class PevaluacionController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$pestudios;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();
            return $next($request);
        }]);
    }

    public function pevaluacions(Request $request)
    {
        $pestudios = $this->pestudios;        
        return view('evaluacions.pevaluacions.index', compact('pestudios'));
    }

    public function index(Request $request)
    {
        $pestudios = $this->pestudios;        
        return view('evaluacions.pevaluacions.evaluacions.index', compact('pestudios'));
    }
}

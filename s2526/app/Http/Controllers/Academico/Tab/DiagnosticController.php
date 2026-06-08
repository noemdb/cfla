<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\app\Pescolar\Peducativo;

class DiagnosticController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$pestudios;
    
    public function __construct()
    {
        $this->middleware(['auth','is_academico', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::getPestudios($user->id); //dd($this->pestudios);
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('academicos.diagnostics.index',compact('autoridad','pestudios'));
    }
}

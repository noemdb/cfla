<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
use App\User;


class DiagnosticController extends Controller
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


    public function index(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('evaluacions.diagnostics.index',compact('autoridad','pestudios'));
    }
}

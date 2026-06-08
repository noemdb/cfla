<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstudiantController extends Controller
{
    public $user, $autoridad, $list_comment_autoridad, $pestudios;

    public function __construct()
    {
        $this->middleware(['auth', 'is_evaluacion', function ($request, $next) {
            $user_id = Auth::id();
            $user = User::find($user_id);
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id', $user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::Orderby('peducativos.order', 'asc')->where('pestudios.status_active', 'true')
                ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
                ->where(
                    function ($query) use ($user_id) {
                        $query->orWhere('peducativos.manager_id', $user_id)
                            ->orWhere('peducativos.assistant_id', $user_id)
                            ->orWhere('peducativos.deputy_id', $user_id)
                        ;
                    }
                )
                ->get();
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('evaluacions.estudiants.index', compact('autoridad', 'pestudios'));
    }
}

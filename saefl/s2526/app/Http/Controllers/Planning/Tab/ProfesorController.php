<?php

namespace App\Http\Controllers\Planning\Tab;

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
    use UserDataInitializer;

    public function __construct()
    {
        $this->middleware(['auth', 'is_planning', function ($request, $next) {
            $this->initializeUserData();
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $user = $this->user;
        $pestudios = $this->pestudios;
        $peducativos = $this->peducativos;
        // $profesors = Profesor::getProfesorForLeaderId($user->id);
        $profesors = Profesor::active("true")->get();
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current(); 

        return view('plannings.profesors.index', compact('pestudios','peducativos','profesors','lapsos','lapso_active'));
    }
}

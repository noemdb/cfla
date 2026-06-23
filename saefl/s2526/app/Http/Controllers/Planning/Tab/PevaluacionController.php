<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use Illuminate\Http\Request;

class PevaluacionController extends Controller
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
        $profesors = Profesor::getProfesorForLeaderId($user->id);
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current(); 

        return view('plannings.pevaluacions.index', compact('pestudios','peducativos','profesors','lapsos','lapso_active'));
    }

}

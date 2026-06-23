<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
///////////////////////////////////////////////////////////////
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstudiantController extends Controller
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
        $autoridad = $this->autoridad;
        $pestudios = $this->pestudios;
        return view('plannings.estudiants.index',compact('autoridad','pestudios'));
    }
}

<?php

namespace App\Http\Controllers\Profesor\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;

use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Estudiante\Representant;


class EstudiantController extends Controller
{
    public $profesor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;

        $estudiants = $profesor->guia_estudiants; //dd($estudiants);

        return view('profesors.estudiants.index',compact('estudiants'));
    }
}

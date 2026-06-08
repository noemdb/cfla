<?php

namespace App\Http\Controllers\General\Instrument;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class DiagnostigController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','is_profesor']);
    }

    public function index(Request $request)
    {        
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINADOR DE EVALUACIÓN'); //dd($autoridad2);
        $toDay = Date::now();
        return view('general.instruments.diagnostics.index',compact('institucion','autoridad1','autoridad2','toDay'));
    }
}

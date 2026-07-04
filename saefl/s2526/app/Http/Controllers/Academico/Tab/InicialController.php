<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InicialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_academico']);
    }
    public function index(Request $request)
    {
        $profesor_id = $request->profesor_id;
        $grado_id = $request->grado_id;
        $seccion_id = $request->seccion_id;

        $eiplanningwks = Eiplanningwk::query();
        $eiplanningwks = ($grado_id) ? $eiplanningwks->where('grado_id',$grado_id) : $eiplanningwks ;
        $eiplanningwks = ($profesor_id) ? $eiplanningwks->where('profesor_id',$profesor_id) : $eiplanningwks ;
        $eiplanningwks = $eiplanningwks->get();

        $eiprojectks = Eiprojectk::query();
        $eiprojectks = ($grado_id) ? $eiprojectks->where('grado_id',$grado_id) : $eiprojectks ;
        $eiprojectks = ($profesor_id) ? $eiprojectks->where('profesor_id',$profesor_id) : $eiprojectks ;
        $eiprojectks = $eiprojectks->get();

        $list_grado = Grado::list_pestudio_grado(6); //Educ Inicial
        $list_profesors = Profesor::list_profesors_pestudio(6); //Educ Inicial
        $compact = [
            'eiplanningwks',
            'eiprojectks',
            'profesor_id',
            'grado_id',
            'list_grado',
            'list_profesors',
        ];
        return view('academicos.inicilas.index',compact($compact));
    }

    public function format_eiplanningwk($id, Request $request)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);
        $profesor = $eiplanningwk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eiplanningwk.index',compact('profesor','eiplanningwk','institucion','fecha'));
    }

    public function format_eiprojectks($id, Request $request)
    {
        $eiprojectk = Eiprojectk::findOrFail($id);
        $profesor = $eiprojectk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eiprojectks.index',compact('profesor','eiprojectk','institucion','fecha'));
    }
}

/*
profesor_id
grado_id
seccion_id
*/
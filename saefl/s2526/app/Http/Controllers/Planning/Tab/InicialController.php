<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Profesor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InicialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_planning']);
    }
    public function index(Request $request)
    {
        $profesor_id = $request->profesor_id;
        $grado_id = $request->grado_id;
        $seccion_id = $request->seccion_id;

        $eiplanningwks = Eiplanningwk::query();
        $eiplanningwks = ($grado_id) ? $eiplanningwks->where('grado_id',$grado_id) : $eiplanningwks ;
        $eiplanningwks = ($profesor_id) ? $eiplanningwks->where('profesor_id',$profesor_id) : $eiplanningwks ;
        $eiplanningwks = $eiplanningwks->get() ?? collect();

        $eiplanningbwks = Eiplanningbwk::query();
        $eiplanningbwks = ($grado_id) ? $eiplanningbwks->where('grado_id',$grado_id) : $eiplanningbwks ;
        $eiplanningbwks = ($profesor_id) ? $eiplanningbwks->where('profesor_id',$profesor_id) : $eiplanningbwks ;
        $eiplanningbwks = $eiplanningbwks->get() ?? collect();

        $eiprojectks = Eiprojectk::query();
        $eiprojectks = ($grado_id) ? $eiprojectks->where('grado_id',$grado_id) : $eiprojectks ;
        $eiprojectks = ($profesor_id) ? $eiprojectks->where('profesor_id',$profesor_id) : $eiprojectks ;
        $eiprojectks = $eiprojectks->get() ?? collect();

        $eispecialks = Eispecialk::when($grado_id, fn($q) => $q->where('grado_id', $grado_id))
                         ->when($profesor_id, fn($q) => $q->where('profesor_id', $profesor_id))
                         ->get()
                         ->whenEmpty(fn() => collect());

        $eievaluationks = Eievaluationk::query();
        $eievaluationks = ($grado_id) ? $eievaluationks->where('grado_id',$grado_id) : $eievaluationks ;
        $eievaluationks = ($profesor_id) ? $eievaluationks->where('profesor_id',$profesor_id) : $eievaluationks ;
        $eievaluationks = $eievaluationks->get() ?? collect();

        $list_grado = Grado::list_pestudio_grado(6); //Educ Inicial
        $list_profesors = Profesor::list_profesors_pestudio(6); //Educ Inicial
        $compact = [
            'eiplanningwks',
            'eiplanningbwks',
            'eispecialks',
            'eiprojectks',
            'eievaluationks',
            'profesor_id',
            'grado_id',
            'list_grado',
            'list_profesors',
        ];
        return view('plannings.inicilas.index',compact($compact));
    }

    public function format_eiplanningwk($id, Request $request)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);
        $profesor = $eiplanningwk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eiplanningwk.index',compact('profesor','eiplanningwk','institucion','fecha'));
    }

    public function format_eiplanningbwk($id, Request $request)
    {
        $eiplanningbwk = Eiplanningbwk::findOrFail($id);
        $profesor = $eiplanningbwk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eiplanningbwk.index',compact('profesor','eiplanningbwk','institucion','fecha'));
    }

    public function format_eiprojectks($id, Request $request)
    {
        $eiprojectk = Eiprojectk::findOrFail($id);
        $profesor = $eiprojectk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eiprojectks.index',compact('profesor','eiprojectk','institucion','fecha'));
    }

    public function format_eispecialks($id, Request $request)
    {
        $eispecialk = Eispecialk::findOrFail($id);
        $profesor = $eispecialk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eispecialks.index',compact('profesor','eispecialk','institucion','fecha'));
    }

    public function format_eievaluationks($id, Request $request)
    {
        $eievaluationk = Eievaluationk::findOrFail($id);
        $profesor = $eievaluationk->profesor;
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eievaluationk.index',compact('profesor','eievaluationk','institucion','fecha'));
    }
}

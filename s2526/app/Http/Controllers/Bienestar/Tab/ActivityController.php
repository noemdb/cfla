<?php

namespace App\Http\Controllers\Bienestar\Tab;

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

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }

    public function index()
    {
        return view('bienestars.activities.index');
    }

    public function format($id)
    {
        /*******************query****************************/

        $pevaluacion = Pevaluacion::findOrFail($id);

        $activities = Activity::where('pevaluacion_id',$id)->orderBy('finicial','asc')->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        return view('plannings.activities.format',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function resume($id)
    {
        /*******************query****************************/

        $pevaluacion = Pevaluacion::findOrFail($id);

        $activities = Activity::where('pevaluacion_id',$id)->whereNotNull('description')->orderBy('finicial','asc')->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        return view('plannings.activities.resume',compact('pevaluacion','activities','institucion','fecha'));
    }
}

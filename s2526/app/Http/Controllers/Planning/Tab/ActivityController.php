<?php

namespace App\Http\Controllers\Planning\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
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

        return view('plannings.activities.index', compact('pestudios','peducativos','profesors','lapsos','lapso_active'));
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

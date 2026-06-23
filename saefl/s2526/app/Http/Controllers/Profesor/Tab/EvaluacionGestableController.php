<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\EvaluacionGestable;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Profesor\ProfesorGestable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EvaluacionGestableController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function store(Request $request)
    {
        $profesor = $this->profesor;

        $evaluacion = Evaluacion::create($request->all());

        if ($evaluacion) {
            $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
            $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
            $profesor_gestable = ProfesorGestable::where('profesor_id',$profesor->id)->where('pevaluacion_id',$pevaluacion_id)->first();

            $profesor_gestable = EvaluacionGestable::create([
                'profesor_gestable_id'=>$profesor->id,
                'evaluacion_id'=>$evaluacion->id
            ]);
        }

        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
        $inputs = [
            'grado_id'=>$grado_id,
            'seccion_id'=>$seccion_id,
            'lapso_id'=>$lapso_id,
            'pevaluacion_id'=>$pevaluacion_id,
        ];
        return redirect()->route('profesors.profesor_gestables.index',$inputs);
    }
}

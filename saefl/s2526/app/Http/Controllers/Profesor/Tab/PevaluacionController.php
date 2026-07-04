<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Profesor\CreatePevaluacionRequest;
use App\Http\Requests\Administracion\Profesor\UpdatePevaluacionRequest;

// use App\Http\Requests\Administracion\UpdateUserRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Escala;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\PlanBenefico;
use Illuminate\Support\Facades\Auth;

class PevaluacionController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function crud(Request $request)
    {
        $profesor = $this->profesor;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->where('profesor_id',$profesor->id)
            ->OrderBy('created_at','desc');

        $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        $pevaluacions = $pevaluacions->get();

        $list_grado = Profesor::list_grado($profesor->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.pevaluacions.crud',compact('pevaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id); //dd($pevaluacion);
        $pensum = $pevaluacion->pensum;
        $grado = $pensum->grado;
        $lapso = $pevaluacion->lapso;
        $seccion = $pevaluacion->seccion;

        // $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');
        $tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $baremo_apply_list = Baremo::baremo_apply_list();
        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $profesor_list = Profesor::all()->sortByDesc('created_at')->pluck('fullname','id');

        return view('profesors.pevaluacions.edit',compact('pevaluacion','lapso_list','escala_list','tipo_list','seccion','pensum','grado','lapso','profesor_list','baremo_apply_list'));
    }

    public function update(UpdatePevaluacionRequest $request, $id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $pevaluacion->fill($request->all());
        $pevaluacion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);

        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $pevaluacions = Pevaluacion::all()->where('profesor_id',$profesor->id)->sortByDesc('created_at');

        return redirect()->route('profesors.pevaluacions.crud',compact('pevaluacions'));
    }
}

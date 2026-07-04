<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Profesor\CreateEvaluacionRequest;
use App\Http\Requests\Administracion\Profesor\UpdateEvaluacionRequest;
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
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Escala;
use Illuminate\Support\Facades\Auth;

class EvaluacionController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        /*******************request****************************/
        $profesor = $this->profesor;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        /*******************query****************************/
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->where('profesor_id',$profesor->id)
            ->OrderBy('created_at','desc');

            /*******************if()?****************************/
        $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        /*******************get collections****************************/
        $pevaluacions = $pevaluacions->get();

        /*******************list****************************/
        $list_grado = Profesor::list_grado($profesor->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.evaluacions.index',compact('pevaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }

    public function crud(Request $request)
    {
        /*******************request****************************/
        $profesor = $this->profesor;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        /*******************query****************************/
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id',$profesor->id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

        /*******************if()?****************************/
        $evaluacions = ($grado_id) ? $evaluacions->where('pensums.grado_id',$grado_id) : $evaluacions ;
        $evaluacions = ($seccion_id) ? $evaluacions->where('pevaluacions.seccion_id',$seccion_id) : $evaluacions ;
        $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;

        /*******************get collections****************************/
        $evaluacions = $evaluacions->get();

        /*******************list****************************/
        $list_grado = Profesor::list_grado($profesor->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.evaluacions.crud',compact('evaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }

    public function create($pevaluacion_id, Request $request)
    {
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);

        $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        // dd($pevaluacion->evaluacions);

        return view('profesors.evaluacions.create',compact('pevaluacion','escala_list'));
    }

    public function store(CreateEvaluacionRequest $request)
    {

        $evaluacion = Evaluacion::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');

        $pevaluacion = Pevaluacion::findorfail($request->pevaluacion_id);

        $id = $pevaluacion->id;

        $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return redirect()->route('profesors.evaluacions.create',compact('id','pevaluacion','escala_list'));
    }

    public function edit($id)
    {
        $evaluacion = Evaluacion::findOrFail($id);

        $pevaluacion = $evaluacion->pevaluacion;

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.evaluacions.edit',
            compact('evaluacion','pevaluacion','escala_list'));
    }

    public function update(UpdateEvaluacionRequest $request, $id)
    {

        $evaluacion = Evaluacion::findOrFail($id);
        $evaluacion->fill($request->all());
        $evaluacion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);

        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
            ->where('pevaluacions.profesor_id',$profesor->id)
            ->wherenull('pevaluacions.deleted_at')
            ->get();

        return redirect()->route('profesors.evaluacions.crud',compact('evaluacions'));
    }

    public function create_clone($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);

        $pensum = Pensum::findOrFail($pevaluacion->pensum_id);
        $grado = Grado::findOrFail($pensum->grado_id);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id);

        $pevaluacion_list = Pevaluacion::select('pevaluacions.id',
                DB::raw("CONCAT(grados.name, ' ',seccions.name, ' - ',asignaturas.name, ' || ' ,pevaluacions.description) as pfullname"))
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->where('pevaluacions.id','<>',$pevaluacion->id)
                ->where('pevaluacions.profesor_id',$pevaluacion->profesor_id)
                ->where('pevaluacions.pensum_id',$pevaluacion->pensum_id)
                ->where('pevaluacions.lapso_id',$pevaluacion->lapso_id)
                ->OrderBy('pevaluacions.created_at')
                ->pluck('pfullname', 'id');

        $seccion_list = $grado->seccions->where('id','<>',$seccion->id)->pluck('name','id');

        return view('profesors.evaluacions.create_clone',compact('pevaluacion','pevaluacion_list','pensum','grado','seccion_list'));
    }

    public function store_clone(Request $request)
    {
        $pevaluacion_origen = Pevaluacion::findOrFail($request->pe_id_origen);

        $pevaluacion_destino = Pevaluacion::findOrFail($request->pe_id_destino);

        $evaluacions = $pevaluacion_origen->evaluacions;

        foreach ($evaluacions as $evaluacion) {
            $evaluacion_arr = $evaluacion->toarray();
            $evaluacion_arr['pevaluacion_id'] = $pevaluacion_destino->id;
            $pevaluacion = Evaluacion::create($evaluacion_arr);
        }

        $messenge = trans('db_oper_result.create_ok');
        $operation= 'clone';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok','Buen trabajo! El registro Fue exportado exitosamente');
        $evaluacions = Evaluacion::all()->sortByDesc('created_at');
        return redirect()->route('profesors.pevaluacions.crud');
    }

    public function destroy($id, Request $request)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $evaluacion->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        $evaluacions = Evaluacion::all()->sortByDesc('created_at');
        return view('administracion.pevaluacions.evaluacions.crud',compact('evaluacions'));
    }

}

<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Profesor\CreateEvaluacionRequest;
use App\Http\Requests\Administracion\Profesor\UpdateEvaluacionRequest;
use App\Models\app\Pescolar\Asignatura;
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


class EvaluacionController extends Controller
{

    public function index(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $finicial = (!empty($request->finicial)) ? $request->finicial: null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal: null;

        /*******************inicializaciones****************************/
        $pevaluacions = collect();

        if (count($request->all())>0) {
            /*******************query****************************/
            $pevaluacions = Pevaluacion::select('pevaluacions.*')
                ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
                ->OrderBy('created_at','desc');

            /*******************if()?****************************/
            $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
            $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
            $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;
            $pevaluacions = ($pensums_id) ? $pevaluacions->where('pensums.id',$pensums_id) : $pevaluacions ;
            $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id',$profesor_id) : $pevaluacions ;
            $pevaluacions = ($finicial) ? $pevaluacions->where('pevaluacions.created_at',$finicial) : $pevaluacions ;
            $pevaluacions = ($ffinal) ? $pevaluacions->where('pevaluacions.created_at',$ffinal) : $pevaluacions ;

            /*******************get collections****************************/
            $pevaluacions = $pevaluacions->get();
        }


        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);
        $list_profesor = Profesor::list_profesors();

        return view('administracion.pevaluacions.evaluacions.index',
        compact('pevaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso','list_pensum','pensums_id','list_profesor','profesor_id', 'finicial', 'ffinal'));
    }

    public function crud(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $finicial = (!empty($request->finicial)) ? $request->finicial: null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal: null;
        $evaluacions = collect();

        if (count($request->all())>0) {

            $evaluacions = Evaluacion::select('evaluacions.*')
                ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
                ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('seccions', 'grados.id', '=', 'seccions.grado_id')

                ->wherenull('pevaluacions.deleted_at')
                ->wherenull('pensums.deleted_at')

                ->orderby('evaluacions.created_at')
                ->groupby('evaluacions.id');

                $evaluacions = ($grado_id) ? $evaluacions->where('pensums.grado_id',$grado_id) : $evaluacions ;
                $evaluacions = ($seccion_id) ? $evaluacions->where('pevaluacions.seccion_id',$seccion_id) : $evaluacions ;
                $evaluacions = ($lapso_id) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions ;
                $evaluacions = ($pensums_id) ? $evaluacions->where('pensums.id',$pensums_id) : $evaluacions ;
                $evaluacions = ($profesor_id) ? $evaluacions->where('pevaluacions.profesor_id',$profesor_id) : $evaluacions ;
                $evaluacions = ($finicial) ? $evaluacions->where('evaluacions.fecha','>=',$finicial) : $evaluacions ;
                $evaluacions = ($ffinal) ? $evaluacions->where('evaluacions.fecha','<=',$ffinal) : $evaluacions ;

                $evaluacions = $evaluacions->get();

        }

        /* list para los select-html */
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);
        $list_profesor = Profesor::list_profesors();

        return view('administracion.pevaluacions.evaluacions.crud',
        compact('evaluacions','grado_id','list_grado','seccion_id','list_seccion','finicial','ffinal','lapso_id','list_lapso','list_pensum','pensums_id','list_profesor','profesor_id'));
    }

    public function create_clone($id)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $pevaluacion = Pevaluacion::findOrFail($evaluacion->pevaluacion->id);
        $pensum = Pensum::findOrFail($pevaluacion->pensum_id);
        $grado = Grado::findOrFail($pensum->grado_id);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id);

        $seccion_list = $grado->seccions->where('id','<>',$seccion->id)->pluck('name','id');

        return view('administracion.pevaluacions.evaluacions.create_clone',compact('evaluacion','pevaluacion','pensum','grado','seccion_list'));
    }

    public function store_clone(Request $request)
    {
        $pevaluacion = Pevaluacion::findOrFail($request->pevaluacion_id);

        $evaluacions = $pevaluacion->evaluacions;

        foreach ($evaluacions as $evaluacion) {
            $evaluacion_arr = $evaluacion->toarray();
            $pevaluacion = Evaluacion::create($evaluacion_arr);
        }

        $messenge = trans('db_oper_result.update_ok');
        $operation= 'clone';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        $evaluacions = Evaluacion::all()->sortByDesc('created_at');
        return redirect()->route('administracion.evaluacions.crud',compact('evaluacions'));
    }

    public function create($pevaluacion_id, Request $request)
    {
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);

        $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        // dd($pevaluacion->evaluacions);

        return view('administracion.pevaluacions.evaluacions.create',compact('pevaluacion','escala_list'));
    }

    public function store(CreateEvaluacionRequest $request)
    {
        $evaluacion = Evaluacion::create($request->all());

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok','Registro guardado exitosamente');

        $pevaluacion = Pevaluacion::findorfail($request->pevaluacion_id);

        $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return redirect()->route('administracion.evaluacions.create',compact('pevaluacion','escala_list'));
    }

    public function store_pevaluacions(CreateEvaluacionRequest $request)
    {
        $evaluacion = Evaluacion::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');

        $pensum_id = $request->pensum_id;
        $grado_id = $request->grado_id;
        $lapso_id = $request->lapso_id;
        $pevaluacion_id = $request->pevaluacion_id;

        $pevaluacion = Pevaluacion::findorfail($pevaluacion_id);
        $seccion = $pevaluacion->seccion;
        $seccion_id = $seccion->id;

        $evaluacion = ($evaluacion) ? $evaluacion : new Evaluacion ;

        $grado = Grado::findOrFail($grado_id);
        $pensum = Pensum::findOrFail($pensum_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $profesor_list = Profesor::all()->sortByDesc('created_at')->pluck('fullname','id');

        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return redirect()->route('administracion.pevaluacions.create',compact('grado_id','seccion_id','pensum_id','lapso_id','evaluacion','pevaluacion','grado','pensum','lapso','profesor_list','lapso_list','escala_list'));
    }

    public function edit($id)
    {
        $evaluacion = Evaluacion::findOrFail($id);

        $pevaluacion = $evaluacion->pevaluacion;

        $seccion = $pevaluacion->seccion;
        $seccion_id = $seccion->id;

        $pensum = Pensum::findOrFail($pevaluacion->pensum_id);

        $grado = Grado::findOrFail($pensum->grado_id);

        $lapso = Lapso::findOrFail($pevaluacion->lapso_id);

        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('administracion.pevaluacions.evaluacions.edit',
            compact('seccion','seccion_id','evaluacion','pevaluacion','pensum','grado','lapso','escala_list'));
    }
    public function update(UpdateEvaluacionRequest $request, $id)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $evaluacion->fill($request->all());
        $evaluacion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.evaluacions.edit',$id);
    }
    public function destroy($id, Request $request)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $boletins = $evaluacion->boletins; //dd($boletins);
        foreach ($boletins as $boletin) {
            $boletin->delete();
        }

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

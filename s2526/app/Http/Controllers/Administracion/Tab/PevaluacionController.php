<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Profesor\CreatePevaluacionRequest;
use App\Http\Requests\Administracion\Profesor\UpdatePevaluacionRequest;
use App\Models\app\Educational\DebateQuestion;
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
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Pescolar\Asignatura;

class PevaluacionController extends Controller
{
    public function index(Request $request)
    {
        $pensums = Pensum::all();
        $pevaluacions = Pevaluacion::all();
        $lapsos = Lapso::all();
        $grados = Grado::all();
        $seccions = Seccion::all();
        $profesors = Profesor::all();
        $fecha = Carbon::Now();
        $lapso_active = Lapso::WhereDate('finicial','<=', $fecha)->WhereDate('ffinal','>=', $fecha)->first(); //dd($lapso_active);
        $lapso_active = ($lapso_active) ? $lapso_active : Lapso::first() ;

        return view('administracion.pevaluacions.index',
            compact(
                'grados','seccions','profesors',
                'lapsos','pensums','lapso_active'
        ));
    }

    public function carga(Request $request)
    {
        /*******************request************************************************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;

        /*******************inicializaciones***************************************************/
        $pensums = collect();

        $grado = ($grado_id) ? Grado::find($grado_id) : null ;
        $seccion = ($seccion_id) ? Seccion::find($seccion_id) : null ;

        /*******************query builder***************************************************/


        if ($grado_id || $pensums_id) {
            // dd($grado_id,$seccion_id,$lapso_id,$profesor_id,$pensums_id);
            $pensums = Pensum::select('pensums.*')
            // ->join('pevaluacions','pensums.id','=','pevaluacions.pensum_id')
            ->groupBy('pensums.id')
            ->OrderBy('created_at','desc');

            /*******************if()?****************************/
            $pensums = ($grado_id) ? $pensums->where('pensums.grado_id',$grado_id) : $pensums ;
            // $pensums = ($seccion_id) ? $pensums->join('pevaluacions','pensums.id','=','pevaluacions.pensum_id')->where('pevaluacions.seccion_id',$seccion_id) : $pensums ;
            // $pensums = ($lapso_id) ? $pensums->where('pevaluacions.lapso_id',$lapso_id) : $pensums ;
            // $pensums = ($profesor_id) ? $pensums->where('pevaluacions.profesor_id',$profesor_id) : $pensums ;
            $pensums = ($pensums_id) ? $pensums->where('pensums.id',$pensums_id) : $pensums ;

            /*******************get collections****************************/
            $pensums = $pensums->get();

        }

        // dd($pensums);

        /*******************list*****************************************************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_profesor = Profesor::list_profesors();
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);

        return view('administracion.pevaluacions.carga',
        compact('pensums','grado','grado_id','list_grado','seccion','seccion_id','list_seccion','list_lapso','list_profesor','profesor_id','list_pensum','pensums_id'));
    }

    public function crud(Request $request)
    {
        /*******************request************************************************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;

        /*******************inicializaciones***************************************************/
        $pevaluacions = collect();

        /*******************query builder***************************************************/

        if (count($request->all())>0) {
            $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->OrderBy('created_at','desc');

            /*******************if()?****************************/
            $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
            $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
            $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;
            $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id',$profesor_id) : $pevaluacions ;
            $pevaluacions = ($pensums_id) ? $pevaluacions->where('pensums.id',$pensums_id) : $pevaluacions ;

            /*******************get collections****************************/
            $pevaluacions = $pevaluacions->get();

        }

        /*******************list*****************************************************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_profesor = Profesor::list_profesors();
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);
        $list_grupo_estable = GrupoEstable::list_grupo_estable_full();

        return view('administracion.pevaluacions.crud',
        compact('pevaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso','list_profesor','profesor_id','list_pensum','pensums_id','list_grupo_estable'));
    }

    public function create($grado_id, $seccion_id, $pensum_id, $lapso_id, Request $request)
    {
        $pevaluacion = Pevaluacion::where('pensum_id',$pensum_id)
            ->where('lapso_id',$lapso_id)
            ->where('seccion_id',$seccion_id)
            ->first();

        $pevaluacion = ($pevaluacion) ? $pevaluacion : new Pevaluacion ;
        $lapso = Lapso::findOrFail($lapso_id);
        $grado = Grado::findOrFail($grado_id);
        $seccions = $grado->seccions;
        $seccion = Seccion::findOrFail($seccion_id);
        $pensum = Pensum::findOrFail($pensum_id);
        $profesor_list = Profesor::all()->sortBy('fullname')->pluck('fullname','id');

        $list_seccion = Seccion::where('grado_id',$grado_id)->pluck('name', 'id');

        $list_pevaluacion = Pevaluacion::select('pevaluacions.id','seccions.name')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->where('pevaluacions.pensum_id',$pensum_id)
            ->where('pevaluacions.lapso_id',$lapso_id)
            ->pluck('name', 'id');

        $arr_id_pe = Pevaluacion::where('pensum_id',$pensum_id)->pluck('id')->toarray();
        $str_id_pe = implode(",", $arr_id_pe);
        $list_pevaluacion->put($str_id_pe,'AB');

        // $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');
        $tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $baremo_apply_list = Baremo::baremo_apply_list();

        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado) ? Seccion::Where('grado_id',$grado->id)->pluck('name', 'id') : collect();

        $pensum_list = Pensum::list_pestudio_pensum($grado_id);

        // dd($pevaluacion->evaluacions);

        $list_grupo_estable = GrupoEstable::list_grupo_estable_full();

        $list_category = DebateQuestion::CATEGORY;

        return view('administracion.pevaluacions.create',compact('pevaluacion','pensum_list','list_grado','list_seccion','list_pevaluacion','seccion','list_seccion',
        'tipo_list','grado_id','seccion_id','pensum_id','lapso_id','pevaluacion','grado','seccions',
        'pensum','lapso','profesor_list','lapso_list','escala_list','baremo_apply_list', 'list_grupo_estable','list_category'));
    }

    public function store(CreatePevaluacionRequest $request)
    {
        $request_arr = $request->all();

        $seccion_ids_arr = $request['seccion_id'];

        $pensum_id = $request->pensum_id;
        $grado_id = $request->grado_id;
        $lapso_id = $request->lapso_id;
        $seccion_id = $request->seccion_id_old;

        // foreach ($seccion_ids_arr as $k => $v) {
        //     if ($v=='true') {
        //         $test = Pevaluacion::where('seccion_id',$k)->where('lapso_id',$lapso_id)->where('pensum_id',$pensum_id)->get();
        //         if($test->isEmpty()){
        //             $request_arr['seccion_id'] = $k;
        //             $pevaluacion = Pevaluacion::create($request_arr);
        //             Session::flash('operp_ok','Registro guardado exitosamente');
        //         } else {
        //             Session::flash('operp_no_ok','La sección '.Seccion::getName($v).' ya tiene Plan de Estudio registrado');
        //         }
        //     }
        // }

        $pevaluacion = Pevaluacion::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');

        $pevaluacion = Pevaluacion::where('pensum_id',$pensum_id)->first();
        $pevaluacion = ($pevaluacion) ? $pevaluacion : new Pevaluacion ;

        $grado = Grado::findOrFail($grado_id);
        $pensum = Pensum::findOrFail($pensum_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $profesor_list = Profesor::all()->sortByDesc('created_at')->pluck('fullname','id');

        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return redirect()->route('administracion.pevaluacions.create', compact('grado_id','seccion_id','pensum_id','lapso_id'));
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id); //dd($pevaluacion);
        $pensum = Pensum::findOrFail($pevaluacion->pensum_id); //dd($pensum);
        $lapso = Lapso::findOrFail($pevaluacion->lapso_id); //dd($lapso);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id); //dd($seccion);
        $grado = Grado::findOrFail($seccion->grado_id);

        $grado_id = ($grado) ? $grado->id : null ;
        $seccion_id = ($seccion) ? $seccion->id : null ;

        $tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $baremo_apply_list = Baremo::baremo_apply_list();
        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $pensum_list = Pensum::list_pestudio_pensum();

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado) ? Seccion::Where('grado_id',$grado->id)->pluck('name', 'id') : collect();

        $profesor_list = Profesor::all()->sortByDesc('created_at')->pluck('fullname','id');

        $pensum_list = Pensum::list_pestudio_pensum($grado->id);
        $pensum_list = (count($pensum_list)) ? $pensum_list : Pensum::list_pestudio_pensum();

        $list_grupo_estable = GrupoEstable::list_grupo_estable_full();

        $list_category = DebateQuestion::CATEGORY;

        return view('administracion.pevaluacions.edit',compact('pevaluacion','grado_id','seccion_id','list_grado','list_seccion','lapso_list','escala_list','list_category','tipo_list','pensum_list','seccion','pensum','grado','lapso','profesor_list','baremo_apply_list','list_grupo_estable'));
    }
    public function update(UpdatePevaluacionRequest $request, $id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id); //dd($pevaluacion,$request->all());
        $pevaluacion->fill($request->all()); //dd($request->all());
        $pevaluacion->save(); //dd($pevaluacion);
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.pevaluacions.edit',$id);
    }

    public function create_clone($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);

        $pensum = Pensum::findOrFail($pevaluacion->pensum_id);
        $grado = Grado::findOrFail($pensum->grado_id);
        $lapso = Lapso::findOrFail($pevaluacion->lapso_id);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id);

        $seccion_list = $grado->seccions->where('id','<>',$seccion->id)->pluck('name','id');

        return view('administracion.pevaluacions.create_clone',compact('pevaluacion','pensum','grado','lapso','seccion_list'));
    }

    // public function store(CreatePevaluacionRequest $request)
    public function store_clone(Request $request)
    {
        // dd($request->all());
        $pevaluacion = Pevaluacion::findOrFail($request->pevaluacion_id);
        $pevaluacion_arr = $pevaluacion->toarray();

        $pevaluacion_arr['seccion_id'] = $request->seccion_id;

        $pevaluacion = Pevaluacion::create($pevaluacion_arr);

        $messenge = trans('db_oper_result.update_ok');
        $operation= 'clone';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        $pevaluacions = Pevaluacion::all()->sortByDesc('created_at');
        return redirect()->route('administracion.pevaluacions.crud',compact('pevaluacions'));
        // return view('administracion.pevaluacions.crud',compact('pevaluacions'));
    }


    public function destroy($id, Request $request)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $pevaluacion->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        $pevaluacions = Pevaluacion::all()->sortByDesc('created_at');
        return view('administracion.pevaluacions.crud',compact('pevaluacions'));
    }
}

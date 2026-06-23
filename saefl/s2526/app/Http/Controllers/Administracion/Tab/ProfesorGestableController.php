<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Escala;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\ProfesorGestable;

class ProfesorGestableController extends Controller
{

    public function index(Request $request)
    {
        /*******************request************************************************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $pensum_seccion_id = (!empty($request->pensum_seccion_id)) ? $request->pensum_seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id: null;
        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;

        /*******************inicializaciones***************************************************/
        $pensums = collect();
        $selected = ($pensum_id) ? Pensum::find($pensum_id) : null ; //dd($pensum_id,$selected);
        $modeSetUp = ($selected) ? true : false ;
        $profesor_gestables = ($selected) ? $selected->profesor_gestables : collect() ;
        $pevaluacion = ($pevaluacion_id) ? Pevaluacion::find($pevaluacion_id) : null ; //dd($pensum_id,$selected);

        /*******************query builder***************************************************/
        if (count($request->all())>0) {
            $pensums = Pensum::select('pensums.*','seccions.name as seccion_name','seccions.id as seccion_id','lapsos.name as lapso_name')
            ->join('pevaluacions','pensums.id','=','pevaluacions.pensum_id')
            ->join('lapsos','lapsos.id','=','pevaluacions.lapso_id')
            ->join('seccions','seccions.id','=','pevaluacions.seccion_id')
            ->join('asignaturas','asignaturas.id','=','pensums.asignatura_id')
            ->where('asignaturas.enable_grupo_estable','true')
            ->OrderBy('created_at','desc')
            ->groupBy('seccions.id')
            ;

            /*******************if()?****************************/
            $pensums = ($grado_id) ? $pensums->where('pensums.grado_id',$grado_id) : $pensums ;
            $pensums = ($seccion_id) ? $pensums->where('pevaluacions.seccion_id',$seccion_id) : $pensums ;
            $pensums = ($lapso_id) ? $pensums->where('pevaluacions.lapso_id',$lapso_id) : $pensums ;
            $pensums = ($pensum_id) ? $pensums->where('pensums.id',$pensum_id) : $pensums ;

            /*******************get collections****************************/
            $pensums = $pensums->get();
        }
        //dd($pensums);

        /*******************list*****************************************************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_profesor = Profesor::list_profesors(true);
        $list_grupo_estable = GrupoEstable::list_grupo_estable_active();
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);

        $compact = [
            'pensums',
            'modeSetUp','selected','profesor_gestables',
            'pevaluacion_id','lapso_id','pevaluacion','profesor_id','pensum_id','grado_id','seccion_id','pensum_seccion_id',
            'list_lapso','list_pensum','list_grado','list_seccion','list_profesor','list_grupo_estable',
        ];

        return view('administracion.profesor_gestables.index', compact($compact));
    }

    public function create($pevaluacion_id, Request $request)
    {
        /*******************request************************************************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $pensum_seccion_id = (!empty($request->pensum_seccion_id)) ? $request->pensum_seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id: null;
        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
        /*******************inicializaciones***************************************************/

        $pevaluacion = Pevaluacion::where('pensum_id',$pensum_id)
            ->where('lapso_id',$lapso_id)
            ->where('seccion_id',$seccion_id)
            ->first();

        $pevaluacion = ($pevaluacion) ? $pevaluacion : new Pevaluacion ;
        $lapso = Lapso::findOrFail($lapso_id);
        $grado = Grado::findOrFail($grado_id);
        $seccions = $grado->seccions;
        $seccion = Seccion::findOrFail($pensum_seccion_id);
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

        // dd($pevaluacion->evaluacions);

        return view('administracion.profesor_gestables.create',compact('pevaluacion','list_pevaluacion','seccion','list_seccion',
        'tipo_list','grado_id','seccion_id','pensum_seccion_id','pensum_id','lapso_id','pevaluacion','grado','seccions',
        'pensum','lapso','profesor_list','lapso_list','escala_list','baremo_apply_list'));
    }

    public function store(Request $request)
    {
        $arr = [
            'profesor_id'=>$request->profesor_id,
            'lapso_id'=>$request->lapso_id,
            'seccion_id'=>$request->pensum_seccion_id,
            'pensum_id'=>$request->pensum_id,
            'status_baremo'=>'false',
            'nota_type'=>'PROMEDIADA',
            'escala_id'=>1,
            'objetivo'=>'Subplan de Evaluación área de formación',
            'description'=>'Grupo Estable del área de formación',
            'observations'=>'Asignación del grupo estable',
        ];
        $pevaluacion = Pevaluacion::create($arr);

        $arr = [
            'profesor_id'=>$request->profesor_id,
            'pevaluacion_id'=>$pevaluacion->id,
            'grupo_estable_id'=>$request->grupo_estable_id,
            'observations'=>$request->observations,
        ];
        $profesor_gestable = ProfesorGestable::create($arr);
        
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $pensum_seccion_id = (!empty($request->pensum_seccion_id)) ? $request->pensum_seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id: null;
        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
        $inputs = [
            'grado_id'=>$grado_id,
            'seccion_id'=>$seccion_id,
            'pensum_seccion_id'=>$pensum_seccion_id,
            'lapso_id'=>$lapso_id,
            'profesor_id'=>$profesor_id,
            'pensum_id'=>$pensum_id,
            'pevaluacion_id'=>$pevaluacion_id,
        ];
        return redirect()->route('administracion.profesor_gestables.index',$inputs);
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id); //dd($pevaluacion);

        $pensum = Pensum::findOrFail($pevaluacion->pensum_id); //dd($pensum);
        $grado = Grado::findOrFail($pensum->grado_id); //dd($grado);
        $lapso = Lapso::findOrFail($pevaluacion->lapso_id); //dd($lapso);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id); //dd($seccion);

        // $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');
        $tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $baremo_apply_list = Baremo::baremo_apply_list();
        $lapso_list = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $profesor_list = Profesor::all()->sortByDesc('created_at')->pluck('fullname','id');

        return view('administracion.profesor_gestables.edit',compact('pevaluacion','lapso_list','escala_list','tipo_list','seccion','pensum','grado','lapso','profesor_list','baremo_apply_list'));
    }

    public function update(Request $request, $id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $pevaluacion->fill($request->all());
        $pevaluacion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.profesor_gestables.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $delete = ProfesorGestable::findOrFail($id);
        $delete->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return view('administracion.profesor_gestables.crud');
    }

}

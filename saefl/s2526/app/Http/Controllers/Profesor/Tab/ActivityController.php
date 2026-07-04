<?php

namespace App\Http\Controllers\Profesor\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Escala;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActivityController extends Controller
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
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('profesor_id',$profesor->id)
            ->where('pestudios.planning_module', true)  //modulo de planificacion
            ->where('pestudios.status_active', 'true') 
            ->OrderBy('created_at','desc');

            /*******************if()?****************************/
        $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        /*******************get collections****************************/
        $pevaluacions = $pevaluacions->get(); // dd($pevaluacions);

        /*******************list****************************/
        $list_grado = Profesor::list_grado($profesor->id,true);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.activities.index',compact('pevaluacions','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }

    public function create($pevaluacion_id, Request $request)
    {
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);

        $tipo_list = array('ACUMULATIVA'=>'ACUMULATIVA','PROMEDIADA'=>'PROMEDIADA');

        $escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.activities.create',compact('pevaluacion','escala_list'));
    }

    public function format($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id);
        $activities = Activity::where('pevaluacion_id',$id)->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('profesors.activities.format',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function resume($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id);
        $activities = Activity::where('pevaluacion_id',$id)->whereNotNull('description')->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('profesors.activities.resume',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function clone($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);

        $pensum = Pensum::findOrFail($pevaluacion->pensum_id);
        $grado = Grado::findOrFail($pensum->grado_id);
        $lapso = Lapso::findOrFail($pevaluacion->lapso_id);
        $seccion = Seccion::findOrFail($pevaluacion->seccion_id);

        $seccion_list = $grado->seccions->where('id','<>',$seccion->id)->pluck('name','id');

        return view('profesors.activities.clone',compact('pevaluacion','pensum','grado','lapso','seccion_list'));
    }

    public function store_clone(Request $request)
    {
        // dd($request->all());
        $pevaluacion = Pevaluacion::findOrFail($request->pevaluacion_id);
        $sección = Pevaluacion::findOrFail($request->pevaluacion_id);
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

}

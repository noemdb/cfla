<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Helpers\Convertidor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

//validation request
use App\Http\Requests\Administracion\CreateUserRequest;
use App\Models\app\Bienestar\StudentRecord;
// use App\Http\Requests\Administracion\UpdateUserRequest;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\Escolaridad;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Estudiante\Programacion;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Inschistory;
use App\Models\app\Pescolar\Preinscripcion;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use App\User;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']); //unregistered
    }

    public function asignar(Request $request)
    {

        $search         = (!empty($request->search)) ? $request->search : null;
        $status_preinscripcion    = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $prosecucion_seccion_id     = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants = Estudiant::select('estudiants.*')
                // ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->where('estudiants.status_active', 'true')
                ->orderBy('estudiants.ci_estudiant')
                ->groupBy('estudiants.id');

            if ($search) {
                $search = $request->get('search');
                $arr_get = ['search' => $search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($grado_id) ? $estudiants->where('grados.id', $grado_id) : $estudiants;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id', $seccion_id) : $estudiants;
            $estudiants = ($prosecucion_seccion_id) ? $estudiants->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')->where('prosecucions.seccion_id', $prosecucion_seccion_id) : $estudiants;
            $estudiants = ($status_preinscripcion == 'SI') ? $estudiants->join('preinscripcions', 'estudiants.id', '=', 'preinscripcions.estudiant_id') : $estudiants;

            $estudiants = $estudiants->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $list_prosecucion = Prosecucion::list_prosecucion(); //dd($list_prosecucion);
        $list_grupo_estable = GrupoEstable::list_grupo_estable_code(); //dd($list_prosecucion);
        $planpago_list = Planpago::active()->pluck('name', 'id');
        return view('administracion.inscripciones.asignar', compact('estudiants', 'planpago_list', 'list_grado', 'list_seccion', 'list_prosecucion', 'list_grupo_estable', 'search', 'grado_id', 'seccion_id', 'status_preinscripcion', 'prosecucion_seccion_id'));
    }

    public function asignarStore(Request $request)
    {

        $search = (!empty($request->search)) ? $request->search : null;
        $req_grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $req_seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $status_preinscripcion = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;
        $prosecucion_seccion_id = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id : null;
        $count = null;

        if (!empty($request->grado_arr) && !empty($request->seccion_arr)) {
            $grado_arr = $request->grado_arr;
            $seccion_arr = $request->seccion_arr;
            if (is_array($grado_arr) && is_array($seccion_arr)) {
                $count = 0;
                foreach ($grado_arr as $estudiant_id => $grado_id) {
                    if ($grado_id && $estudiant_id) {

                        $estudiant = Estudiant::findOrFail($estudiant_id);

                        if (array_key_exists($estudiant->id, $seccion_arr)) {

                            $grado = Grado::findOrFail($grado_id);
                            $seccion = Seccion::where('grado_id', $grado->id)->where('name', $seccion_arr[$estudiant->id])->first();

                            if ($seccion) {
                                $inscripcion = Inscripcion::where('estudiant_id', $estudiant->id)->first();

                                if ($inscripcion) {
                                    $inscripcion->fill(['seccion_id' => $seccion->id, 'user_id' => Auth::user()->id]);
                                    $inscripcion->save();
                                    $count++;
                                } else {
                                    $preinscripcion = Preinscripcion::where('estudiant_id', $estudiant->id)->first();
                                    $grupo_estable_id = ($preinscripcion) ? $preinscripcion->grupo_estable_id : null;
                                    $create = Inscripcion::create([
                                        'estudiant_id' => $estudiant_id,
                                        'seccion_id' => $seccion->id,
                                        'tipo_id' => 2,
                                        'escolaridad_id' => 1,
                                        'programacion_id' => 1,
                                        'grupo_estable_id' => $grupo_estable_id,
                                        'user_id' => Auth::user()->id
                                    ]);
                                    $count++;
                                    $this->fillStudentRecord($estudiant_id);
                                }
                            }
                        }
                    }
                }
            }
        }

        $count_sentence = Convertidor::numToSentence($count);

        $messege = ($count > 1) ? 'Inscripciones guardadas correctamente!!. ' . $count_sentence . ' (' . $count . ') Registros procesados' : 'Inscripción guardada correctamente!!. ' . $count_sentence . ' (' . $count . ') Registro procesado';

        Session::flash('operp_ok', $messege);

        $arr_compact = ['search' => $search, 'grado_id' => $req_grado_id, 'seccion_id' => $req_seccion_id, 'status_preinscripcion' => $status_preinscripcion, 'prosecucion_seccion_id' => $prosecucion_seccion_id];

        return redirect()->route('administracion.inscripciones.asignar', $arr_compact);
        // return redirect()->route('administracion.inscripciones.asignar',['status_preinscripcion'=>$status_preinscripcion,'search'=>$search]);

    }

    public function prosecucion(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $tipo_id = (!empty($request->tipo_id)) ? $request->tipo_id : null;
        $inscripcions = collect();

        if ($request->all()) {

            $inscripcions = Inscripcion::select('inscripcions.*')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->wherenull('grados.deleted_at')
                ->wherenull('seccions.deleted_at');
            $inscripcions = ($grado_id) ? $inscripcions->where('grados.id', $grado_id) : $inscripcions;
            $inscripcions = ($seccion_id) ? $inscripcions->where('seccions.id', $seccion_id) : $inscripcions;
            $inscripcions = ($tipo_id) ? $inscripcions->where('inscripcions.tipo_id', $tipo_id) : $inscripcions;
            $inscripcions = $inscripcions->get();
        }

        /*******************list****************************/
        $list_grado = Grado::where('id', '<', 6)->pluck('name', 'id');
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $list_tinscripcion = Tinscripcion::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');

        return view('administracion.inscripciones.prosecucion', compact('inscripcions', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion', 'tipo_id', 'list_tinscripcion'));
    }

    public function movement(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $lastdate = (!empty($request->lastdate)) ? $request->lastdate : null;
        $estudiants_retiros = collect();
        $inscripcions = collect();

        if ($request->all()) {
            $inscripcions = Inscripcion::select('inscripcions.*')
                ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')

                ->where('seccions.status_active', 'true')
                ->where('estudiants.status_active', 'true')

                ->wherenull('estudiants.deleted_at')
                ->wherenull('grados.deleted_at')
                ->wherenull('seccions.deleted_at');

            $inscripcions = ($grado_id) ? $inscripcions->where('grados.id', $grado_id) : $inscripcions;
            $inscripcions = ($seccion_id) ? $inscripcions->where('seccions.id', $seccion_id) : $inscripcions;

            $inscripcions = $inscripcions->get();

            $estudiants_retiros = Estudiant::select('estudiants.*')
                ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->whereNull('inscripcions.id')
                ->where('retiros.tipo', 'control');

            $estudiants_retiros = ($lastdate) ? $estudiants_retiros->whereDate('retiros.created_at', '>=', $lastdate) : $estudiants_retiros;

            $estudiants_retiros = $estudiants_retiros->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();

        $compact = array('inscripcions', 'estudiants_retiros', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion', 'lastdate');

        return view('administracion.inscripciones.movement', compact($compact));
    }

    public function crud(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active : 'true';
        $tipo_id = (!empty($request->tipo_id)) ? $request->tipo_id : null;
        $escolaridad_id = (!empty($request->escolaridad_id)) ? $request->escolaridad_id : null;
        $grupo_estable_id = (!empty($request->grupo_estable_id)) ? $request->grupo_estable_id : null;
        $fecha = (!empty($request->fecha)) ? $request->fecha : null;
        $inscripcions = collect();

        if ($request->all()) {

            $inscripcions = Inscripcion::select('inscripcions.*')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftJoin('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
                ->where('seccions.status_active', 'true')
                ->wherenull('retiros.id')
                ->wherenull('grados.deleted_at')
                ->wherenull('seccions.deleted_at');

            $inscripcions = ($grado_id) ? $inscripcions->where('grados.id', $grado_id)->where('seccions.status_active', 'true') : $inscripcions;
            $inscripcions = ($seccion_id) ? $inscripcions->where('seccions.id', $seccion_id)->where('seccions.status_active', 'true') : $inscripcions;
            $inscripcions = ($tipo_id) ? $inscripcions->where('inscripcions.tipo_id', $tipo_id) : $inscripcions;
            $inscripcions = ($escolaridad_id) ? $inscripcions->where('inscripcions.escolaridad_id', $escolaridad_id) : $inscripcions;
            $inscripcions = ($grupo_estable_id) ? $inscripcions->where('inscripcions.grupo_estable_id', $grupo_estable_id) : $inscripcions;
            $inscripcions = ($fecha) ? $inscripcions->where('inscripcions.created_at','<=', $fecha) : $inscripcions;

            $inscripcions = ($status_active == 'true') ? $inscripcions->where('seccions.status_active', 'true') : $inscripcions;
            $inscripcions = ($status_active == 'false') ? $inscripcions->where('seccions.status_active', 'false') : $inscripcions;

            $inscripcions = $inscripcions->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $list_escolaridad = Escolaridad::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_tinscripcion = Tinscripcion::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_grupo_estables = GrupoEstable::list_grupo_estable_full_inscriptions();

        return view(
            'administracion.inscripciones.crud',
            compact(
                'inscripcions',
                'grado_id',
                'list_grado',
                'seccion_id',
                'status_active',
                'list_seccion',
                'list_escolaridad',
                'list_tinscripcion',
                'list_grupo_estables',
                'escolaridad_id',
                'tipo_id',
                'fecha',
                'grupo_estable_id'
            )
        );
    }

    public function retiro(Request $request)
    {
        $search         = (!empty($request->search)) ? $request->search : null;
        $planpago_id       = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                // ->where('estudiants.status_active','true')
            ;

            if ($search) {
                $search = $request->get('search');
                $arr_get = ['search' => $search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($grado_id) ? $estudiants->where('grados.id', $grado_id) : $estudiants;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id', $seccion_id) : $estudiants;
            $estudiants = ($planpago_id) ? $estudiants->where('inscripcions.planpago_id', $planpago_id) : $estudiants;

            $estudiants = $estudiants->get();
        }

        /*******************list****************************/
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $planpago_list = Planpago::select('name', 'id')->where('status_active', 'true')->orderby('id', 'asc')->pluck('name', 'id');

        return view('administracion.inscripciones.retiro', compact('estudiants', 'search', 'planpago_list', 'list_grado', 'list_seccion', 'planpago_id', 'grado_id', 'seccion_id'));
    }

    public function matricula_inicial()
    {
        // $list_pescolar = Pescolar::select('pescolars.*')
        //     ->orderby('pescolars.name','asc')
        //     ->pluck('name', 'id');
        $list_Pestudio = Pestudio::select('id', DB::raw("CONCAT(code,' - ',name) as fullname"))->orderby('name', 'asc')->where('status_active', 'true')->pluck('fullname', 'id');
        $list_grados = Grado::select('id', 'name')->orderby('name', 'asc')->where('status_active', 'true')->pluck('name', 'id');

        // dd($list_pescolar);

        return view('administracion.inscripciones.matricula.inicial', compact('list_Pestudio', 'list_grados'));
    }
    public function listview()
    {
        $list_pescolar = Pescolar::select('pescolars.*')->orderby('pescolars.name', 'asc')->pluck('name', 'id');

        $list_grados = Grado::select('grados.*')->orderby('grados.name', 'asc')->pluck('name', 'id');

        return view('administracion.inscripciones.list.view', compact('list_grados', 'list_pescolar'));
    }

    public function book(Request $request)
    {
        $list_pescolar = Pescolar::select('pescolars.*')
            ->orderby('pescolars.name', 'asc')
            ->pluck('name', 'id');

        $pescolar_id = ($request->get('pescolar_id')) ? $request->get('pescolar_id') : Session::get('pescolar_id');

        $pestudios = Pestudio::Orderby('order', 'asc')->where('status_active', 'true')->get();
        $grados = Grado::Orderby('id', 'asc')->where('status_active', 'true')->get();
        $seccions = Seccion::Orderby('id', 'asc')->get();
        $tinscripcions = Tinscripcion::Orderby('id', 'asc')->get();
        $std_ciaca_siadm = Inscripcion::std_ciaca_siadm(); //dd($std_ciaca_siadm);
        $std_siaca_ciadm = Administrativa::std_siaca_ciadm();

        $arrMonths = arrMonths();
        return view('administracion.inscripciones.book', compact('std_ciaca_siadm', 'std_siaca_ciadm', 'pescolar_id', 'list_pescolar', 'pestudios', 'grados', 'seccions', 'tinscripcions', 'arrMonths'));
    }

    public function index(Request $request)
    {
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = ['search' => $search];

            $inscripcions = Inscripcion::select('inscripcions.id', 'inscripcions.seccion_id as seccion_id', 'inscripcions.estudiant_id', 'estudiants.name', 'estudiants.lastname', 'estudiants.ci_estudiant', 'estudiants.representant_ci', 'grados.name as grados_name', 'seccions.name as seccions_name')
                ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->name($arr_get)
                ->OrderBy('inscripcions.id', 'desc')
                ->get();

            return view('administracion.inscripciones.index', compact('inscripcions', 'search'));
        } else {
            $search = '';
            return view('administracion.inscripciones.index', compact('search'));
        }
    }

    public function individual(Request $request)
    {
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = ['search' => $search];
            $arr_inscripcion = Inscripcion::select('inscripcions.estudiant_id')->get()->toArray();
            // dd($arr_inscripcion);

            $estudiants = Estudiant::name($arr_get)
                // ->OrderBy('created_at', 'desc')
                ->OrderBy('estudiants.id', 'asc')
                ->whereNotIn('estudiants.id', function ($q) {
                    $q->select('inscripcions.estudiant_id')->from('inscripcions');
                })
                // ->whereNotIn('id',$arr_inscripcion)
                ->get();

            return view('administracion.inscripciones.individual', compact('estudiants', 'search'));
        } else {
            $search = '';
            return view('administracion.inscripciones.individual', compact('search'));
        }
    }

    public function batchs()
    {
        return view('administracion.inscripciones.batchs');
    }

    public function dashboard()
    {
        return view('administracion.inscripciones.create');
    }

    public function edit($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $grado_id = $inscripcion->seccion->grado->id;
        $estudiant = Estudiant::findOrFail($inscripcion->estudiant_id);

        $list_grado = Grado::list_pestudio_grado();

        $grado_id = $estudiant->inscripcion->seccion->grado->id;
        $list_seccion = Seccion::select('name', 'id')
            ->where('grado_id', $grado_id)
            ->orderby('name', 'asc')
            ->pluck('name', 'id');

        $list_tinscripcion = Tinscripcion::select('name', 'id')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');

        $list_escolaridad = Escolaridad::select('name', 'id')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');

        $list_programacion = Programacion::select('description', 'id')
            ->orderby('id', 'asc')
            ->pluck('description', 'id');

        $list_grupo_estables = GrupoEstable::select('id', 'name', DB::raw("CONCAT(name,' || ',code) as fullname"))->orderby('name', 'asc')->pluck('fullname', 'id');

        return view('administracion.inscripciones.edit', compact('inscripcion', 'estudiant', 'list_tinscripcion', 'list_grado', 'list_seccion', 'grado_id', 'list_escolaridad', 'list_programacion', 'list_grupo_estables'));
    }

    public function update(Request $request, $id)
    {
        $inscripcion = Inscripcion::findOrFail($id);

        $inscripcion->fill($request->all());

        $inscripcion->save();

        $this->fillStudentRecord($inscripcion->estudiant_id);

        $grupo_estable = ($inscripcion->grupo_estable) ? $inscripcion->grupo_estable->name : null;

        $messenge = trans('db_oper_result.update_ok');

        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "grupo_estable" => $grupo_estable,
            ]);
        }

        Session::flash('operp_ok', $messenge);

        Session::flash('class_oper', 'success');

        // return redirect()->route('administracion.inscripciones.edit',$id);
        return redirect()->route('administracion.inscripciones.crud');
    }

    public function create($id)
    {
        $estudiant = Estudiant::findOrFail($id);
        $grado_id = '';

        $list_grado = Grado::list_pestudio_grado();

        $list_escolaridad = Escolaridad::select('name', 'id')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');

        $list_programacion = Programacion::select('description', 'id')
            ->orderby('id', 'asc')
            ->pluck('description', 'id');

        // $list_seccion = Seccion::select('seccions.name','seccions.id', DB::raw("CONCAT(grados.name,' - ',seccions.name) as fullname"))
        //     ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        //     ->orderby('grados.name','asc')
        //     ->pluck('fullname', 'id');
        $list_seccion = ['Seleccione un grado' => 'Seleccione un grado'];

        $list_tinscripcion = Tinscripcion::select('name', 'id')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');

        $list_grupo_estables = GrupoEstable::select('id', 'name', DB::raw("CONCAT(name,' || ',code) as fullname"))
            ->orderby('name', 'asc')
            ->pluck('fullname', 'id');

        return view('administracion.inscripciones.create', compact('estudiant', 'list_tinscripcion', 'list_grado', 'list_seccion', 'grado_id', 'list_escolaridad', 'list_programacion', 'list_grupo_estables'));
    }

    public function store(Request $request)
    {
        $estudiant = Estudiant::findOrFail($request->estudiant_id);
        $inscripcion = $estudiant->inscripcion;
        $search = $estudiant->ci_estudiant;
        if ($inscripcion) {
            $inscripcion->fill($request->all());
            $inscripcion->save();
        } else {
            $snscripcion = Inscripcion::create($request->all());
        }

        $administrativa = Administrativa::where('estudiant_id', $estudiant->id)->first();
        if (!$administrativa) {
            $create = Administrativa::create([
                'estudiant_id' => $estudiant->id,
                'planpago_id' => 1,
                'user_id' => Auth::id(),
            ]);
        }

        $this->fillStudentRecord($estudiant->id);

        Session::flash('operp_ok', 'Registro guardado exitosamente');
        return redirect()->route('administracion.inscripciones.index', compact('search'));
    }

    public function gradoByseccion($id)
    {
        return Seccion::where('grado_id', '=', $id)->get();
    }

    public function destroy($id, Request $request)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        Session::flash('operp_ok', $messenge);
        return redirect()->route('administracion.inscripcions.crud');
    }

    public function fillStudentRecord($estudiant_id)
    {
        $estudiant = Estudiant::find($estudiant_id);
        if ($estudiant) {
            $enrollment = Enrollment::where('ci_estudiant', $estudiant->ci_estudiant)->first();
            if ($enrollment) {
                $enrollment->estudiant_id = $estudiant->id;
                $enrollment->save();
                $student_record = StudentRecord::firstOrCreate(
                    ['estudiant_id' => $estudiant->id],
                    $enrollment->toArray()
                );
                return $student_record;
            }
        }
    }

    public function unregistered(Request $request)
    {

        $search         = (!empty($request->search)) ? $request->search : null;
        $status_prosecucion    = (!empty($request->status_prosecucion)) ? $request->status_prosecucion : null;
        $prosecucion_seccion_id     = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id : null;
        $estudiants = collect();

        if (count($request->all()) > 0) {

            $estudiants = $estudiants = Estudiant::select('estudiants.*');

            if ($prosecucion_seccion_id || $status_prosecucion == 'SI') {

                $estudiants = $estudiants
                    ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
                    ->whereNull('inscripcions.id');

                if ($prosecucion_seccion_id) {
                    $estudiants = $estudiants->where('prosecucions.seccion_id', $prosecucion_seccion_id);
                }
            }

            // ✅ Filtro de búsqueda: name o lastname
            if ($search) {
                $estudiants = $estudiants->where(function ($query) use ($search) {
                    $query->where('estudiants.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('estudiants.lastname', 'LIKE', '%' . $search . '%');
                });
            }

            $estudiants = $estudiants->get();
        }

        $list_prosecucion = Prosecucion::list_prosecucion(); //dd($list_prosecucion);

        return view('administracion.inscripciones.unregistered', compact('estudiants', 'status_prosecucion', 'search', 'list_prosecucion', 'prosecucion_seccion_id'));
    }

    public function register(Request $request)
    {
        $user = User::find(Auth::id());
        $estudiant = Estudiant::findOrFail($request->id);
        Prosecucion::create([
            'seccion_id' => 1,
            'estudiant_id' => $estudiant->id,
            'observations' => 'AGREGADO POR: ' . ($user) ? $user->username : null,
        ]);
        return redirect()->route('administracion.inscripciones.unregistered');
    }
}

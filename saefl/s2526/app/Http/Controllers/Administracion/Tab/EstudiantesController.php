<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Estudiant\CreateEstudiantRequest;
use App\Http\Requests\Administracion\Estudiant\UpdateEstudiantRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Estudiante\TypeCi;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\RepresentanteTest;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;

class EstudiantesController extends Controller
{

    public function pases(Request $request)
    {
        return view('administracion.pases.index');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pagos(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $status_active = (!empty($request->status_active)) ? $request->status_active : null;
        $count_estudiants = (!empty($request->count_estudiants)) ? $request->count_estudiants : null;
        $num_cuotas = (!empty($request->num_cuotas)) ? $request->num_cuotas : null;

        $estudiants = collect();
        $deuda_total = null;
        $deuda_total_ex = null;
        $deuda_total_bs = null;
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];

        if (count($request->all()) > 0) {
            $estudiants =
                Estudiant::query()
                ->select('estudiants.*')
                ->selectRaw('count(estudiants.id) as count_estudiants')
                ->leftjoin('representants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderBy('representants.name')
                ->groupBy('representants.id');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id', $grado_id) : $estudiants;
            $estudiants = (isset($seccion_id)) ? $estudiants->where('seccions.id', $seccion_id) : $estudiants;

            $estudiants = ($formally == 'SI') ? $estudiants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $estudiants;
            $estudiants = ($formally == 'NO') ? $estudiants->whereNull('inscripcions.id') : $estudiants;

            $estudiants = ($status_inscription_affects) ? $estudiants->where('planpagos.status_inscription_affects', $status_inscription_affects) : $estudiants;
            $estudiants = ($status_active) ? $estudiants->where('seccions.status_active', $status_active) : $estudiants;

            $estudiants = (isset($count_estudiants)) ? $estudiants->havingRaw('count(estudiants.id) = ?', [$count_estudiants]) : $estudiants;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        $compact = [
            'status_inscription_affects',
            'status_active',
            'estudiants',
            'deuda_total_bs',
            'deuda_total_ex',
            'deuda_bs_arr',
            'deuda_ex_arr',
            'planpago_list',
            'formally',
            'planpago_id',
            'grado_id',
            'list_grado',
            'seccion_id',
            'list_seccion',
            'count_estudiants',
            'num_cuotas',
        ];

        return view('administracion.estudiants.pagos', compact($compact));
    }

    public function blacklist(Request $request)
    {
        $estudiants = collect();
        $search = ($request->has('search')) ? $request->get('search') : null;
        if ($request->has('search')) {
            $arr_get = ['search' => $search];
            $estudiants = Estudiant::select('estudiants.*')
                ->where('estudiants.status_blacklist', 'true')
                ->where(function ($query) use ($search) {
                    $query->where('estudiants.ci_estudiant', 'like', "%" . $search . "%")
                        ->orWhere(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%" . $search . "%");
                })
                ->get();
        }
        return view('administracion.estudiants.blacklist', compact('estudiants', 'search'));
    }


    public function historico(Request $request)
    {

        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null;
        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null;

        $estudiant = Estudiant::where('id', $estudiant_id)->first();
        $registropagos = RegistroPago::where('estudiant_id', $estudiant_id)
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->where('pagos.pagos_ammount', '>', 0)
            ->get();

        $list_estudiant = Estudiant::select('id', DB::raw("CONCAT(ci_estudiant,' - ',lastname,' ',name) as estudian_fullname"))
            ->where('status_active', 'true')
            ->orderby('ci_estudiant', 'asc')
            ->pluck('estudian_fullname', 'id');

        // dd($list_estudiant->toarray());

        return view('administracion.estudiants.historico', compact('estudiant_id', 'help_estudiant', 'estudiant', 'registropagos', 'list_estudiant'));
    }

    public function dashboard() {}

    public function saldos(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;

        $estudiants = collect();
        $deuda_total = null;
        $deuda_no_total = null;
        $deuda_arr = [];

        $deuda_total = null;
        $deuda_total_ex = null;
        $deuda_total_bs = null;
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];

        if (count($request->all()) > 0) {

            $estudiants =
                Estudiant::select('estudiants.*', 'seccions.name as seccion_name', 'grados.name as grado_name')
                ->orderby('estudiants.lastname')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->orderBy('estudiants.lastname');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id', $grado_id) : $estudiants;
            $estudiants = (isset($seccion_id)) ? $estudiants->where('seccions.id', $seccion_id) : $estudiants;
            $estudiants = (isset($planpago_id) && isset($planpago_id)) ? $estudiants->where('administrativas.planpago_id', $planpago_id) : $estudiants;

            $estudiants = $estudiants->active('true')->get();


            foreach ($estudiants as $estudiant) {
                // $ammount_expire_bill = $estudiant->ammount_expire_bill;
                // if ($ammount_expire_bill > 0) {
                //     $deuda_total = $deuda_total + $ammount_expire_bill;
                //     $deuda_arr[$estudiant->id] = $ammount_expire_bill;
                // }

                $bs_exchange_ammount_expire_bill = $estudiant->bs_exchange_ammount_expire_bill;
                $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
                if ($exchange_ammount_expire_bill > 0) {
                    $deuda_total_ex = $deuda_total_ex + $exchange_ammount_expire_bill;
                    $deuda_total_bs = $deuda_total_bs + $bs_exchange_ammount_expire_bill;
                    $deuda_bs_arr[$estudiant->id] = $bs_exchange_ammount_expire_bill;
                    $deuda_ex_arr[$estudiant->id] = $exchange_ammount_expire_bill;
                }
            }
        }

        //dd($deuda_total_ex,$deuda_total_bs,$deuda_bs_arr,$deuda_ex_arr);

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        return view(
            'administracion.estudiants.saldos',
            compact('estudiants', 'deuda_total_bs', 'deuda_total_ex', 'deuda_bs_arr', 'deuda_ex_arr', 'planpago_id', 'planpago_list', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion')
        );
    }

    public function crud(Request $request)
    {

        $ci_estudiant = (!empty($request->ci_estudiant)) ? $request->ci_estudiant : null;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $seccion_status_active = (!empty($request->seccion_status_active)) ? $request->seccion_status_active : null; //dd($status_active);
        //$status_prosecution = (!empty($request->status_prosecution)) ? $request->status_prosecution:null; //dd($status_active);

        $status_prosecution = (isset($request->status_prosecution)) ? $request->status_prosecution : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants =
                Estudiant::select('estudiants.*')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                // ->whereNull('inscripcions.deleted_at')
                // ->whereNull('administrativas.deleted_at')
                // ->whereNull('seccions.deleted_at')
                // ->whereNull('grados.deleted_at')
                // ->where('estudiants.status_active','true')
                ->groupby('estudiants.id');
            // ->get();

            $estudiants = (isset($ci_estudiant)) ? $estudiants->where('estudiants.ci_estudiant', $ci_estudiant) : $estudiants;

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id', $grado_id)->where('seccions.status_active', 'true') : $estudiants;
            $estudiants = (isset($seccion_id) && isset($seccion_id)) ? $estudiants->where('seccions.id', $seccion_id)->where('seccions.status_active', 'true') : $estudiants;
            $estudiants = (isset($planpago_id) && isset($planpago_id)) ? $estudiants->where('administrativas.planpago_id', $planpago_id) : $estudiants;
            $estudiants = ($status_active) ? $estudiants->where('estudiants.status_active', $status_active) : $estudiants;

            $estudiants = ($formally == 'SI') ? $estudiants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $estudiants;
            $estudiants = ($formally == 'NO') ? $estudiants->whereNull('inscripcions.id') : $estudiants;

            $estudiants = ($status_inscription_affects) ? $estudiants->where('planpagos.status_inscription_affects', $status_inscription_affects) : $estudiants;
            $estudiants = ($seccion_status_active) ? $estudiants->where('seccions.status_active', $seccion_status_active) : $estudiants;

            if (isset($status_prosecution)) {
                $estudiants = $estudiants
                    ->where('estudiants.status_prosecution', $status_prosecution)
                    ->whereNotNull('date_prosecution');
            }

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

        return view('administracion.estudiants.crud', compact('ci_estudiant', 'status_inscription_affects', 'seccion_status_active', 'estudiants', 'list_grado', 'list_seccion', 'planpago_list', 'grado_id', 'seccion_id', 'planpago_id', 'status_active', 'formally', 'status_prosecution'));
    }

    public function retirar(Request $request)
    {
        $estudiants = Estudiant::all();

        return view('administracion.estudiants.retirar', compact('estudiants'));
    }

    public function set_retirar($id, Request $request)
    {
        $retiro = Retiro::where('estudiant_id', $id)->first();
        $user = User::find(Auth::id());
        if (empty($retiro->id)) {
            $retiro = Retiro::create([
                'estudiant_id' => $id,
                'user_id' => $user->id,
            ]);
        }

        $estudiant =  Estudiant::findOrFail($id);

        if (!empty($estudiant->inscripcion->id) && $user->isControl()) {
            $inscripcion = Inscripcion::findOrFail($estudiant->inscripcion->id);
            $inscripcion->delete();
            $retiro->update(['tipo' => 'control']);
        }
        if (!empty($estudiant->administrativa->id) && $user->isAdmon()) {
            $administrativa = Administrativa::findOrFail($estudiant->administrativa->id);
            $administrativa->delete();
            $estudiant->update(['status_active' => 'false']);
            $retiro->update(['tipo' => 'admon']);
        }

        DB::commit();

        $title = 'Retirado el: ' . $retiro->created_at;

        if ($request->ajax()) {
            return response()->json([
                "messenge" => 'Estudiante retirado exitosamente.',
                "operation" => 'operp_ok',
                "div" => '
                <a title="' . $title . '" class="btn btn-secondary btn-sm disabled" href="#">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </a>',
            ]);
        }
    }

    public function index(Request $request)
    {
        if ($request->get('search')) {
            $search = $request->get('search');
            $arr_get = [
                'search' => $search,
            ];

            $estudiants = Estudiant::name($arr_get)->active('true')->OrderBy('estudiants.created_at', 'desc')->get();

            return view('administracion.estudiants.index', compact('estudiants', 'search'));
        } else {
            $search = '';
            return view('administracion.estudiants.index', compact('search'));
        }
    }

    public function edit(Request $request)
    {
        if (!empty($request->id)) {
            $estudiant = Estudiant::findOrFail($request->id);
        } else {
            if (!empty($request->ci_estudiant)) {
                $estudiant = Estudiant::where('ci_estudiant', $request->ci_estudiant)->first();
            } else {
                return 'sin id ni cédula';
            }
        }

        $seccion = $estudiant->seccion;
        $pevaluacions = $estudiant->pevaluacions;
        $pevaluacionsOrigin = Pevaluacion::where('seccion_id', 22)->get();
        $pevaluacionsTarget = Pevaluacion::where('seccion_id', 21)->get();

        $datas = collect(new Boletin);

        $boletins = $estudiant->boletins; //dd($boletins);
        $boletinsCopy = $estudiant->boletins; //dd($boletins);

        // $user_id = Auth::id();

        // if ($user_id==1) {
        //     foreach ($boletins as $boletin) {
        //         //dd($boletin);
        //         $evaluacionStart = $boletin->evaluacion; //dd($evaluacionStart);

        //         $lapso = $evaluacionStart->lapso; //dd($evaluacionStart,$evaluacionStart->pevaluacion,$lapso);

        //         $evaluacionEnd = Evaluacion::select('evaluacions.*')
        //             ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        //             ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        //             ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
        //             ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        //             ->where('grados.id',$estudiant->grado->id)
        //             ->where('lapsos.id',$lapso->id)
        //             ->where('evaluacions.description',$evaluacionStart->description)
        //             ->where('evaluacions.id','<>',$evaluacionStart->id)
        //             ->first(); //dd($evaluacionStart,$evaluacionEnd);

        //         $boletin->evaluacion_id = ($evaluacionEnd) ? $evaluacionEnd->id : $boletin->evaluacion_id ;

        //         $boletin->save(); //dd($evaluacionStart,$boletin);

        //         //$datas->push($boletin);

        //         //dd($evaluacionStart,$evaluacionEnd,$boletins->take(5),$boletinsCopy->take(5),$datas->take(5));

        //     }
        // }

        //dd($boletins,$pevaluacions,$pevaluacionsOrigin,$pevaluacionsTarget,$seccion,$estudiant);

        $list_type_ci = TypeCi::select('name', 'id')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');

        $list_country_birth = Estudiant::list_country_birth();
        $list_representant = Representant::list_representant();
        $list_comment = Estudiant::COLUMN_COMMENTS;

        return view('administracion.estudiants.edit', compact('list_type_ci', 'estudiant', 'list_representant', 'list_country_birth', 'list_comment'));
    }

    public function update(UpdateEstudiantRequest $request, $id)
    {
        // dd($request->all());
        $arr_dat = $request->all();
        unset($arr_dat['help_representante']);
        $estudiant = Estudiant::findOrFail($id);
        $search = $estudiant->ci_estudiant;
        $estudiant->fill($arr_dat);
        $estudiant->save();
        // $messenge = trans('db_oper_result.user_update_ok');
        Session::flash('operp_ok', 'Registro actualizado exitosamente');
        Session::flash('class_oper', 'success');
        return redirect()->route('administracion.estudiants.index', compact('search'));
    }

    public function create()
    {
        $list_type_ci = TypeCi::select('name', 'id')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');

        $list_country_birth = Estudiant::list_country_birth();
        $list_representant = Representant::list_representant();
        $list_comment = Estudiant::COLUMN_COMMENTS;

        return view('administracion.estudiants.create', compact('list_type_ci', 'list_representant', 'list_country_birth', 'list_comment'));
    }

    public function store(CreateEstudiantRequest $request)
    {
        // dd($request->all());
        $arr_dat = $request->all();
        unset($arr_dat['help_representante']);
        $estudiant = Estudiant::create($arr_dat);
        $search = $estudiant->ci_estudiant;
        Session::flash('operp_ok', 'Registro guardado exitosamente');
        return redirect()->route('administracion.estudiants.index', compact('search'));
    }

    public function destroy($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);
        $estudiant_fullname = $estudiant->fullname;

        $inscripcion = Inscripcion::Where('estudiant_id', $id)->first();
        if ($inscripcion) $inscripcion->delete();

        $administrativa = Administrativa::Where('estudiant_id', $id)->first();
        if ($administrativa) $administrativa->delete();

        $estudiant->desactive();
        $estudiant->delete();

        $messenge = trans('db_oper_result.delete_ok') . ' - ' . $estudiant_fullname . ' eliminado.';
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        Session::flash('operp_ok', $messenge);
        return redirect()->route('administracion.estudiants.index');
    }
}

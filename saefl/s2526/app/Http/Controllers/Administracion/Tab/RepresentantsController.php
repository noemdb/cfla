<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Estudiant\CreateRepresentantRequest;
use App\Http\Requests\Administracion\Estudiant\UpdateRepresentantRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

use App\Http\Controllers\Admin\Fix\DB\HomeController as FixDBControlller;
use App\Models\app\Planpago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\User;

use App\Http\Controllers\Admin\FixDB\CreateUserDB;
use Illuminate\Support\Facades\Validator;

class RepresentantsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function auditorias(Request $request)
    {
        $ci_representant            = (!empty($request->ci_representant)) ? $request->ci_representant : null;
        $grado_id                   = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id                 = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $formally                   = (!empty($request->formally)) ? $request->formally : null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $status_active              = (!empty($request->status_active)) ? $request->status_active : null;

        $representants = collect();

        if (count($request->all()) > 0) {
            $representants = Representant::query()
                ->select('representants.*')
                ->leftjoin('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderBy('representants.name')
                ->groupBy('representants.id');

            $representants = (!empty($ci_representant))
            ? $representants->where('representants.ci_representant', 'LIKE', "%$ci_representant%")
            : $representants;

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;

            $representants = ($status_inscription_affects) ? $representants->where('planpagos.status_inscription_affects', $status_inscription_affects) : $representants;
            $representants = ($status_active) ? $representants->where('seccions.status_active', $status_active) : $representants;

            $representants = $representants->get();
        }

        $list_grado    = Grado::list_pestudio_grado();
        $list_seccion  = Seccion::list_seccion_grado($grado_id);
        $pescolar = Pescolar::first();

        $compact = [
            'pescolar',
            'representants',
            'ci_representant',
            'grado_id',
            'seccion_id',
            'list_grado',
            'list_seccion',
            'formally',
            'status_inscription_affects',
            'status_active',
        ];

        return view('administracion.representants.auditorias', compact($compact));
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

        $representants = collect();
        $deuda_total = null;
        $deuda_total_ex = null;
        $deuda_total_bs = null;
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];

        if (count($request->all()) > 0) {
            $representants =
                Representant::query()
                ->select('representants.*', 'estudiants.ci_estudiant')
                ->selectRaw('count(estudiants.id) as count_estudiants')
                ->leftjoin('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderBy('representants.name')
                ->groupBy('representants.id');

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;

            $representants = ($status_inscription_affects) ? $representants->where('planpagos.status_inscription_affects', $status_inscription_affects) : $representants;
            $representants = ($status_active) ? $representants->where('seccions.status_active', $status_active) : $representants;

            $representants = (isset($count_estudiants)) ? $representants->havingRaw('count(estudiants.id) = ?', [$count_estudiants]) : $representants;

            $representants = $representants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        $compact = [
            'status_inscription_affects',
            'status_active',
            'representants',
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

        return view('administracion.representants.pagos', compact($compact));
    }

    public function blacklist(Request $request)
    {
        $representants = collect();
        $search = ($request->has('search')) ? $request->get('search') : null;
        if ($request->has('search')) {
            $representants = Representant::select('representants.*')
                ->where('representants.status_blacklist', 'true')
                ->where(function ($query) use ($search) {
                    $query->where('representants.ci_representant', 'like', "%" . $search . "%")
                        ->orWhere('representants.name', 'like', "%" . $search . "%");
                })
                ->get();
        }
        return view('administracion.representants.blacklist', compact('representants', 'search'));
    }

    public function dashboard() {}

    public function historico(Request $request)
    {
        $ci_representant = (!empty($request->ci_representant)) ? $request->ci_representant : null;
        $representant_id = (!empty($request->representant_id)) ? $request->representant_id : null;
        $help_representante = (!empty($request->help_representante)) ? $request->help_representante : null;

        // $fix_zero = new FixDBControlller;
        // $fix_zero->fix_paid_zero();

        $representant = null;
        $registropagos = collect();
        $pago_combinados = collect();

        if ($representant_id) {

            $representant = Representant::findOrFail($representant_id);
            // dd($representant);
            // $representant->fix_registro_pagos();
            // $representant->fix_registro_pagos_zero();

            $registropagos = RegistroPago::where('representant_id', $representant_id)->get();
            $pago_combinados = RegistroPagoCombinado::select('registro_pago_combinados.*', DB::raw("DATE(registro_pagos.created_at) as date"))
                ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
                ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                ->where('registro_pago_combinados.representant_id', $representant_id)
                ->where('pagos.pagos_ammount', '>', 0)
                ->wherenull('registro_pagos.deleted_at')
                ->wherenull('pagos.deleted_at')
                // ->groupby('date','registro_pago_combinados.representant_id')
                ->groupby('registro_pago_combinados.id')
                ->orderby('registro_pago_combinados.created_at', 'desc')
                ->get();
        }

        $list_representant = Representant::list_representant();

        $compact = ['ci_representant', 'representant_id', 'help_representante', 'list_representant', 'representant', 'registropagos', 'pago_combinados'];

        return view('administracion.representants.historico', compact($compact));
    }

    public function historico_index(Request $request)
    {
        $representants = Representant::all();
        return view('administracion.representants.historico_index', compact('representants'));
    }

    public function book(Request $request)
    {
        $list_grados = Grado::select('grados.*')->orderby('grados.name', 'asc')->pluck('name', 'id');

        return view('administracion.representants.book', compact('list_grados'));
    }

    public function crud(Request $request)
    {

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $status_active = (!empty($request->status_active)) ? $request->status_active : null;
        $count_estudiants = (!empty($request->count_estudiants)) ? $request->count_estudiants : null;
        $num_cuotas = (!empty($request->count_estunum_cuotasiants)) ? $request->num_cuotas : null;

        $representants = collect();

        if (count($request->all()) > 0) {

            $representants =  Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->leftjoin('grados', 'seccions.grado_id', '=', 'grados.id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->Where('seccions.status_active', '=', 'true')
                ->Where('grados.status_active', '=', 'true')
                ->Where('estudiants.status_active', '=', 'true')
                ->groupBy('representants.id')
                // ->where('seccions.status_inscription_affects','true')
                // ->where('planpagos.status_inscription_affects','true')
            ;

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($grado_id) && isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('seccions.status_inscription_affects', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;

            $representants = ($status_inscription_affects) ? $representants->where('planpagos.status_inscription_affects', $status_inscription_affects)->where('seccions.status_inscription_affects', 'true') : $representants;
            $representants = ($status_active) ? $representants->where('seccions.status_active', $status_active) : $representants;

            $representants = $representants->get(); //dd($representants);

        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        return view('administracion.representants.crud', compact('status_inscription_affects', 'count_estudiants', 'num_cuotas', 'status_active', 'representants', 'formally', 'grado_id', 'list_grado', 'list_seccion', 'seccion_id', 'planpago_list', 'planpago_id'));
    }

    public function saldosDate(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $date = (!empty($request->date)) ? $request->date : null;

        $representants = collect();
        $deuda_total = null;
        $deuda_total_ex = null;
        $deuda_total_ex_date = null;
        $deuda_total_bs = null;
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];
        $deuda_ex_arr_date = [];

        if (count($request->all()) > 0) {
            $representants =
                Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderby('representants.name')
                ->groupby('representants.id'); //dd($representants);

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;


            $representants = $representants->get(); //dd($representants);

            foreach ($representants as $representant) {
                $exchange_ammount_expire_bill_date = $representant->getExchangeAmmountExpireBillDate($date, true);
                $exchange_ammount_round = round($exchange_ammount_expire_bill_date, 2);
                if ($exchange_ammount_round >= 0.01) {
                    $deuda_total_ex_date = $deuda_total_ex_date + $exchange_ammount_expire_bill_date;
                    $deuda_ex_arr_date[$representant->id] = $exchange_ammount_expire_bill_date;
                }
            }
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_date_last(); //dd($list_cuentaxpagar);
        $compact = ['representants', 'deuda_total_bs', 'deuda_total_ex', 'deuda_ex_arr_date', 'deuda_bs_arr', 'deuda_ex_arr', 'deuda_total_ex_date', 'planpago_list', 'formally', 'planpago_id', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion', 'date', 'list_cuentaxpagar'];
        return view('administracion.representants.saldosDate', compact($compact));
    }

    public function saldosDateRefactor(Request $request)
    {
        $grado_id = $request->input('grado_id');
        $seccion_id = $request->input('seccion_id');
        $formally = $request->input('formally');

        $mensualidad = $request->input('mensualidad');
        $date_start = null;
        $date_end = null;

        if ($mensualidad) {
            $date = Carbon::parse($mensualidad);
            $date_start = $date->copy()->startOfMonth()->format('Y-m-d');
            $date_end = $date->copy()->endOfMonth()->format('Y-m-d');
        }

        $representants = collect();

        // VARIABLES INICIALIZADAS (Necesarias para compact())
        // ----------------------------------------------------
        $deuda_total = null; // Variable sin usar, pero se mantiene si es requerida por otra parte

        // Variables de deuda en dólares (exchange) que sí se usan:
        $deuda_total_ex = null; // No se usa en la lógica actual, pero se define
        $deuda_total_ex_date = 0; // Se inicializa a cero para sumar

        // Variables de deuda en Bs (base) que no se usan en la lógica de cálculo actual,
        // pero deben definirse para evitar el error de compact():
        $deuda_total_bs = 0; // <<-- SOLUCIÓN: Asegurar la definición
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];
        $deuda_ex_arr_date = [];
        // ----------------------------------------------------

        if (count($request->all()) > 0) {
            $representants_query =
                Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderby('representants.name')
                ->groupby('representants.id');

            $representants_query = (isset($grado_id)) ? $representants_query->where('grados.id', $grado_id) : $representants_query;
            $representants_query = (isset($seccion_id)) ? $representants_query->where('seccions.id', $seccion_id) : $representants_query;

            $representants_query = ($formally == 'SI') ? $representants_query->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants_query;
            $representants_query = ($formally == 'NO') ? $representants_query->whereNull('inscripcions.id') : $representants_query;

            $representants = $representants_query->get();

            foreach ($representants as $representant) {
                $exchange_ammount_expire_bill_date = round($representant->getExchangeAmmountExpireBillQuota($date_start, $date_end), 2);
                if ($exchange_ammount_expire_bill_date > 0) {
                    $deuda_total_ex_date += $exchange_ammount_expire_bill_date;
                    $deuda_ex_arr_date[$representant->id] = $exchange_ammount_expire_bill_date;
                }
            }
        }

        // ... (Listados para la vista)
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_date();

        // Aseguramos que todas las variables en compact existan, aunque su valor sea 0 o []
        $compact = [
            'representants',
            'deuda_total_bs',
            'deuda_total_ex',
            'deuda_ex_arr_date',
            'deuda_bs_arr',
            'deuda_ex_arr',
            'deuda_total_ex_date',
            'planpago_list',
            'formally',
            'grado_id',
            'list_grado',
            'seccion_id',
            'list_seccion',
            'mensualidad',
            'list_cuentaxpagar'
        ];

        return view('administracion.representants.saldosDate', compact($compact));
    }

    public function saldos(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $status_active = (!empty($request->status_active)) ? $request->status_active : null;
        $count_estudiants = (!empty($request->count_estudiants)) ? $request->count_estudiants : null;
        $num_cuotas = (!empty($request->num_cuotas)) ? $request->num_cuotas : null;

        $representants = collect();
        $deuda_total = null;
        $deuda_total_ex = null;
        $deuda_total_bs = null;
        $deuda_bs_arr = [];
        $deuda_ex_arr = [];

        if (count($request->all()) > 0) {
            $representants =
                Representant::query()
                ->select('representants.*', 'estudiants.ci_estudiant')
                ->selectRaw('count(estudiants.id) as count_estudiants')
                ->leftjoin('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->orderBy('representants.name')
                ->groupBy('representants.id'); //dd($representants);

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;

            $representants = ($status_inscription_affects) ? $representants->where('planpagos.status_inscription_affects', $status_inscription_affects) : $representants;
            $representants = ($status_active) ? $representants->where('seccions.status_active', $status_active) : $representants;

            $representants = (isset($count_estudiants)) ? $representants->havingRaw('count(estudiants.id) = ?', [$count_estudiants]) : $representants;

            $representants = $representants->get();

            foreach ($representants as $representant) {
                $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
                if ($exchange_ammount_expire_bill > 0) {
                    $bs_exchange_ammount_expire_bill = $representant->bs_exchange_ammount_expire_bill;
                    $deuda_total_ex = $deuda_total_ex + $exchange_ammount_expire_bill;
                    $deuda_total_bs = $deuda_total_bs + $bs_exchange_ammount_expire_bill;
                    $deuda_bs_arr[$representant->id] = $bs_exchange_ammount_expire_bill;
                    $deuda_ex_arr[$representant->id] = $exchange_ammount_expire_bill;
                }
            }
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        $compact = [
            'status_inscription_affects',
            'status_active',
            'representants',
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

        return view('administracion.representants.saldos', compact($compact));
    }

    public function solvents(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null;

        $solvents = collect();

        if (count($request->all()) > 0) {

            $representants =  Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->Where('seccions.status_active', '=', 'true')
                ->Where('grados.status_active', '=', 'true')
                ->Where('estudiants.status_active', '=', 'true')
                ->where('seccions.status_inscription_affects', 'true')
                ->where('planpagos.status_inscription_affects', 'true')
                ->groupBy('representants.id');

            $representants = (isset($grado_id)) ? $representants->where('grados.id', $grado_id) : $representants;
            $representants = (isset($grado_id) && isset($seccion_id)) ? $representants->where('seccions.id', $seccion_id) : $representants;

            $representants = ($formally == 'SI') ? $representants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $representants;
            $representants = ($formally == 'NO') ? $representants->whereNull('inscripcions.id') : $representants;

            $representants = $representants->get();

            foreach ($representants as $representant) {
                $ammount = round($representant->exchange_ammount_expire_bill, 2);
                if ($ammount <= 0) {
                    $solvents->push($representant);
                }
            }
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = Seccion::list_seccion_grado($grado_id);
        $planpago_list = Planpago::list_planpago();

        // $solvents = Representant::formalySolvents();
        return view('administracion.representants.solvents', compact('solvents', 'grado_id', 'seccion_id', 'formally', 'list_grado', 'list_seccion', 'planpago_list'));
    }

    public function index(Request $request)
    {
        if ($request->get('search')) {
            $search = $request->get('search');
            $arr_get = [
                'search' => $search
            ];

            $representants = Representant::name($arr_get)
                ->where('status_active', 'true')
                ->OrderBy('created_at', 'desc')
                ->get();

            return view('administracion.representants.index', compact('representants', 'search'));
        } else {
            $search = '';
            return view('administracion.representants.index', compact('search'));
        }
    }

    public function edit($id)
    {
        $representant = Representant::findOrFail($id);

        $list_comment = Representant::COLUMN_COMMENTS;

        $user_list = User::orderby('users.username', 'asc')->pluck('users.username', 'users.id');

        return view('administracion.representants.edit', compact('representant', 'list_comment', 'user_list'));
    }

    public function update(UpdateRepresentantRequest $request, $id)
    {
        $representant = Representant::findOrFail($id);
        $search = $representant->ci_representant;
        $representant->fill($request->all());
        $representant->save();
        $users = CreateUserDB::create_users_representant();
        $user = User::find($representant->user_id);
        if ($user) {
            $unique = User::where('email', $representant->email)->first();
            $user->email = ($representant->email) ? $representant->email : $user->username . '@saefl.test';
            $arr = [
                'email' => $user->email
            ];
            Validator::make($request->all(), [
                'email' => 'unique:users,email,' . $user->id,
            ])->validate();

            // $user->save();
        }
        $messenge = trans('db_oper_result.user_update_ok');
        Session::flash('operp_ok', $messenge);
        Session::flash('class_oper', 'success');
        return redirect()->route('administracion.representants.index', compact('search'));
    }

    public function create()
    {
        $representant = Representant::first();
        $list_comment = Representant::COLUMN_COMMENTS;
        $user_list = User::orderby('users.username', 'asc')->pluck('users.username', 'users.id');
        return view('administracion.representants.create', compact('list_comment', 'user_list'));
    }

    public function store(CreateRepresentantRequest $request)
    {
        $search = $request->all()['ci_representant'];
        $representant = Representant::create($request->all());
        $users = CreateUserDB::create_users_representant();
        $user = User::find($representant->user_id);
        if ($user) {
            $user->email = ($representant->email) ? $representant->email : $user->username . '@saefl.test';
            $user->save();
        }
        Session::flash('operp_ok', 'Registro guardado exitosamente');
        return redirect()->route('administracion.representants.index', compact('search'));
    }

    public function destroy($id, Request $request)
    {
        $delete = Representant::findOrFail($id);
        $delete->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        Session::flash('operp_ok', $messenge);
        $pevaluacions = Representant::all()->sortByDesc('created_at');
        return view('administracion.representants.crud');
    }

    public function timeline(Request $request)
    {
        $representants = Representant::all();
        $search = $request->get('search', '');

        return view('administracion.representants.timeline', compact('representants', 'search'));
    }

    public function timeline_show(Request $request, $id)
    {
        $representant = Representant::findOrFail($id);

        return view('administracion.representants.timeline_show', compact('representant'));
    }
}

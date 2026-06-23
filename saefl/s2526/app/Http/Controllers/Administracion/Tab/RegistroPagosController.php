<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoRequest;
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoRepresentantRequest;
use App\Http\Requests\Administracion\Planpago\CreateRegistroParcialRequest;
use App\Http\Requests\Administracion\Planpago\UpdateRegistroPagoRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\DescuentoAplicado;

use App\Http\Controllers\Admin\Fix\DB\HomeController as FixDBControlller;

//Functions
use App\Http\Controllers\Administracion\Tab\Functions\RegistroPago\Create as Create;
use App\Http\Controllers\Administracion\Tab\Functions\RegistroPago\Store as Store;
use App\Http\Controllers\Administracion\Tab\Functions\RegistroPago\Edit as Edit;
use App\Http\Controllers\Administracion\Tab\Functions\RegistroPago\Crud as Crud;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;
use Exception;

class RegistroPagosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_admon']);
    }

    use Create; //parcial_create,representant_create,create
    use Store; //parcial_store,representant_store,store
    use Edit; //edit,update
    use Crud; //edit,update

    public function forceDelete($id, Request $request)
    {
        $registro_pago_combinado =
            DB::table('registro_pago_combinados')
            ->select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->where('registro_pagos.id', $id)
            ->first();

        $registro_pagos = RegistroPago::where('registro_pago_combinado_id', $registro_pago_combinado->id)->get();
        $approval = RegistroPago::where('registro_pago_combinado_id', $registro_pago_combinado->id)->where('cancellable', true)->first();
        $msn = '';

        foreach ($registro_pagos as $registro_pago) {

            $representant = $registro_pago->representant;

            $pago = Pago::withTrashed()->where('registro_pago_id', $registro_pago->id)->first();
            if (!empty($pago->id)) {
                $msn = $msn . 'PA_ID: ' . $pago->id . ' - ';
                if (!empty($pago->ingreso)) {
                    $ingreso = $pago->ingreso;
                    $number_i_pay = $ingreso->number_i_pay . '[BORRADO]' . $pago->id;
                    $update = Ingreso::where('id', $ingreso->id);
                    $update->update(['number_i_pay' => $number_i_pay]);
                    $msn = $msn . 'IN_ID: ' . $ingreso->id . ' - ';
                    $ingreso->forceDelete();
                }
                $pago->forceDelete();
            }

            if (!empty($registro_pago->creditoafavor->id)) {
                $registro_pago->creditoafavor->forceDelete();
            }

            foreach ($registro_pago->abono_aplicados as $abono_aplicado) {
                $msn = $msn . 'AP_ID: ' . $abono_aplicado->id . ' - ';
                $abono = Abono::withTrashed()->find($abono_aplicado->abono_id);
                $abono->restore();
                $ingreso = Ingreso::withTrashed()->find($abono->ingreso_id);
                $ingreso->restore();
                $abono_aplicado->forceDelete();
            }

            foreach ($registro_pago->creditoaplicados as $creditoaplicado) {
                $msn = $msn . 'CA_ID: ' . $creditoaplicado->id . ' - ';
                $credito_a_favor = CreditoAFavor::withTrashed()->find($creditoaplicado->credito_a_favor_id);
                $credito_a_favor->restore();
                $creditoaplicado->forceDelete();
            }

            foreach ($registro_pago->descuentoaplicados as $descuentoaplicado) {
                $msn = $msn . 'DCTO_ID: ' . $descuentoaplicado->id . ' - ';
                $descuentoaplicado->forceDelete();
            }

            foreach ($registro_pago->conceptocancelados as $conceptocancelado) {
                $msn = $msn . 'CONCEPT_ID: ' . $conceptocancelado->id . ' - ';
                $conceptocancelado->forceDelete();
            }

            if (!empty($registro_pago->registro_pago_combinado)) {
                $registro_pago->registro_pago_combinado->delete();
            }

            $registro_pago->forceDelete();

            DB::commit();
        }
    }

    public function anular($id, Request $request)
    {
        $registro_pago_combinado =
            DB::table('registro_pago_combinados')
            ->select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->where('registro_pagos.id', $id)
            ->first();

        $registro_pagos = RegistroPago::where('registro_pago_combinado_id', $registro_pago_combinado->id)->get();
        $approval = RegistroPago::where('registro_pago_combinado_id', $registro_pago_combinado->id)->where('cancellable', true)->first();

        $msn = '';

        foreach ($registro_pagos as $registro_pago) {

            $pago = Pago::withTrashed()->where('registro_pago_id', $registro_pago->id)->first();
            if (!empty($pago->id)) {
                $msn = $msn . 'PA_ID: ' . $pago->id . ' - ';
                if (!empty($pago->ingreso)) {
                    $ingreso = $pago->ingreso;
                    $number_i_pay = $ingreso->number_i_pay . '[BORRADO]' . $pago->id;
                    $update = Ingreso::where('id', $ingreso->id);
                    $update->update(['number_i_pay' => $number_i_pay]);
                    $msn = $msn . 'IN_ID: ' . $ingreso->id . ' - ';
                    $ingreso->delete();
                }
                $pago->delete();
            }

            if (!empty($registro_pago->creditoafavor->id)) {
                $registro_pago->creditoafavor->delete();
            }

            foreach ($registro_pago->abono_aplicados as $abono_aplicado) {
                // dd($abono_aplicado);
                $msn = $msn . 'AP_ID: ' . $abono_aplicado->id . ' - ';
                $abono = Abono::withTrashed()->find($abono_aplicado->abono_id);
                $abono->restore();
                $ingreso = Ingreso::withTrashed()->find($abono->ingreso_id);
                $ingreso->restore();
                $abono_aplicado->forceDelete();
            }

            foreach ($registro_pago->creditoaplicados as $creditoaplicado) { //dd($creditoaplicado);
                $msn = $msn . 'CA_ID: ' . $creditoaplicado->id . ' - ';
                $credito_a_favor = CreditoAFavor::withTrashed()->find($creditoaplicado->credito_a_favor_id);
                $credito_a_favor->restore();
                $creditoaplicado->forceDelete();
            }

            foreach ($registro_pago->descuentoaplicados as $descuentoaplicado) {
                $msn = $msn . 'DCTO_ID: ' . $descuentoaplicado->id . ' - ';
                $descuentoaplicado->forceDelete();
            }

            foreach ($registro_pago->conceptocancelados as $conceptocancelado) {
                $msn = $msn . 'CONCEPT_ID: ' . $conceptocancelado->id . ' - ';
                $conceptocancelado->forceDelete();
            }

            if (!empty($registro_pago->registro_pago_combinado)) {
                $registro_pago->registro_pago_combinado->delete();
            }
            $registro_pago->approval_date = $approval?->approval_date;
            $registro_pago->approval_user_id =  $approval?->approval_user_id;

            $registro_pago->cancellable = true;
            $registro_pago->cancellation_user_id = Auth::id();
            $registro_pago->cancelled_at = Carbon::now();
            $registro_pago->save();
            $registro_pago->delete();

            DB::commit();
        }

        if ($request->ajax()) {
            return response()->json([
                "messenge" => 'Registro de pago anulado exitosamente',
                "text" => $msn,
                "operation" => 'operp_ok',
            ]);
        }
    }

    public function index(Request $request)
    {
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = ['search' => $search];

            $registropagos = RegistroPago::select('registro_pagos.*', 'estudiants.name as estudiants_name', 'estudiants.lastname', 'estudiants.id as estudiant_id')
                ->join('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
                ->join('pagos', 'pagos.registro_pago_id', '=', 'registro_pagos.id')
                ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
                ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
                // ->join('bancos', 'pagos.banco_id', '=', 'bancos.id')
                ->name($arr_get)
                ->OrderBy('registro_pagos.id', 'desc')
                ->get();
            // dd($registropagos);

            return view('administracion.registropagos.index', compact('registropagos', 'search'));
        } else {
            $search = '';
            return view('administracion.registropagos.index', compact('search'));
        }
    }

    public function individual(Request $request)
    {
        // dd($request->all());
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = [
                'search' => $search,
            ];

            $estudiants = Estudiant::name($arr_get)->OrderBy('created_at', 'desc')->get();

            return view('administracion.registropagos.individual', compact('estudiants', 'search'));
        } else {
            $search = '';
            return view('administracion.registropagos.individual', compact('search'));
        }
    }

    public function show($id)
    {
        $registropago = RegistroPago::findOrFail($id);
        $creditos_aplicados = CreditoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();
        $credito_generado = CreditoAFavor::withTrashed()->where('registro_pago_id', $registropago->id)->first();
        $abonos_aplicados = AbonoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();
        $descuentos_aplicados = DescuentoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();

        return view('administracion.registropagos.show', compact('registropago', 'creditos_aplicados', 'credito_generado', 'abonos_aplicados', 'descuentos_aplicados'));
    }

    public function cancelations(Request $request)
    {
        $cuentaxpagar_id                = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null;
        $finicial                       = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal                         = (!empty($request->ffinal)) ? $request->ffinal : null;
        $bco_finicial                   = (!empty($request->bco_finicial)) ? $request->bco_finicial : null;
        $bco_ffinal                     = (!empty($request->bco_ffinal)) ? $request->bco_ffinal : null;
        $number_i_pay                   = (!empty($request->number_i_pay)) ? $request->number_i_pay : null;
        $ci                             = (!empty($request->ci)) ? $request->ci : null;
        $status_unexpired               = (!empty($request->status_unexpired)) ? $request->status_unexpired : null;
        $is_adjustment                  = (!empty($request->is_adjustment)) ? $request->is_adjustment : null;
        $status_inscription_affects     = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $cancellation_status            = (!empty($request->cancellation_status)) ? $request->cancellation_status : null;

        $registropagos = collect();

        if (count($request->all()) > 0) {

            $registropagos = RegistroPago::select('registro_pagos.*')

                ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
                ->leftjoin('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
                ->leftjoin('representants', 'representants.id', '=', 'estudiants.representant_id')
                ->where('estudiants.status_active', 'true')
                ->OrderBy('registro_pagos.created_at', 'desc');

            $registropagos = (isset($cuentaxpagar_id) && is_numeric($cuentaxpagar_id)) ? $registropagos->where('cuentaxpagars.id', $cuentaxpagar_id) : $registropagos;
            $registropagos = ($cuentaxpagar_id == 'DEUDA INDIVIDUAL') ? $registropagos->where('cuentaxpagars.type', 'INDIVIDUAL') : $registropagos;

            $registropagos = (isset($finicial)) ? $registropagos->where('registro_pagos.created_at', '>=', $finicial) : $registropagos;
            $registropagos = (isset($ffinal)) ? $registropagos->where('registro_pagos.created_at', '<=', $ffinal) : $registropagos;

            if (isset($number_i_pay) || isset($bco_finicial) || isset($bco_ffinal)) {
                $registropagos = $registropagos->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id');

                $registropagos = (isset($number_i_pay)) ? $registropagos->where('ingresos.number_i_pay', 'like', "%" . $number_i_pay . "%") : $registropagos;

                $registropagos = (isset($bco_finicial)) ? $registropagos->where('ingresos.date_payment', '>=', $bco_finicial) : $registropagos;
                $registropagos = (isset($bco_ffinal)) ? $registropagos->where('ingresos.date_payment', '<=', $bco_ffinal) : $registropagos;
            }

            $registropagos = ($status_inscription_affects) ? $registropagos->where('planpagos.status_inscription_affects', $status_inscription_affects) : $registropagos;

            $registropagos = ($status_unexpired == 'true') ? $registropagos->where('registro_pagos.status_unexpired', true) : $registropagos;
            $registropagos = ($status_unexpired == 'false') ? $registropagos->where('registro_pagos.status_unexpired', false) : $registropagos;

            // New cancellation status filter
            if ($cancellation_status) {
                switch ($cancellation_status) {
                    case 'active':
                        $registropagos = $registropagos->whereNull('registro_pagos.cancelled_at');
                        break;
                    case 'cancelled':
                        $registropagos = $registropagos->whereNotNull('registro_pagos.cancelled_at')
                            ->whereNotNull('registro_pagos.approval_date');
                        break;
                    case 'pending_approval':
                        $registropagos = $registropagos->whereNotNull('registro_pagos.cancelled_at')
                            ->whereNull('registro_pagos.approval_date');
                        break;
                }
            }

            if ($is_adjustment) {
                $registropagos =
                    $registropagos
                    ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
                    ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
                    ->where('bancos.is_adjustment', $is_adjustment);
            }

            if ($ci) {
                $registropagos = $registropagos->where(function ($query) use ($ci) {
                    $query->where('estudiants.ci_estudiant', 'like', "%" . $ci . "%")
                        ->orWhere('representants.ci_representant', 'like', "%" . $ci . "%");
                });
            }

            $registropagos = $registropagos->get();
        }

        $pago_total = 0;
        $pago_total_exchage = 0;
        $deuda_no_total = 0;
        foreach ($registropagos as $registropago) {
            $pago_total = $pago_total + $registropago->pagos->sum('pagos_ammount');
            $pago_total_exchage = $pago_total_exchage + $registropago->pagos->sum('exchange_ammount');
        }

        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar();
        $list_cuentaxpagar->put('DEUDA INDIVIDUAL', ['DEUDA INDIVIDUAL' => 'DEUDA INDIVIDUAL']);

        $compact = [
            'registropagos',
            'finicial',
            'ffinal',
            'bco_finicial',
            'bco_ffinal',
            'pago_total',
            'pago_total_exchage',
            'number_i_pay',
            'ci',
            'list_cuentaxpagar',
            'cuentaxpagar_id',
            'status_unexpired',
            'is_adjustment',
            'status_inscription_affects',
            'cancellation_status'
        ];

        return view('administracion.registropagos.cancelations', compact($compact));
    }
}

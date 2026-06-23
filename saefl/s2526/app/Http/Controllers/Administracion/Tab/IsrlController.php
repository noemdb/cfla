<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IsrlController extends Controller
{
    public function index(Request $request)
    {
        $total_annuity_exchange = 0;
        $total_annuity_ammunt = 0;
        $total_monthly_exchange = 0;
        $total_monthly_ammunt = 0;

        $total_exchange_ammount_payment=0;
        $total_general_exchange = 0;
        $total_ammount_ingresos=0;
        $total_exchange_ammount_ingresos=0;
        $estudiants = estudiant::formalys('2020-12-31');

        $registro_pagos = RegistroPago::select('registro_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->whereDate('cuentaxpagars.date_expiration','<=','2020-12-31')
            ->where('cuentaxpagars.type','GENERAL')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->whereNull('pagos.deleted_at')
            ->get();

        foreach ($registro_pagos as $registro_pago) {

            $pago = $registro_pago->pago;
            $cuentaxpagar = $registro_pago->cuentaxpagar;
            $estudiant = $registro_pago->estudiant;

            if ($pago && $cuentaxpagar && $estudiant) {

                $pagos_ammount = $pago->pagos_ammount ;
                $exchange_ammount = $pago->exchange_ammount ;

                $concepto_pagos = $cuentaxpagar->concepto_pagos;
                $concepto_pagos = ($concepto_pagos->isNotEmpty()) ? $concepto_pagos->where('exchange_ammount','>',0)->sortByDesc('status_annuity') : $concepto_pagos ; //dd($concepto_pagos);

                $total_general_exchange = $total_general_exchange + $exchange_ammount;

                foreach ($concepto_pagos as $concepto_pago) {

                    $descuento_ammount = $estudiant->descuento_ammount($cuentaxpagar->id);
                    $factor = ($descuento_ammount) ? (1/2) : 1 ;
                    $concepto_pago->exchange_ammount = ($concepto_pago->status_discount=='true') ? $concepto_pago->exchange_ammount * $factor : $concepto_pago->exchange_ammount;

                    if ($concepto_pago->status_annuity == 'true') {

                        if ($exchange_ammount >= $concepto_pago->exchange_ammount) {

                            $exchange_ammount_partial = $concepto_pago->exchange_ammount;
                            $exchange_ammount = $exchange_ammount - $concepto_pago->exchange_ammount;

                            $factor = $exchange_ammount / $concepto_pago->exchange_ammount;
                            $pagos_ammount_partial = $pagos_ammount * $factor;
                            $pagos_ammount = $pagos_ammount - $pagos_ammount_partial;

                        }
                        else {
                            $exchange_ammount_partial = $exchange_ammount;
                            $exchange_ammount = 0;

                            $pagos_ammount_partial = $pagos_ammount;
                            $pagos_ammount = 0;
                        }

                        $total_annuity_exchange = $total_annuity_exchange + $exchange_ammount_partial;
                        $total_annuity_ammunt = $total_annuity_ammunt + $pagos_ammount_partial;
                    }

                    if ($concepto_pago->status_annuity == 'false') {

                        $total_monthly_exchange = $total_monthly_exchange + $exchange_ammount ;

                        $total_monthly_ammunt = $total_monthly_ammunt + $pagos_ammount ;

                    }

                }

            }
        }


        $total_annuity_exchange_bill = 0;
        $total_monthly_exchange_bill = 0;

        foreach ($estudiants as $estudiant) {

            $cuentaxpagars = $estudiant->cuentaxpagars->where('type','GENERAL')->where('date_expiration','<=','2020-12-31'); //dd($estudiant,$cuentaxpagars);

            foreach ($cuentaxpagars as $cuentaxpagar) {

                $exchange_ammount = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);

                $concepto_pagos = $cuentaxpagar->concepto_pagos->sortByDesc('status_annuity');

                $total_general_exchange = $total_general_exchange + $exchange_ammount;

                foreach ($concepto_pagos as $concepto_pago) {

                    if ($concepto_pago->status_annuity == 'true') {
                        if ($exchange_ammount >= $concepto_pago->exchange_ammount) {
                            $exchange_ammount_partial = $exchange_ammount - $concepto_pago->exchange_ammount;
                            $exchange_ammount = $concepto_pago->exchange_ammount;
                        }
                        else {
                            $exchange_ammount_partial = $exchange_ammount;
                            $exchange_ammount = 0;
                        }
                        $total_annuity_exchange_bill = $total_annuity_exchange_bill + $exchange_ammount_partial;
                    }

                    if ($concepto_pago->status_annuity == 'false') {
                        $total_monthly_exchange_bill = $total_monthly_exchange_bill + $exchange_ammount ;
                    }

                }
            }
        }

        $total_annuity_exchange_bill = 0.781011372 * $total_annuity_exchange_bill;

        $cuentaxpagars = Cuentaxpagar::where('type','GENERAL')->where('date_expiration','<=','2020-12-31')->groupBy('name')->get(); //dd($cuentaxpagars);

        $pagos =  DB::table('pagos')
            ->selectRaw('sum(pagos.exchange_ammount) as exchange_ammount')
            ->selectRaw('sum(pagos.pagos_ammount) as pagos_ammount')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->whereNull('registro_pagos.deleted_at')
            ->first(); //dd($pagos);

        $total_ammount_pagos = ($pagos) ? $pagos->pagos_ammount : 0 ;
        $total_exchange_ammount_pagos = ($pagos) ? $pagos->exchange_ammount : 0 ;

        // $total_exchange_ammount_payment = ($pagos) ? $pagos->exchange_ammount : 0;

        // $total_annuity_ammunt = $total_annuity_ammunt * 1.155180333;
        // $total_monthly_ammunt =  $total_monthly_ammunt * 1.155180333;
        $total_monthly_ammunt =  $total_monthly_ammunt * 1.00700221;

        $estudiants_plan_beneficos = Estudiant::estudiantsPlanBeneficos(null,'2020-12-31'); //dd($plan_beneficos);

        $compact = [
            'total_annuity_exchange','total_monthly_exchange','total_monthly_ammunt','total_annuity_ammunt','total_ammount_pagos',
            'total_exchange_ammount_pagos','total_annuity_exchange_bill','total_monthly_exchange_bill','total_exchange_ammount_ingresos',
            'total_ammount_ingresos',
            'estudiants','cuentaxpagars','total_exchange_ammount_payment','estudiants_plan_beneficos'
        ];

        return view('administracion.isrl.index', compact($compact));

    }

    public function paids(Request $request)
    {
        $planpago_id        = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null;
        $concepto_pago_id   = (!empty($request->concepto_pago_id)) ? $request->concepto_pago_id : null;
        $date_payment       = (!empty($request->date_payment)) ? $request->date_payment : null;
        $finicial           = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null;
        $status_annuity     = (!empty($request->status_annuity)) ? $request->status_annuity : null;

        $allRepresentants = collect();
        $representants = collect();
        $paids = collect();
        $paids = collect();
        $cuentaxpagar = null;
        $monto_total = 0;

        if (count($request->all())>0) {

            $representants = Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->where('estudiants.status_active','true')
                ->orderBy('representants.ci_representant')
                ->groupBy('representants.ci_representant');

            $representants = (isset($ci_representant)) ? $representants->where('ci_representant', 'like', "%".$ci_representant."%") : $representants;

            $representants = $representants->get();

            $concepto_pagos = ConceptoPago::select('concepto_pagos.*');

            $concepto_pagos = ($status_annuity=='true' || $status_annuity=='false') ? $concepto_pagos->where('concepto_pagos.status_annuity',$status_annuity) : $concepto_pagos;

            $concepto_pagos = $concepto_pagos->get();

            foreach ($concepto_pagos as $concepto_pago) {
                foreach ($representants as $representant) {
                    $cEstudiante = collect();
                    $cRepresentant = collect();
                    $datas = collect();
                    $monto = 0;
                    $estudiants = $representant->estudiants;
                    foreach ($estudiants as $estudiant) {
                        $data = collect();
                        $monto_exchange = $concepto_pago->TotalExchangeMontoConceptoPagoPagado($estudiant->id,$finicial,$ffinal);
                        if ($monto_exchange > 0) {
                            $data->put('estudiant',$estudiant);
                            $data->put('monto_exchange',$monto_exchange);
                            $datas->push($data);
                            $monto_total += $monto_exchange ;
                        }
                    }
                    if ($datas->isNotEmpty()) {
                        $cRepresentant->put('representant',$representant);
                        $cRepresentant->put('total_abono_exchange',$representant->total_abono_exchange);
                        $cRepresentant->put('total_credito_exchange',$representant->total_credito_exchange);
                        $cRepresentant->put('estudiants',$datas);
                        $paids->push($cRepresentant);
                    }
                }
            }
        }

        $list_planpago = Planpago::list_planpago();
        $list_conceptopago = ConceptoPago::list_conceptopago();
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_simple();

        $compact = [
            'paids','cuentaxpagar','representants',
            'list_planpago', 'list_cuentaxpagar', 'list_conceptopago',
            'planpago_id','cuentaxpagar_id','concepto_pago_id','date_payment','ci_representant','status_annuity','finicial','ffinal',
            'monto_total'
        ];

        return view('administracion.isrl.conceptopagos.paids', compact($compact));
    }
}

<?php

namespace App\Models\app\Planpago\Functions\Cuentaxpagar;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Planpago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiant;
use App\Models\app\Planpago\DescuentoAplicado;

trait Administracions
{

    public function StatusPay($estudiant_id)
    {
        return ($this->TotalMontoConceptosXPagar($estudiant_id)) ? false : true;
    }

    public function ConceptosPagados($estudiant_id)
    {
        $concepto_pagos =
            Cuentaxpagar::select(
                'concepto_pagos.status_discount as status_discount',
                'concepto_pagos.concepto_ammount as concepto_ammount',
                'registro_pagos.id as registro_pago_id',
                'nom_concepto_pagos.name as concepto_name',
                'concepto_pagos.id as concepto_pago_id',
                'concepto_cancelados.id as concepto_cancelados_id ',
                'concepto_cancelados.status_paid as concepto_cancelados_status_paid',
                'descuento_aplicados.id as descuento_aplicado_id',
                'descuentos.descuento_ammount'
            )
            ->selectRaw('sum(concepto_cancelados.concepto_ammount) as sum_pay_concepto_ammount')

            ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('concepto_cancelados', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
            ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
            ->leftjoin('descuento_aplicados', 'registro_pagos.id', '=', 'descuento_aplicados.registro_pago_id')
            ->leftjoin('descuentos', 'descuentos.id', '=', 'descuento_aplicados.descuento_id')
            ->where('cuentaxpagars.id', $this->id)
            ->where('registro_pagos.estudiant_id', $estudiant_id)

            ->where('concepto_cancelados.status_paid', 'true')
            // ->where('concepto_cancelados.status_partial','false')

            // ->havingRaw('sum(concepto_cancelados.concepto_ammount) >= ?',['concepto_pagos.concepto_ammount'])

            ->wherenull('registro_pagos.deleted_at')
            ->wherenull('concepto_cancelados.deleted_at')
            ->wherenull('concepto_pagos.deleted_at')

            // ->whereRAW('sum(concepto_ammount) >?', 0)
            // ->whereRaw('price > IF(state = "TX", ?, 100)', [200])
            // ->whereRaw('sum(concepto_cancelados.concepto_ammount) > 0')

            ->orderBy('concepto_pagos.id', 'asc')
            ->groupBy('concepto_pagos.id')

            ->get();

        return $concepto_pagos;
    }
    public function ConceptosPagadosParcial($estudiant_id)
    {
        $concepto_pagos =
            Cuentaxpagar::select(
                'concepto_pagos.status_discount as status_discount',
                'concepto_pagos.concepto_ammount as concepto_ammount',
                'registro_pagos.id as registro_pago_id',
                'nom_concepto_pagos.name as concepto_name',
                'concepto_pagos.id as concepto_pago_id',
                'concepto_cancelados.id as concepto_cancelados_id ',
                'concepto_cancelados.status_paid as concepto_cancelados_status_paid',
                'concepto_cancelados.concepto_ammount AS ammount_pago_parcial'
            )
            ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('concepto_cancelados', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
            ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')

            ->where('cuentaxpagars.id', $this->id)
            ->where('registro_pagos.estudiant_id', $estudiant_id)

            ->where('concepto_cancelados.status_partial', 'true')
            ->where('concepto_cancelados.status_paid', 'false')

            // ->havingRaw('sum(concepto_cancelados.concepto_ammount) < ?',['concepto_pagos.concepto_ammount'])

            ->wherenull('concepto_cancelados.deleted_at')
            ->wherenull('concepto_pagos.deleted_at')
            ->wherenull('registro_pagos.deleted_at')
            ->orderby('concepto_pagos.id', 'asc')
            ->get();

        // dd('concepto_pagos',$concepto_pagos->toarray());
        return $concepto_pagos;
    }

    public function ConceptosXPagar($estudiant_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $conceptos_x_pagar = collect();

        $arr_id = $this->ConceptosPagados($estudiant_id)->pluck('concepto_pago_id');

        $conceptos_x_pagar =
            ConceptoPago::select(
                'concepto_pagos.status_discount as status_discount',
                'nom_concepto_pagos.name as concepto_name',
                'concepto_pagos.id as concepto_pago_id',
                'concepto_pagos.id as id',
                'concepto_pagos.concepto_ammount as concepto_ammount',
                'cuentaxpagars.id as cuentaxpagar_id'
            )
            ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')

            ->whereNotIn('concepto_pagos.id', $arr_id)
            ->where('cuentaxpagars.id', $this->id)

            //incluir correctamente las cuentas individuales
            ->where('cuentaxpagars.estudiant_id', $this->estudiant_id)

            ->wherenull('concepto_pagos.deleted_at')

            // ->whereRAW('sum(concepto_ammount) >?', 0)

            // ->groupBy('cuentaxpagars.id')

            ->orderby('concepto_pagos.created_at', 'asc');
        // ->get();

        $planpago = ($estudiant->administrativa) ? $estudiant->administrativa->planpago : null;
        $conceptos_x_pagar = ($planpago) ? $conceptos_x_pagar->where('cuentaxpagars.planpago_id', $planpago->id) : $conceptos_x_pagar;

        $conceptos_x_pagar = $conceptos_x_pagar->get();

        return $conceptos_x_pagar;
    }

    public function TotalMontoConceptosPagados($estudiant_id)
    {
        $total = 0;
        $registros_pagos = RegistroPago::Where('cuentaxpagar_id', $this->id)->Where('estudiant_id', $estudiant_id)->get()->toArray();
        if (is_array($registros_pagos)) {
            foreach ($registros_pagos as $k => $v) {
                $descuento_ammount = 0;
                $descuento =
                    DescuentoAplicado::select('descuentos.descuento_ammount as descuento_ammount')
                    ->join('registro_pagos', 'registro_pagos.id', '=', 'descuento_aplicados.registro_pago_id')
                    ->join('descuentos', 'descuentos.id', '=', 'descuento_aplicados.descuento_id')
                    ->wherenull('registro_pagos.deleted_at')
                    ->where('descuento_aplicados.registro_pago_id', $v['id'])
                    ->where('registro_pagos.estudiant_id', $estudiant_id)
                    ->first();
                if (!is_null($descuento)) {
                    $descuento_ammount = $descuento->descuento_ammount;
                }

                $conceptos = $this->ConceptosPagados($estudiant_id);
                foreach ($conceptos as $concepto) {
                    $ammount = ConceptoCancelado::where('concepto_pago_id', $concepto->concepto_pago_id)->sum('concepto_ammount');
                    if ($concepto->status_discount == 'true') {
                        $total = $total + ($concepto->concepto_ammount - $ammount) * (1 - $descuento_ammount / 100);
                    } else {
                        $total = $total + ($concepto->concepto_ammount - $ammount);
                    }
                }
            }
        }
        return $total;
    }

    public function TotalMontoConceptosXPagar($estudiant_id)
    {
        $conceptos_x_pagar_cd_compl = 0;
        $conceptos_x_pagar_sd_compl = 0;
        $conceptos_x_pagar_cd_p = 0;
        $conceptos_x_pagar_cd = 0;
        $conceptos_x_pagar_sd_p = 0;
        $conceptos_x_pagar_sd = 0;

        $conceptos_x_pagar_cd_compl = $this->ConceptosXPagar($estudiant_id)->where('status_discount', 'true')->sum('concepto_ammount');
        if (!empty($conceptos_x_pagar_cd_compl)) {
            $conceptos_x_pagar_cd_p = $this->ConceptosPagadosParcial($estudiant_id)->where('status_discount', 'true')->sum('ammount_pago_parcial');
        }

        $conceptos_x_pagar_sd_compl = $this->ConceptosXPagar($estudiant_id)->where('status_discount', 'false')->sum('concepto_ammount');
        if (!empty($conceptos_x_pagar_sd_compl)) {
            $conceptos_x_pagar_sd_p = $this->ConceptosPagadosParcial($estudiant_id)->where('status_discount', 'false')->sum('ammount_pago_parcial');
            $conceptos_x_pagar_sd = $conceptos_x_pagar_sd_compl - $conceptos_x_pagar_sd_p;
        }

        $descuento = PlanBenefico::select('descuentos.descuento_ammount')
            ->withTrashed()
            ->join('descuentos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->Where('plan_beneficos.estudiant_id', $estudiant_id)
            ->Where('plan_beneficos.created_at', '<=', $this->date_expiration)
            ->Where('plan_beneficos.ffinal', '>=', $this->date_expiration)
            ->wherenull('descuentos.deleted_at')
            ->wherenull('plan_beneficos.deleted_at')
            ->sum('descuento_ammount');
        $descuento_ammount = $conceptos_x_pagar_sd + ($conceptos_x_pagar_cd_compl * (1 - ($descuento / 100))) - $conceptos_x_pagar_cd_p;

        $str = $conceptos_x_pagar_sd . ' + ' . ($conceptos_x_pagar_cd_compl * (1 - ($descuento / 100))) . ' - ' . $conceptos_x_pagar_cd_p . '(' . $this->id . ')';

        return $descuento_ammount;
    }

    public function ConceptoPagadoTest($concepto_id, $estudiant_id)
    {
        $concepto =
            ConceptoCancelado::select('concepto_cancelados.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('concepto_cancelados.id', $concepto_id)
            ->wherenull('registro_pagos.deleted_at')
            ->where('cuentaxpagars.id', $this->id)
            ->where('registro_pagos.estudiant_id', $estudiant_id)
            ->find(1);
        // dd('concepto_pagos',$concepto->toarray());
        return $concepto;
    }

    public function CuentaPagadaTest($estudiant_id)
    {
        $total = $this->TotalMontoConceptosXPagar($estudiant_id);

        return ($total > 0) ? true : false;
    }

    public function SumaConceptos()
    {
        return ConceptoPago::where('cuentaxpagar_id', $this->id)->sum('concepto_ammount');
    }

    public function getTotalConceptosAttribute()
    {
        return ConceptoPago::where('cuentaxpagar_id', $this->id)->sum('concepto_ammount');
    }

    public function SumaConceptosDescuentos($estudiant_id)
    {
        $descuento_ammount = Estudiant::findorfail($estudiant_id)->descuento_ammount($this->id);
        $concepto_cd = ConceptoPago::where('cuentaxpagar_id', $this->id)->where('status_discount', 'true')->sum('concepto_ammount');
        $concepto_sd = ConceptoPago::where('cuentaxpagar_id', $this->id)->where('status_discount', 'false')->sum('concepto_ammount');
        $total = $concepto_sd + $concepto_cd * (1 - $descuento_ammount / 100);
        return $total;
    }

    public function getCtaConDescuentoAttribute()
    {
        $descuento = ConceptoPago::where('cuentaxpagar_id', $this->id)->where('status_discount', 'true')->find(1);
        return (!$descuento) ? true : false;
    }

    public function TotalMontoCuentasXPagar($estudiant_id)
    {
        $sum = DB::table('cuentaxpagars')
            ->selectRaw('sum(concepto_pagos.concepto_ammount) as ammount')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.id', $this->id)
            ->first();
        return ($sum) ? $sum->ammount : null;
    }

    public function TotalMontoCuentasXPagarPagado($estudiant_id)
    {
        $sum = DB::table('registro_pagos')
            ->selectRaw('sum(pagos.pagos_ammount) as ammount')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.id', $this->id)
            ->where('registro_pagos.estudiant_id', $estudiant_id)
            ->groupBy('cuentaxpagars.id')
            ->first();
        return ($sum) ? $sum->ammount : null;
    }

    public function TotalMontoCuentasXPagarAdeudado($estudiant_id)
    {
        $total_x_pagar = $this->TotalMontoCuentasXPagar($estudiant_id); //dd($total_x_pagar);
        $total_pagadas = $this->TotalMontoCuentasXPagarPagado($estudiant_id); //dd($total_pagadas);
        $total = $total_x_pagar - $total_pagadas; //dd($total,$total_x_pagar,$total_pagadas);
        return $total;
    }
}

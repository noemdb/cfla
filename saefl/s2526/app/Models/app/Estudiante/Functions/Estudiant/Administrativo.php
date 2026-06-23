<?php

namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Administrativo
{

    public function getAdministrativa()
    {
        $administrativa = $this->administrativa;
        if (! empty($administrativa)) {
            $planpago = $administrativa->planpago;
            if (! empty($planpago)) {
                $status_active = $planpago->status_active;
                return ($status_active == "true") ? $administrativa : null;
            }
        }
    }

    public function getBillIndividualsAttribute()
    {
        $cuentaxpagars =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->selectRaw('sum(concepto_pagos.exchange_ammount) as concepto_pagos_exchange_ammount')
            ->Where('cuentaxpagars.type', 'INDIVIDUAL')
            ->orWhere('cuentaxpagars.estudiant_id', $this->id)
            ->WhereNotNull('cuentaxpagars.estudiant_id')
            ->OrderBy('cuentaxpagars.date_expiration', 'asc')
            ->groupBy('cuentaxpagars.id')
            ->get();

        return $cuentaxpagars;
    }

    public function getAmmountBillIndividualsAttribute()
    {
        return ($this->bill_individuals->isNotEmpty()) ? $this->bill_individuals->concepto_pagos_exchange_ammount : null;
    }

    public function getFullAdministrativaAttribute()
    {
        $full_administrativa = null;
        if (! empty($this->administrativa)) {
            $administrativa = $this->administrativa; //dd($administrativa);
            if (! empty($administrativa->planpago)) {
                $planpago = $administrativa->planpago; //dd($planpago);
                if (! empty($planpago)) { //dd($planpago->status_inscription_affects );
                    $full_administrativa = ($planpago->status_inscription_affects == "true") ? "{$planpago->name}" : 'Desactivo';
                }
            }
        }
        return $full_administrativa;
    }

    public function getExpireBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $cuentaxpagars_expire =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->Where('date_expiration', '<=', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.type', 'GENERAL')
            ->orWhere('cuentaxpagars.estudiant_id', $this->id)
            ->WhereNotNull('cuentaxpagars.estudiant_id')
            ->OrderBy('cuentaxpagars.date_expiration', 'asc')
            ->get();

        return (!empty($cuentaxpagars_expire)) ? $cuentaxpagars_expire : 0;
    }

    public function getBillsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $cuentaxpagars_expire =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.type', 'GENERAL')
            ->orWhere('cuentaxpagars.estudiant_id', $this->id)
            ->WhereNotNull('cuentaxpagars.estudiant_id')
            ->OrderBy('cuentaxpagars.date_expiration', 'asc')
            ->get();

        return (!empty($cuentaxpagars_expire)) ? $cuentaxpagars_expire : 0;
    }

    public function getAmmountExpireBillAttribute()
    {
        $id = $this->id;
        $total = 0;
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';

        $cta_x_pagar_vencidas =
            Cuentaxpagar::Where('date_expiration', '<', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.type', 'GENERAL')
            ->orWhere('cuentaxpagars.estudiant_id', $this->id)
            ->WhereNotNull('cuentaxpagars.estudiant_id')
            ->get();

        foreach ($cta_x_pagar_vencidas as $cta_x_pagar_vencida) {
            // $total = $total + $cta_x_pagar_vencida->TotalMontoConceptosXPagar($this->id);
            $total = $total + $cta_x_pagar_vencida->TotalExchangeMontoCuentasXPagar($this->id);
        }

        $exchange_rate_current = ExchangeRate::whereDate('date', Carbon::now()->format('Y-m-d'))->first();
        $total = $exchange_rate_current ? $exchange_rate_current->ammount * $total : null;
        $total = round($total, 2);

        return $total;
    }

    public function getNoExpireBillsAttribute()
    {
        $id = $this->id;
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $no_expire_bills =
            Cuentaxpagar::Where('date_expiration', '>=', Carbon::now())
            ->where('planpago_id', $planpago_id)
            ->Where('cuentaxpagars.type', 'GENERAL')
            ->orWhere(function ($q) use ($id) {
                $q->where('cuentaxpagars.type', 'INDIVIDUAL')
                    ->where('cuentaxpagars.estudiant_id', $id)
                    ->where('cuentaxpagars.date_expiration', '>=', Carbon::now());
            })
            ->get();
        return (!empty($no_expire_bills)) ? $no_expire_bills : 0;
    }

    public function getAmmountNoExpireBillAttribute()
    {
        $id = $this->id;
        $total = 0;
        $cta_x_pagar_no_vencidas = $this->no_expire_bills;
        foreach ($cta_x_pagar_no_vencidas as $cta_x_pagar_no_vencida) {
            $total = $total + $cta_x_pagar_no_vencida->TotalMontoConceptosXPagar($this->id);
        }
        return $total;
    }

    public function getCuentaxpagarsAttribute()
    {
        return $this->expire_bills;
    }


    public function statusPaidCuentaxpagar($cuentaxpagar_id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
        $monto = $cuentaxpagar->TotalMontoConceptosXPagar($this->id);
        if ($monto > 0) {
            return true;
        }
        return false;
    }

    public function getExpireBillPendientesAttribute()
    {
        $expire_bills = $this->expire_bills;

        $pendientes = collect();
        $total_monto = null;

        foreach ($expire_bills as $expire_bill) {
            $monto = $expire_bill->TotalMontoConceptosXPagar($this->id);
            $total_monto = $total_monto + $monto;
            if ($monto > 0) {
                $pendientes->push($expire_bill);
            }
        }

        return $pendientes;
    }

    public function getExpireBillConceptsAttribute()
    {
        return $this->expire_bills;
    }

    public function getCtaxpagarsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $cta_x_pagars   =
            Cuentaxpagar::where('planpago_id', $planpago_id)
            ->where('date_expiration', '<=', Carbon::now())
            ->get();

        return $cta_x_pagars;
    }

    public function getDebtsAttribute()
    {
        $planpago_id = (!empty($this->administrativa->planpago_id)) ? $this->administrativa->planpago_id : '0';
        $cta_x_pagars   =
            Cuentaxpagar::where('planpago_id', $planpago_id)
            ->where('date_expiration', '<=', Carbon::now())
            ->get()
            ->ToArray();

        foreach ($cta_x_pagars as $cta_x_pagar) {
            $registro_pago =
                RegistroPago::where('cuentaxpagar_id', 9)
                ->get();
        }

        return $cta_x_pagars;
    }

    public function getPlanpagoAttribute()
    {
        $administrativa = ($this->administrativa) ? $this->administrativa : null;
        $planpago = ($administrativa) ? $administrativa->planpago : null;
        return ($planpago) ? $planpago : null;
    }

    public function getPlanpagoNameAttribute()
    {
        // $administrativa = ($this->administrativa) ? $this->administrativa : null ;
        // $planpago = ($administrativa) ? $administrativa->planpago : null ;
        return ($this->planpago) ? $this->planpago->name : null;
    }

    public function getTotalAPagaroAttribute()
    {
        $sum_conceptos =
            Cuentaxpagar::select('cuentaxpagars.*')
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->wherenull('planpagos.deleted_at')
            ->wherenull('cuentaxpagars.deleted_at')
            ->where('planpagos.id', $this->administrativa->planpago->id)
            ->sum('concepto_pagos.concepto_ammount');

        return (empty($sum_conceptos)) ? 0 : $sum_conceptos;
    }

    public function getDiscountAmmount($cuentaxpagar_id)
    {
        $cuentaxpagar = DB::table('cuentaxpagars')->select('date_expiration')->where('id', $cuentaxpagar_id)->first();
        $q = DB::table('descuentos')
            ->select('descuentos.descuento_ammount')
            ->join('plan_beneficos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->join('estudiants', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->wherenull('descuentos.deleted_at')
            ->wherenull('plan_beneficos.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->where('estudiants.id', $this->id)
            ->WhereDate('plan_beneficos.created_at', '<=', $cuentaxpagar->date_expiration)
            ->WhereDate('plan_beneficos.ffinal', '>=', $cuentaxpagar->date_expiration)
            ->first();
        return ($q) ? $q->descuento_ammount : null;
    }

    public function descuento($cuentaxpagar_id)
    { //FIX implementando multiples planes beneficos
        $cuentaxpagar = Cuentaxpagar::findorfail($cuentaxpagar_id);
        $date_expiration = ($cuentaxpagar->name == 'AGOSTO')
            ? (date('Y') - 1) . '-08-06'
            : $cuentaxpagar->date_expiration; // se cambia la fecha de vencimiento de la cuota, para permitir el descuento


        $descuento =
            Estudiant::select(
                'descuentos.id',
                'descuentos.descuento_name',
                'descuentos.descuento_description',
                'descuentos.descuento_observations',
                'descuentos.descuento_type',
                'descuentos.status_modifiable',
                'descuentos.status_active',
                'plan_beneficos.name as plan_beneficos_name'
            )
            ->selectRaw('sum(descuentos.descuento_ammount) as descuento_ammount')
            ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->join('descuentos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->wherenull('plan_beneficos.deleted_at')
            ->wherenull('descuentos.deleted_at')
            ->where('estudiants.id', $this->id)
            ->WhereDate('plan_beneficos.created_at', '<=', $date_expiration)
            ->WhereDate('plan_beneficos.ffinal', '>=', $date_expiration)
            ->groupBy('plan_beneficos.estudiant_id')
            ->first();

        if ($this->id == 1140 && $cuentaxpagar_id == 8) {
            // dd($descuento,$date_expiration);
        }
        return $descuento;
    }

    public function getDescuentosAttribute()
    {
        $fecha = Carbon::now()->format('Y-m-d');
        $descuentos =
            Estudiant::select('descuentos.*', 'plan_beneficos.name as plan_beneficos_name')
            ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->join('descuentos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->wherenull('plan_beneficos.deleted_at')
            ->wherenull('descuentos.deleted_at')
            ->where('estudiants.id', $this->id)
            ->WhereDate('plan_beneficos.created_at', '<=', $fecha)
            ->WhereDate('plan_beneficos.ffinal', '>=', $fecha)
            ->first();
        return $descuentos;
    }

    public function descuento_ammount($cuentaxpagar_id)
    {
        $cuentaxpagar = Cuentaxpagar::findorfail($cuentaxpagar_id);
        return (!empty($this->descuento($cuentaxpagar->id))) ? $this->descuento($cuentaxpagar->id)->descuento_ammount : 0;
    }

    public function getDescuentoTestAttribute()
    {
        $descuento =
            Estudiant::select('descuentos.*')
            ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->join('descuentos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->wherenull('plan_beneficos.deleted_at')
            ->wherenull('descuentos.deleted_at')
            ->where('estudiants.id', $this->id)
            ->first();
        return $descuento;
    }

    public function getAmmountUnexpiredBillPaidAttribute()
    {
        $fecha = Carbon::now();
        $total_pagos =
            RegistroPago::select('pagos.pagos_ammount')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->whereDate('cuentaxpagars.date_expiration', '>', $fecha)
            ->where('registro_pagos.estudiant_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->orderby('registro_pagos.id', 'asc')
            ->sum('pagos_ammount');
        return $total_pagos;
    }

    public function TotalCreditoxCtaxPagar($cuentaxpagar_id)
    {
        $total_credito =
            RegistroPago::select('credito_a_favors.credito_ammount')
            ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
            ->join('credito_a_favors', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->where('registro_pagos.cuentaxpagar_id', $cuentaxpagar_id)
            ->where('registro_pagos.estudiant_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->wherenull('plan_beneficos.deleted_at')
            ->orderby('registro_pagos.id', 'asc')
            ->sum('credito_ammount');
        return $total_credito;
    }

    public function MontoDescuentoxCtaxPagar($cuentaxpagar_id)
    {
        $monto_descuento =
            RegistroPago::select('descuentos.descuento_ammount')
            ->join('descuento_aplicados', 'registro_pagos.id', '=', 'descuento_aplicados.registro_pago_id')
            ->join('descuentos', 'descuentos.id', '=', 'descuento_aplicados.descuento_id')
            ->where('registro_pagos.cuentaxpagar_id', $cuentaxpagar_id)
            ->where('registro_pagos.estudiant_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->orderby('registro_pagos.id', 'asc')
            ->sum('descuento_ammount');
        return $monto_descuento;
    }

    public function TotalConceptoxCtaxPagar($cuentaxpagar_id)
    {
        $total_concepto =
            RegistroPago::select('descuentos.descuento_ammount')
            ->join('descuento_aplicados', 'registro_pagos.id', '=', 'descuento_aplicados.registro_pago_id')
            ->join('descuentos', 'descuentos.id', '=', 'descuento_aplicados.descuento_id')
            ->where('registro_pagos.cuentaxpagar_id', $cuentaxpagar_id)
            ->where('registro_pagos.estudiant_id', $this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->orderby('registro_pagos.id', 'asc')
            ->sum('descuento_ammount');
        return $total_concepto;
    }

    public function CreditosAFavorDisponibles()
    {
        $creditos =
            CreditoAFavor::select('credito_a_favors.*')
            ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->where('credito_a_favors.representant_id', $this->representant_id)
            ->whereNull('credito_aplicados.credito_a_favor_id')
            ->wherenull('credito_a_favors.deleted_at')
            // ->wherenotnull('credito_aplicados.deleted_at')
            ->orderby('credito_a_favors.id', 'asc')
            ->get();
        return $creditos;
    }

    public function getCreditosDisponiblesAttribute()
    {
        $creditos =
            CreditoAFavor::select('credito_a_favors.*')
            ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->where('credito_a_favors.representant_id', $this->representant_id)
            ->whereNull('credito_aplicados.credito_a_favor_id')
            ->wherenull('credito_a_favors.deleted_at')
            // ->wherenotnull('credito_aplicados.deleted_at')
            ->orderby('credito_a_favors.id', 'asc')
            ->get();
        return $creditos;
    }
}

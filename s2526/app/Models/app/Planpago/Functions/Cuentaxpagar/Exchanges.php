<?php

namespace App\Models\app\Planpago\Functions\Cuentaxpagar;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;

trait Exchanges
{

    public $limit_omit = 0.1; //liminite inferior para considerar pagada la deuda

    public function TotalExchangeMontoCuentasXPagarNeto()
    {
        return round($this->conceptopagos->sum('exchange_ammount'), 8);
    }

    public function TotalExchangeMontoCuentasXPagar($estudiant_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $conceptopagos = $this->conceptopagos;
        $tota_exchange = null;
        foreach ($conceptopagos as $conceptopago) {
            $descuento_ammount = ($conceptopago->status_discount == 'true') ? $estudiant->descuento_ammount($this->id) : null;
            $tota_exchange = $tota_exchange + $conceptopago->exchange_ammount * (1 - ($descuento_ammount / 100));
        }
        $tota_exchange = round($tota_exchange, 8);
        return $tota_exchange;
    }

    public function TotalExchangeMontoCuentasXPagarPagado($estudiant_id)
    {
        $sum_exchange = DB::table('registro_pagos')
            ->selectRaw('sum(pagos.exchange_ammount) as ammount')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->where('cuentaxpagars.id', $this->id)
            ->where('registro_pagos.estudiant_id', $estudiant_id)
            ->groupBy('cuentaxpagars.id')
            ->first();
        $tota_exchange = ($sum_exchange) ?  round($sum_exchange->ammount, 8) : null;

        return $tota_exchange;
    }

    public function TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id)
    {
        $total_x_pagar = $this->TotalExchangeMontoCuentasXPagar($estudiant_id); //dd($total_x_pagar);
        $total_pagadas = $this->TotalExchangeMontoCuentasXPagarPagado($estudiant_id); //dd($total_pagadas);
        $tota_exchange = $total_x_pagar - $total_pagadas;
        $tota_exchange = round($tota_exchange, 8);
        return $tota_exchange;
    }

    public function TotalExchangeMontoCuentasXPagarAdeudadoRepresentant($representant_id)
    {
        $total = 0;
        $representant = Representant::findOrfail($representant_id);
        $estudiants = $representant->estudiants;
        foreach ($estudiants as $estudiant) {
            $total_x_pagar = $this->TotalExchangeMontoCuentasXPagar($estudiant->id); //dd($total_x_pagar);
            $total_pagadas = $this->TotalExchangeMontoCuentasXPagarPagado($estudiant->id); //dd($total_pagadas);
            $total = $total_x_pagar - $total_pagadas;
        }
        return $total;
    }

    public function StatusPayExchange($estudiant_id)
    {
        return ($this->TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id) >= $this->limit_omit) ? false : true;
    }

    public function getExchangeAmmounAnnuityAttribute()
    {
        $exchange_ammount = DB::table('cuentaxpagars')
            ->selectRaw('sum(concepto_pagos.exchange_ammount) as ammount')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('concepto_pagos.status_annuity', 'true')
            ->where('cuentaxpagars.type', 'GENERAL')
            ->first();
        return ($exchange_ammount) ? $exchange_ammount->ammount : null;
    }

    public function getExchangeAmmounMonthlyAttribute()
    {
        $exchange_ammount = DB::table('cuentaxpagars')
            ->selectRaw('sum(concepto_pagos.exchange_ammount) as ammount')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('concepto_pagos.status_annuity', 'false')
            ->where('cuentaxpagars.type', 'GENERAL')
            ->first();
        return ($exchange_ammount) ? $exchange_ammount->ammount : null;
    }
}

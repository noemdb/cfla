<?php

namespace App\Models\app\Planpago\Functions\ConceptoPago;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\Cuentaxpagar;

trait Exchanges {

    public $limit_omit=0.1;//liminite inferior para considerar pagada la deuda

    public function getTotalsAmmountExchangePagado($estudiant_id)
    {
        // dd($this);
        $concepto_pagos =
            Cuentaxpagar::select('concepto_pagos.status_discount as status_discount',
            'concepto_pagos.concepto_ammount as concepto_ammount','nom_concepto_pagos.name as concepto_name',
            'concepto_pagos.id as concepto_pago_id', 'concepto_cancelados.id as concepto_cancelados_id ',
            'concepto_cancelados.status_paid as concepto_cancelados_status_paid',
            'concepto_cancelados.concepto_ammount AS ammount_pagado',
            'concepto_cancelados.exchange_ammount AS ammount_exchange_pagado'
            )
                ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->join('concepto_cancelados', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
                ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->where('cuentaxpagars.id',$this->cuentaxpagar_id)
                ->where('concepto_pagos.id',$this->id)
                ->where('registro_pagos.estudiant_id',$estudiant_id)
                ->wherenull('registro_pagos.deleted_at')
                ->wherenull('concepto_cancelados.deleted_at')
                ->wherenull('concepto_pagos.deleted_at')
                ->orderby('concepto_pagos.id','asc')
                ->get();

        return $concepto_pagos;
    }

    public function TotalExchangeMontoCuentasXPagar($estudiant_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);

        $concepto_pagos = DB::table('concepto_pagos')
        ->selectRaw('sum(concepto_pagos.exchange_ammount) as exchange_ammount')
        ->where('concepto_pagos.id',$this->id)
        ->first();

        return ($concepto_pagos) ? $concepto_pagos->exchange_ammount : null;
    }

    public function TotalExchangeMontoConceptoPagoPagado($estudiant_id,$finicial=null,$ffinal=null)
    {
        $registro_pagos = DB::table('registro_pagos')
            // ->selectRaw('sum(pagos.concepto_ammount) as ammount')
            ->selectRaw('sum(concepto_cancelados.exchange_ammount) as exchange_ammount')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')

            ->join('concepto_cancelados', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
            ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')

            ->where('concepto_pagos.id',$this->id)
            ->where('registro_pagos.estudiant_id',$estudiant_id)

            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('concepto_pagos.deleted_at')
            ->whereNull('concepto_cancelados.deleted_at')

            ->groupBy('concepto_pagos.id')
            ;

        $registro_pagos = $registro_pagos->first();

        return ($registro_pagos) ? $registro_pagos->exchange_ammount : null;
    }

    public function TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id)
    {
        $total_x_pagar = $this->TotalExchangeMontoCuentasXPagar($estudiant_id); //dd($total_x_pagar);
        $total_pagadas = $this->TotalExchangeMontoConceptoPagoPagado($estudiant_id); //dd($total_pagadas);
        $total = $total_x_pagar - $total_pagadas;
        return $total;
    }

    public function StatusPayExchange($estudiant_id)
    {
        return ($this->TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id) >= $this->limit_omit) ? false : true ;
    }


}

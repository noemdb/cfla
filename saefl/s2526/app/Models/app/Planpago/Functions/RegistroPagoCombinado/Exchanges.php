<?php

namespace App\Models\app\Planpago\Functions\RegistroPagoCombinado;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;

trait Exchanges {


    public function getCuentaxpagarsAttribute ()
    {
        $cuentaxpagar = Cuentaxpagar::select('cuentaxpagars.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'cuentaxpagars.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->get();

        return $cuentaxpagar;
    }
    public function getCashChangesAttribute ()
    {
        $credito_a_favors = CreditoAFavor::select('credito_a_favors.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->wherenull('registro_pagos.deleted_at')
            ->wherenull('registro_pago_combinados.deleted_at')
            ->Where('registro_pago_combinados.id',$this->id)
            ->Where('credito_a_favors.status_omitted','true')
            ->Where('credito_a_favors.exchange_ammount','>=',1)
            ->get();

        return $credito_a_favors;
    }
    public function getAmmountCashChangesAttribute ()
    {
        return ($this->CashChanges->isNotEmpty()) ? $this->CashChanges->sum('exchange_ammount') : null ;
    }

    public function getIngresoCashsAttribute ()
    {
        $registro_pago_ingreso = Ingreso::select('ingresos.*')
            ->join('pagos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->wherenull('pagos.deleted_at')
            ->wherenull('registro_pagos.deleted_at')
            ->wherenull('registro_pago_combinados.deleted_at')
            ->Where('bancos.is_cash','true')
            ->Where('registro_pago_combinados.id',$this->id)
            ->get();

        $registro_pago_abonos = Ingreso::select('ingresos.*')
            ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->Where('bancos.is_cash','true')
            ->Where('registro_pago_combinados.id',$this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->wherenull('abono_aplicados.deleted_at')
            ->get();

        $cashs = $registro_pago_ingreso->merge($registro_pago_abonos); //dd($cashs);

        return $cashs;
    }

    public function getAmmountIngresoCashsAttribute ()
    {
        return ($this->IngresoCashs->isNotEmpty()) ? $this->IngresoCashs->sum('exchange_ammount') : null ;
    }


}

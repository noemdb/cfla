<?php
namespace App\Models\app\Planpago\Functions\RegistroPagoCombinado;

use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;

trait Functions
{
    public function ingresos_cuenta_x_pagar($cuentaxpagar_id,$banco_id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
        $date_expiration = new Carbon($cuentaxpagar->date_expiration);
        $date_expiration_end = $date_expiration->copy()->subMonth()->endOfMonth();

        $ingresos =

        Ingreso::select('ingresos.*','recursos.status_ingreso','recursos.status_abono')
            ->join('recursos', 'ingresos.id', '=', 'recursos.ingreso_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'recursos.registro_pago_combinado_id')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')

            ->where('registro_pago_combinados.id',$this->id)
            ->where('registro_pagos.cuentaxpagar_id',$cuentaxpagar_id)
            ->wheredate('ingresos.date_transaction','<=',$date_expiration_end)
            ->where('ingresos.banco_id', 'like', "%".$banco_id."%")

            ->whereNull('ingresos.deleted_at')
            ->whereNull('registro_pago_combinados.deleted_at')
            ->whereNull('registro_pagos.deleted_at')

            ->GroupBy('ingresos.id')
            ->get()
            ;

        return $ingresos;

    }

    public function pagos_cuenta_x_pagar($cuentaxpagar_id,$banco_id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
        $date_expiration = new Carbon($cuentaxpagar->date_expiration);
        $date_expiration_end = $date_expiration->copy()->endOfMonth()->subMonth();

        $pagos =

        Pago::select('pagos.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('registro_pago_combinados', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('recursos', 'registro_pago_combinados.id', '=', 'recursos.registro_pago_combinado_id')
            ->join('ingresos', 'ingresos.id', '=', 'recursos.ingreso_id')

            ->where('registro_pago_combinados.id',$this->id)
            ->where('registro_pagos.cuentaxpagar_id',$cuentaxpagar_id)
            ->wheredate('ingresos.date_transaction','<=',$date_expiration_end)
            ->where('ingresos.banco_id', 'like', "%".$banco_id."%")

            ->whereNull('ingresos.deleted_at')
            ->whereNull('registro_pago_combinados.deleted_at')
            ->whereNull('registro_pagos.deleted_at')

            ->GroupBy('pagos.id')
            ->get()
            ;

        return $pagos;

    }

    public function getCiRepresentantAttribute()
    {
        return (!empty($this->representant)) ? $this->representant->ci_representant: null;
    }
    public function getRepresentantNameAttribute()
    {
        return (!empty($this->representant)) ? $this->representant->name: null;
    }

}

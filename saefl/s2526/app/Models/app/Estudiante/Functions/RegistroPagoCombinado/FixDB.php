<?php
namespace App\Models\app\Estudiante\Functions\RegistroPagoCombinado;

use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

trait FixDB
{
    public static function irregular_pay($start=1,$size=100)
    {

        $datas = collect([]);

        $registro_pago_combinados = RegistroPagoCombinado::all()->slice($start,$size);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $data = collect([]);

            $pagos_ammount = $registro_pago_combinado->ammount_pagado;
            $creditos_g_ammount = $registro_pago_combinado->ammount_creditos_generados;

            $ingresos_ammount = $registro_pago_combinado->ammount_ingresos;
            $abonos_ammount = $registro_pago_combinado->ammount_abonos_aplicados;
            $creditos_a_ammount = $registro_pago_combinado->ammount_creditos_aplicados;

            $total_creditos = $ingresos_ammount + $abonos_ammount + $creditos_a_ammount;
            $total_debitos = $pagos_ammount + $creditos_g_ammount;

            $diferencia = round( ($total_debitos - $total_creditos),2);

            if ( $diferencia <> 0 ) {

                $data->put('id', $registro_pago_combinado->id);
                $data->put('registro_pago_combinado_id', $registro_pago_combinado->id);
                $data->put('created_at', $registro_pago_combinado->created_at->format('d-m-Y'));
                $data->put('representant_ci', $registro_pago_combinado->representant->ci_representant);
                $data->put('representant_name', $registro_pago_combinado->representant->name);
                $data->put('representant_id', $registro_pago_combinado->representant->id);
                $data->put('diferencia', f_float($diferencia));
                $data->put('pagos_ammount', f_float($pagos_ammount));
                $data->put('creditos_g_ammount', f_float($creditos_g_ammount));
                $data->put('total_debitos', f_float($total_debitos));
                $data->put('ingresos_ammount', f_float($ingresos_ammount));
                $data->put('abonos_ammount', f_float($abonos_ammount));
                $data->put('creditos_a_ammount', f_float($creditos_a_ammount));
                $data->put('total_creditos', f_float($total_creditos));

                $datas->push($data);
            }
        }

        return $datas;
    }

    public function getStatusIrregularAttribute()
    {
        $pagos_ammount = $this->ammount_pagado;
        $creditos_g_ammount = $this->ammount_creditos_generados;
        $total_debitado = $pagos_ammount + $creditos_g_ammount;

        $ingresos_ammount = $this->ammount_ingresos;
        $abonos_ammount = $this->ammount_abonos_aplicados;
        $creditos_a_ammount = $this->ammount_creditos_aplicados;
        $total_acreditado = $ingresos_ammount + $abonos_ammount + $creditos_a_ammount;

        $diferencia = round( ($total_debitado - $total_acreditado),2);

        return ($diferencia <> 0) ? true:false;
    }

}

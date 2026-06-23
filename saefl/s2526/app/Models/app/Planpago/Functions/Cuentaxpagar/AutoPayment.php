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

trait AutoPayment {

    public function RegistroPagoAutoPayment(RegistroPago $registro_pago,Ingreso $ingreso,Estudiant $estudiant)
    {
        // $ingreso = clone $ingreso;
        $total_recursos_ammount = $ingreso->ingreso_ammount; //dd($total_recursos_exchange);
        $total_recursos_exchange = $ingreso->exchange_ammount; //dd($total_recursos_exchange);
        $exchange_ammount_rate = $ingreso->exchange_ammount_rate; //dd($total_recursos_exchange);
        $data = Array();
        $datas = null;
        $concepto_cancelado_arr = null;

        $datas['registro_pago']=$registro_pago;

        //INI Concepto Cancelado se calcula cuantol se va a pagar
            $total_ammount_pagado = 0;
            $total_ammount_exchange_pagado = 0;
            $concepto_ammount_exchange_por_pagar = 0;
            $concepto_ammount_exchange_a_pagar = 0;

            $concepto_cancelado_id = 1000;

            $conceptopagos = $this->conceptopagos;

            foreach ($conceptopagos as $conceptopago) {

                if ($total_recursos_exchange > 0) {

                    $exchange_ammount =  $conceptopago->exchange_ammount;

                    if ($conceptopago->status_discount =='true') {
                        $descuento_ammount = $estudiant->descuento_ammount($this->id); //dd($descuento_ammount);
                        if ($descuento_ammount) {
                            $descuento_ammount = 1 - $descuento_ammount / 100;
                            $exchange_ammount =  $exchange_ammount * $descuento_ammount; //dd($descuento_ammount);
                        }
                    }

                    $concepto_cancelado_ammount_exchange = $this->TotalExchangeMontoCuentasXPagarPagado($estudiant->id); //dd($concepto_cancelado_ammount_exchange);

                    if ($exchange_ammount > $concepto_cancelado_ammount_exchange) {

                        $concepto_ammount_exchange_por_pagar = $exchange_ammount - $concepto_cancelado_ammount_exchange; //dd($concepto_ammount_exchange_por_pagar);

                        if ($concepto_ammount_exchange_por_pagar > $total_recursos_exchange) {
                            $concepto_ammount_exchange_a_pagar = $total_recursos_exchange; //dd($ingreso->exchange_ammount,$concepto_ammount_exchange_a_pagar);
                            $status_partial = 'true';
                        } else {
                            $concepto_ammount_exchange_a_pagar = $concepto_ammount_exchange_por_pagar; //dd($ingreso->exchange_ammount);
                            $status_partial = 'false';
                        }

                        $total_recursos_exchange = $total_recursos_exchange - $concepto_ammount_exchange_a_pagar;
                        $concepto_ammount_a_pagar = $concepto_ammount_exchange_a_pagar * $exchange_ammount_rate;

                        $arr = [
                            'ingreso_id' => $ingreso->id,
                            'registro_pago_id' => $registro_pago->id,
                            'concepto_pago_id' => $conceptopago->id,
                            'status_partial' => $status_partial,
                            'concepto_ammount' => $concepto_ammount_a_pagar,
                            'exchange_ammount' => $concepto_ammount_exchange_a_pagar,
                            'cuentaxpagar_name' => $this->name,
                            'estudiant_name' => $estudiant->short_name,
                        ];
                        $concepto_cancelado = ConceptoCancelado::create($arr);

                        // $concepto_cancelado = New ConceptoCancelado;
                        // $concepto_cancelado->fill($arr);
                        // $concepto_cancelado->id = $concepto_cancelado_id + 1;

                        $concepto_cancelado_arr[]=$arr; //dd($datas);

                        $total_ammount_pagado = $total_ammount_pagado + $concepto_ammount_a_pagar;
                        $total_ammount_exchange_pagado = $total_ammount_exchange_pagado + $concepto_ammount_exchange_a_pagar;

                    }
                }
            }
        //FIN Concepto Cancelado

        $datas['concepto_cancelados']=$concepto_cancelado_arr; //dd($datas);

        $datas['total_recursos_exchange']=$total_recursos_exchange; //dd($datas); //dd($datas,$ingreso);

        //INI Pagado
            if ($concepto_ammount_exchange_a_pagar) {
                $arr = [
                    'registro_pago_id' => $registro_pago->id,
                    'ingreso_id' => $ingreso->id,
                    'pagos_ammount' => $total_ammount_pagado,
                    'exchange_ammount' => $total_ammount_exchange_pagado
                ];
                $pago = Pago::create($arr);

                // $pago = New Pago;
                // $pago->fill($arr);
                // $pago->id = $registro_pago->id;

                $datas['pago']=$pago; //dd($datas);
            }
        //FIN Pagado

        /* INI descuentos aplicados */
            $descuento = $estudiant->descuento($this->id);
            if ($descuento) {
                $arr = [
                    'registro_pago_id' => $registro_pago->id,
                    'descuento_id' => $descuento->id,
                    'descuento_aplicado_observations' => 'Registro de pago automático'
                ];
                $descuento_aplicado = DescuentoAplicado::create($arr);

                // $descuento_aplicado = New DescuentoAplicado;
                // $descuento_aplicado->fill($arr);
                // $descuento_aplicado->id = $registro_pago->id;

                $datas['descuento_aplicado']=$descuento_aplicado; //dd($datas);
            }
        /* FIN descuentos aplicados */

        return $datas; //dd($datas,json_encode($datas, JSON_FORCE_OBJECT)) [JSON_PRETTY_PRINT]
    }

}

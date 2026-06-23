<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\RegistroPagoCombinado;

use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

trait CreditosAplicados {

    public function fix_creditos_aplicados()
    {
        $datas = collect([]);
        $datas_fix = collect([]);

        $registro_pago_combinados = RegistroPagoCombinado::irregular_pay(3000,500);

        // dd($registro_pago_combinados);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $registro_pagos = RegistroPago::withTrashed()->where('registro_pago_combinado_id',$registro_pago_combinado['id'])->get();

            foreach ($registro_pagos as $registro_pago) {
                $data = collect([]);

                $pago = $registro_pago->all_pago;
                $ammount_pago = round($registro_pago->all_ammont_pago,2);


                $credito_a_Favor = $registro_pago->all_credito_a_Favor;
                $ammont_credito_a_Favor = (!empty($credito_a_Favor)) ? round($credito_a_Favor->sum('credito_ammount'),2):0;

                // $credito_aplicados = $registro_pago->all_credito_aplicados;
                // $ammont_credito_aplicados = round($registro_pago->all_ammont_credito_aplicados,2);

                if ($ammount_pago<=0 && $ammont_credito_a_Favor>0) {

                    $data->put('registro_pago_combinad_id', $registro_pago->registro_pago_combinado_id);
                    $data->put('registro_pago_id', $registro_pago->id);
                    $data->put('ci_representant', $registro_pago->representant->ci_representant);
                    $data->put('created_at', $registro_pago->created_at->format('d-m-Y'));
                    $data->put('deleted_at', $registro_pago->deleted_at->format('d-m-Y'));
                    $data->put('ammount_pago', $ammount_pago);
                    $data->put('credito_a_Favor_id', $credito_a_Favor->id);
                    $data->put('ammont_credito_a_Favor', $ammont_credito_a_Favor);

                    $data->put('diferencia', $registro_pago_combinado['diferencia']);
                    $data->put('pagos_ammount', $registro_pago_combinado['pagos_ammount']);
                    $data->put('creditos_a_ammount', $registro_pago_combinado['creditos_a_ammount']);
                    $data->put('creditos_g_ammount', $registro_pago_combinado['creditos_g_ammount']);
                    $data->put('ingreso_ammount', $registro_pago_combinado['ingreso_ammount']);
                    $data->put('abonos_ammount', $registro_pago_combinado['abonos_ammount']);

                    $next_registro_pago = RegistroPago::Where('registro_pago_combinado_id',$registro_pago_combinado['id'])
                        ->where('id','<>',$registro_pago->id)
                        ->first();

                    if ($next_registro_pago) {
                        $credito_a_Favor->fill(['registro_pago_id'=>$next_registro_pago->id]);
                        $credito_a_Favor->save();
                        $data->put('next_registro_pago_id', $next_registro_pago->id);
                        $datas_fix->push($data);
                    }
                    else{
                        $datas->push($data);
                    }

                }

            }

        }

        dd($datas_fix->toarray(),$datas->toarray());

    }

}

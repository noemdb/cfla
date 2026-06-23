<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\ConceptoPago;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait FixConceptoPago {

    public function fix_concepto_pagos()
    {
        $datas = collect([]);

        $count = RegistroPagoCombinado::all()->count();

        $registro_pago_combinados = RegistroPagoCombinado::irregular_pay($start,$size); // dd($start,$size,$registro_pago_combinados);

        foreach ($registro_pago_combinados as $registro_pago_combinado) {

            $registro_pagos = RegistroPago::withTrashed()->where('registro_pago_combinado_id',$registro_pago_combinado['id'])->get();

            foreach ($registro_pagos as $registro_pago) {

                $data = $registro_pago->fix_registro_pago_zero($registro_pago_combinado);

                if ($data->isNotEmpty()) {

                    $datas->push($data);

                }

            }

        }

        dd('datas',$datas->toarray());

    }

    public function netear_caf_abn(Request $request)
    {
        $estudiants = Estudiant::active('true')->get();

        foreach ($estudiants as $estudiant) {

            $representant = $estudiant->representant;

            $creditos = $estudiant->creditos_disponibles;
            $abonos = $estudiant->abonos_disponibles;

            $creditos_abonos = $creditos->sum('credito_ammount') + $abonos->sum('credito_ammount');

            if ($creditos_abonos>0) {

                $cuentaxpagars = $estudiant->expire_bills;

                foreach ($cuentaxpagars as $cuentaxpagar) {

                    $ammount_bill = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id);

                    if ($ammount_bill>0) {

                        if ($creditos_abonos>$ammount_bill) {

                            $creditos_abonos_bill = $creditos_abonos - $ammount_bill;

                            $arr_ammount_bill[$estudiant->id][$cuentaxpagar->id] = $creditos_abonos_bill;

                            $combinado = RegistroPagoCombinado::create([
                                'representant_id' => $representant->id,
                                'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d'),
                            ]); DB::commit();
                            $arr_registro_combinado[$combinado->id] = $combinado;

                            $registro = RegistroPago::create([
                                'estudiant_id' => $estudiant->id,
                                'representant_id' => $representant->id,
                                'registro_pago_combinado_id' => $combinado->id,
                                'cuentaxpagar_id' => $cuentaxpagar->id,
                                'user_id' => Auth::user()->id
                            ]); DB::commit();
                            $arr_registro[$registro->id] = $registro;

                            //conceptos cancelados
                            $total_concepto_descuento = 0;
                            $conceptopagos = $cuentaxpagar->conceptopagos;
                            foreach ($conceptopagos as $conceptopago) {

                                $concepto_ammount =  $conceptopago->concepto_ammount;
                                if ($conceptopago->status_discount =='true' && !empty($estudiant->descuento($cuentaxpagar->id))) {
                                    $concepto_ammount =  $concepto_ammount  * (1 - $estudiant->descuento($cuentaxpagar->id) / 100);
                                }

                                $concepto_cancelado_ammount = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');

                                $total_concepto_ammount = $concepto_ammount - $concepto_cancelado_ammount;

                                $concepto_ammount_sd = $conceptopago->concepto_ammount - $concepto_cancelado_ammount;

                                $concepto_cancelado = ConceptoCancelado::create([
                                    'registro_pago_id' => $registro->id,
                                    'concepto_pago_id' => $conceptopago->id,
                                    'status_partial' => "true",
                                    'concepto_ammount' => $concepto_ammount_sd,
                                ]); DB::commit();
                                $arr_concepto_cancelado[$concepto_cancelado->id] = $concepto_cancelado;
                                $total_concepto_descuento = $total_concepto_descuento + $total_concepto_ammount;
                            }

                            $caf_ids   = '';
                            foreach ($creditos as $credito) {
                                $credito_aplicado = CreditoAplicado::create([
                                    'registro_pago_id' => $registro->id,
                                    'credito_a_favor_id' => $credito->id,
                                    'credito_aplicado_observations' => 'Crédito aplicado en el proceso de NETEO'
                                ]); DB::commit();
                                $arr_credito_aplicado[$credito_aplicado->id] = $credito_aplicado;

                                $caf_ids = $caf_ids.' - '.$credito->id;
                                $credito->delete();
                            }

                            $abono_ids = '';
                            foreach ($abonos as $abono) {
                                $abono_aplicado = AbonoAplicado::create([
                                    'registro_pago_id' => $registro->id,
                                    'abono_id' => $abono->id,
                                    'abono_aplicado_observations' => 'Abono aplicado en el proceso de NETEO'
                                ]);DB::commit();
                                $arr_abono_aplicado[$abono_aplicado->id] = $abono_aplicado;

                                $abono_ids = $abono_ids.' - '.$abono->id;
                                $abono->delete();
                            }

                            //Pagos
                            $pago = Pago::create([
                                'registro_pago_id' => $registro->id,
                                'caf_ids' => $caf_ids,
                                'abono_ids' => $abono_ids,
                                'pagos_ammount' => $total_concepto_descuento
                            ]); DB::commit();
                            $arr_pago[$pago->id] = $pago;

                            /* INI descuentos aplicados */
                            if (!empty($estudiant->descuento($cuentaxpagar->id))) {
                                $descuento = $estudiant->getDescuento($cuentaxpagar->id);
                                $descuento_aplicado =
                                    DescuentoAplicado::create([
                                    'registro_pago_id' => $registro->id,
                                    'descuento_id' => $descuento->id,
                                    'descuento_aplicado_observations' => $request->credito_aplicado_observations
                                ]); DB::commit();
                                $arr_descuento_aplicado[$descuento_aplicado->id] = $descuento_aplicado;
                            }
                            /* FIN descuentos aplicados */

                            $total = $creditos_abonos - $total_concepto_descuento;
                            if ( $total > 0) {
                                $credito_ammount = round($total,2);
                                $credito_a_Favor = CreditoAFavor::create([
                                    'representant_id' => $representant->id,
                                    'estudiant_id' => $estudiant->id,
                                    'registro_pago_id' => $registro->id,
                                    'credito_description' => 'CAF: REGID'.$registro->id.';F'.Carbon::now(),
                                    'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado del registro de pago ID: '.$registro->id.' | PROCESO DE NETEO',
                                    'credito_ammount' => $credito_ammount,
                                ]); DB::commit();
                                $arr_credito_a_Favor[$credito_a_Favor->id] = $credito_a_Favor;
                            }

                        }

                    }

                }
            }


        }

        dd($arr_ammount_bill,$arr_registro_combinado,$arr_registro,$arr_concepto_cancelado,$arr_credito_aplicado,$arr_abono_aplicado,$arr_pago,$arr_credito_a_Favor);

    }

}

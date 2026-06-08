<?php

namespace App\Http\Controllers\Administracion\Tab\Functions\RegistroPago;

use App\Http\Requests\Administracion\Planpago\CreateRegistroParcialRequest;
use Illuminate\Http\Request;
//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Planpago\ExchangeRate;

use App\Http\Controllers\Admin\Fix\DB\HomeController as FixDBControlller;
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoRepresentantRequest;
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoRequest;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Recurso;
use Illuminate\Support\Facades\Validator;

trait Store {

    public function store_representant_exchange(CreateRegistroPagoRepresentantRequest $request)
    {
        //dd($request->all());
        $representant = Representant::findOrFail($request->representant_id);
        $date_current = Carbon::now()->format('Y-m-d');
        $method_pay_id = $request->method_pay_id;

        $ingreso_ammount = $request->ingreso_ammount;
        $date_payment = $request->date_payment;
        $exchange_rate_current = ExchangeRate::whereDate('date',$date_payment)->first();
        $ingreso_exchange_rate_id = ($exchange_rate_current) ? $exchange_rate_current->id : null;
        $ingreso_exchange_ammount = ($exchange_rate_current) ? $request->ingreso_ammount / $exchange_rate_current->ammount : null;

        $total_ing_ammount = 0;
        $total_ing_ammount_exchange = 0;
        $total_ingreso_credito = 0;
        $total_ingreso_credito_exchange = 0;

        $total_ingreso_credito_abono = 0;
        $total_ingreso_credito_abono_exchange = 0;

        $total_ing_caf_abn_ammount = 0;
        $total_ing_caf_abn_ammount_exchange = 0;

        $total_recursos = 0;
        $total_recursos_exchange = 0;

        $total_pagado = 0;
        $total_pagado_exchange = 0;

        $cuentaxpagar_arr = $request->cuentaxpagar_id; //dd($cuentaxpagar_arr);
        $cuentaxpagar_ammount_arr = $request->cuentaxpagar_ammount;

        $credito_arr = (!empty($request->credito)) ? $request->credito : array();
        $credito_ammount_arr = (!empty($request->credito_ammount)) ? $request->credito_ammount : array();
        $credito_exchange_ammount_arr = (!empty($request->credito_exchange_ammount)) ? $request->credito_exchange_ammount : array();

        $registro_id = '';
        $ingreso_id = '';

        $combinado = RegistroPagoCombinado::create([
            'representant_id' => $representant->id,
            'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d'),
        ]);

        //INI Ingreso
            // if ($method_pay_id <> '1') { // el 1 se refiere a credistos a favor (no se registrar ingreso)
                $ingreso = Ingreso::create([
                    'estudiant_id' => $representant->estudiants()->first()->id,
                    'representant_id' => $representant->id,
                    'method_pay_id' => $method_pay_id,
                    'banco_id' => $request->banco_id,
                    'number_i_pay' =>$request->number_i_pay,
                    'date_transaction' =>$request->date_transaction,
                    'date_payment' =>$request->date_payment,
                    'ingreso_ammount' => $request->ingreso_ammount,
                    'exchange_rate_id' => $ingreso_exchange_rate_id,
                    'exchange_ammount' => $ingreso_exchange_ammount,
                    'ingreso_observations' => $request->ingreso_observations,
                    'person_bill_ci' => $representant->ci_representant,
                    'person_bill_name' =>$representant->name,
                ]);
                $recurso_ingreso = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_ingreso' => 'true']);
                $total_recursos = $ingreso_ammount;
                $total_recursos_exchange = $ingreso_exchange_ammount;
            // }
        //FIN Ingreso

        foreach ($cuentaxpagar_arr as $estudiant_id => $cuentaxpagar_in) {

            $estudiant = Estudiant::findOrFail($estudiant_id);

            foreach ($cuentaxpagar_in as $id => $selected) {

                // $total_recursos = 0;
                // $total_recursos_exchange = 0;

                $total_pagado = 0;
                $total_pagado_exchange = 0;

                if ($selected=='true') {

                    $cuentaxpagar = Cuentaxpagar::findOrFail($id);

                    //INI RegistroPago
                        $registro = RegistroPago::create([
                            'estudiant_id' => $estudiant->id,
                            'representant_id' => $representant->id,
                            'registro_pago_combinado_id' => $combinado->id,
                            'cuentaxpagar_id' => $cuentaxpagar->id,
                            'user_id' => Auth::user()->id
                        ]);
                        $registro_arr[] = $registro;
                        $registro_id = $registro_id.'ID: '.$registro->id.' | '; //dd($registro);
                    //FIN RegistroPago

                    //INI Concepto Cancelado
                        $total_concepto_descuento = 0;
                        $total_concepto_descuento_exchange = 0;
                        $conceptopagos = $cuentaxpagar->conceptopagos; //dd($conceptopagos);
                        foreach ($conceptopagos as $conceptopago) {
                            $concepto_ammount =  $conceptopago->concepto_ammount;
                            $exchange_ammount =  $conceptopago->exchange_ammount;

                            if ($conceptopago->status_discount =='true' && !empty($estudiant->descuento_ammount($cuentaxpagar->id))) {
                                $concepto_ammount =  $concepto_ammount  * (1 - $estudiant->descuento_ammount($cuentaxpagar->id) / 100);
                                $exchange_ammount =  $exchange_ammount  * (1 - $estudiant->descuento_ammount($cuentaxpagar->id) / 100);
                            }

                            $concepto_cancelado_ammount = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                            $total_concepto_ammount = $concepto_ammount - $concepto_cancelado_ammount;
                            $concepto_ammount_sd = $conceptopago->concepto_ammount - $concepto_cancelado_ammount;

                            // $concepto_cancelado_ammount_exchange = $conceptopago->AmmountParcial($estudiant->id)->sum('exchange_ammount_parcial');
                            $concepto_cancelado_ammount_exchange = $exchange_ammount * ($concepto_cancelado_ammount / $conceptopago->concepto_ammount) ;
                            $total_concepto_ammount_exchange = $exchange_ammount - $concepto_cancelado_ammount_exchange;
                            $concepto_ammount_sd_exchange = $conceptopago->exchange_ammount - $concepto_cancelado_ammount_exchange;

                            $status_partial = ($concepto_cancelado_ammount > 0) ? 'true':'false';

                            $concepto_create = ConceptoCancelado::create([
                                'registro_pago_id' => $registro->id,
                                'concepto_pago_id' => $conceptopago->id,
                                'status_partial' => $status_partial,
                                'concepto_ammount' => $concepto_ammount_sd,
                                'exchange_ammount' => $concepto_ammount_sd_exchange,
                            ]);
                            $total_concepto_descuento = $total_concepto_descuento + $total_concepto_ammount;

                            $total_concepto_descuento_exchange = $total_concepto_descuento_exchange + $total_concepto_ammount_exchange;
                            //dd($total_concepto_ammount_exchange,$conceptopago->exchange_ammount);
                        }
                        $total_concepto_descuento = $cuentaxpagar->TotalMontoCuentasXPagarAdeudado($estudiant->id);
                        $total_concepto_descuento_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                        //$total_concepto_descuento_exchange = round($total_concepto_descuento_exchange,2);
                        //dd($conceptopagos,$total_concepto_descuento_exchange);
                    //FIN Concepto Cancelado

                     /* INI CreditoAplicado */
                        $credito_total = 0;
                        $credito_total_exchange = 0;
                        $creditos_ids = null;
                        $creditos = $representant->creditos_disponibles;
                        foreach ($creditos as $credito) {
                            $credito_aplicado = CreditoAplicado::create([
                                'registro_pago_id' => $registro->id,
                                'credito_a_favor_id' => $credito->id,
                                'credito_aplicado_observations' => $request->credito_aplicado_observations
                            ]);
                            $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'credito_a_favor_id' => $credito->id,'status_credito' => 'true']);
                            $credito_total = $credito_total + $credito->credito_ammount;
                            $credito_total_exchange = $credito_total_exchange + $credito->exchange_ammount;
                            $creditos_ids .= $credito->id.';';
                            $credito->delete();
                        }

                        $total_ingreso_credito =  $ingreso_ammount + $credito_total;
                        $total_ingreso_credito_exchange =  $ingreso_exchange_ammount + $credito_total_exchange;
                    /* FIN CreditoAplicado */

                    /* INI AbonoAplicado */
                        $abono_total = 0;
                        $abono_total_exchange = 0;
                        $abonos_ids = null;
                        if (is_array($request->abono)) {
                            $arr_dat = $request->abono; //dd($arr_dat);
                            foreach ($arr_dat as $k => $v) {
                                if ($v == 'true') {
                                    $abono_aplicado = AbonoAplicado::where('abono_id',$k)->first();
                                    if (!$abono_aplicado) {
                                        $abono_aplicado_create = AbonoAplicado::create([
                                            'registro_pago_id' => $registro->id,
                                            'abono_id' => $k,
                                            'abono_aplicado_observations' => $request->abono_aplicado_observations
                                        ]);

                                        $abono = Abono::findOrFail($k);
                                        $ingreso_aplicado = Ingreso::findOrFail($abono->ingreso_id);
                                        $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso_aplicado->id,'status_abono' => 'true']);

                                        $abono_total = $abono_total + $ingreso_aplicado->ingreso_ammount;
                                        $abono_total_exchange = $abono_total_exchange + $ingreso_aplicado->exchange_ammount;
                                        $abonos_ids .= $k.';';
                                        $abono->delete();
                                    }

                                }
                            }
                        }
                        $total_ingreso_credito_abono =  $total_ingreso_credito + $abono_total;
                        $total_ingreso_credito_abono_exchange =  $total_ingreso_credito_exchange + $abono_total_exchange; //dd($total_ingreso_credito_abono,$total_ingreso_credito_abono_exchange);
                    /* FIN AbonoAplicado */

                    //INI Pagado
                        $factor = null;
                        $abono_ids = '';
                        $caf_ids = '';
                        // if ($method_pay_id <> '1') { // el 4 se refiere a credistos a favor (no se registrar ingreso)

                            // $pagos_ammount = $total_concepto_descuento_exchange * $exchange_rate_current->ammount;

                            $pagos_ammount_100 = $credito_total + $abono_total + $ingreso_ammount;

                            $total_credito_abono_exchange_100 = $credito_total_exchange + $abono_total_exchange + $ingreso_exchange_ammount;
                            $factor = $total_concepto_descuento_exchange / $total_credito_abono_exchange_100 ;
                            $pagos_ammount = $factor * $pagos_ammount_100;

                            $pago = Pago::create([
                                'registro_pago_id' => $registro->id,
                                'ingreso_id' => $ingreso->id,
                                'pagos_ammount' => $pagos_ammount,
                                'exchange_ammount' => $total_concepto_descuento_exchange
                            ]);
                            $ingreso_ammount = 0;
                            $ingreso_exchange_ammount = 0;
                            $method_pay_id = 1;
                        // }
                        // else{
                        //     $abono_ids = (is_array($request->abono)) ? implode(';',array_keys($request->abono)) : null ;
                        //     $caf_ids = (is_array($request->credito_a_favor)) ? implode(';',array_keys($request->credito_a_favor)) : null ;

                        //     $pagos_ammount_100 = $credito_total + $abono_total;

                        //     $total_credito_abono_exchange_100 = $credito_total_exchange + $abono_total_exchange;
                        //     $factor = $total_concepto_descuento_exchange / $total_credito_abono_exchange_100 ;
                        //     $pagos_ammount = $factor * $pagos_ammount_100;

                        //     $pago = Pago::create([
                        //         'registro_pago_id' => $registro->id,
                        //         'abono_ids' => $abono_ids,
                        //         'caf_ids' => $caf_ids,
                        //         'pagos_ammount' => $pagos_ammount,
                        //         'exchange_ammount' => $total_concepto_descuento_exchange
                        //     ]);
                        // }
                        $total_pagado = $total_pagado + $pagos_ammount; //dd($combinado,$ingreso,$registro,$concepto_create,$pago);
                        $total_pagado_exchange = $total_pagado_exchange + $total_concepto_descuento_exchange; //dd($combinado,$ingreso,$registro,$concepto_create,$pago);
                    //FIN Pagado

                    $total_recursos = $total_ingreso_credito_abono - $total_pagado;
                    $total_recursos_exchange = $total_ingreso_credito_abono_exchange - $total_pagado_exchange;

                    if ($total_recursos_exchange > 0) {
                        // $status_omitted = ($total_recursos_exchange < 0.001) ? 'false' : 'true' ;
                        $credito_a_Favor_new = CreditoAFavor::create([
                            'representant_id' => $representant->id,
                            'estudiant_id' => $estudiant->id,
                            'registro_pago_id' => $registro->id,
                            'ingreso_id' => $ingreso_id,
                            'credito_description' => 'CAF: REGID'.$registro->id.';F'.Carbon::now(),
                            'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado de los registro de pago ID: '.$registro_id.' | Transacción: ID: '.$ingreso_id.' - Número: '.$request->number_i_pay,
                            'credito_ammount' => $total_recursos,
                            'exchange_ammount' => $total_recursos_exchange
                        ]);
                        //dd($total_recursos_exchange);
                    }

                    /* INI descuentos aplicados */
                        $descuento_total = 0;
                        $descuento = $estudiant->descuento($cuentaxpagar->id);
                        if ($descuento) {
                            $descuentoAplicado = DescuentoAplicado::create([
                                'registro_pago_id' => $registro->id,
                                'descuento_id' => $descuento->id,
                                'descuento_aplicado_observations' => $request->credito_aplicado_observations
                            ]);
                            $descuento_ammount = Descuento::findOrFail($descuento->id)->descuento_ammount;
                            $descuento_total = $descuento_total + $descuento_ammount;
                        }
                    /* FIN descuentos aplicados */

                }
            }
        }

        //dd($total_recursos,$total_recursos_exchange);

        $search = $representant->ci_representant;
        $representant_id = $representant->id;
        $id = $representant->id;
        $help_representante = $representant->ci_representant;
        $pago_combinado = $combinado;
        Session::flash('operp_ok','Registros guardado exitosamente');
        
        return redirect()->route('administracion.registropagos.create_representant_exchange',compact('id','search','help_representante','representant_id','pago_combinado'));
    }

    public function parcial_store(CreateRegistroParcialRequest $request)
    {
        // dd($request->all());
        $estudiant = Estudiant::findOrFail($request->estudiant_id);
        $id = $estudiant->id;
        $cuentaxpagar = Cuentaxpagar::findOrFail($request->cuentaxpagar_id);
        $conceptopagos = $cuentaxpagar->conceptopagos;
        $representant = $estudiant->representant;
        $ingreso_ammount = $request->ingreso_ammount;
        $concepto_pago_arr = $request->concepto_pago;
        $concepto_pago_ammount_arr = $request->concepto_ammount;

        $total_cobro = 0;
        foreach ($conceptopagos as $conceptopago) {

            $status_paid_current = (!empty($conceptopago->concepto_cancelado) ) ? $conceptopago->concepto_cancelado->status_paid : false ;

            if (array_key_exists($conceptopago->id, $concepto_pago_arr) && !( $status_paid_current )) {

                if ($concepto_pago_arr[$conceptopago->id]=='true') {

                    $concepto_ammount = $conceptopago->concepto_ammount;

                    $concepto_cancelado_ammount = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');

                    $total_concepto_ammount = $concepto_ammount - $concepto_cancelado_ammount;

                    $total_cobro += $total_concepto_ammount;

                }
            }
        }

        // dd($request->all(),$total_cobro,$cuentaxpagar,$conceptopagos);

        if (is_array($concepto_pago_arr) && array_search("true",$concepto_pago_arr) && $total_cobro>0) {

            $combinado = RegistroPagoCombinado::create([
                'representant_id' => $representant->id,
                'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d'),
            ]);

            $total_pagado = 0;
            $registro_id = null;

            //INI registro del ingreso
            $ingreso = new Ingreso();
            if ($request->method_pay_id <> '1') { // el 1 se refiere a credistos a favor (no se registrar ingreso)
                $ingreso = ([
                    'estudiant_id' => $estudiant->id,
                    'representant_id' => $representant->id,
                    'method_pay_id' => $request->method_pay_id,
                    'banco_id' => $request->banco_id,
                    'number_i_pay' =>$request->number_i_pay,
                    'date_transaction' =>$request->date_transaction,
                    'date_transaction' =>$request->date_payment,
                    'ingreso_ammount' => $request->ingreso_ammount,
                    'ingreso_observations' => $request->ingreso_observations,
                    'person_bill_ci' => $representant->ci_representant,
                    'person_bill_name' =>$representant->name,
                ]);
                $recurso_ingreso = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_ingreso' => 'true']);

            }
            $ingreso_id = (!empty($ingreso->id)) ? $ingreso->id : null ;
            //FIN registro del ingreso

            //INI registro del ingreso
            $registro = new RegistroPago();
            $registro = RegistroPago::create([
                'estudiant_id' => $request->estudiant_id,
                'representant_id' => $representant->id,
                'registro_pago_combinado_id' => $combinado->id,
                'cuentaxpagar_id' => $request->cuentaxpagar_id,
                'user_id' => Auth::user()->id
            ]);
            $registro_id = (!empty($registro->id)) ? $registro->id : null ;
            //FIN registro del ingreso

            // dd($ingreso,$registro);

            //INI credito_a_favor
            $credito_total = 0;
            $creditos_ids = null;
            $credito_aplicado = new CreditoAplicado();
            if (is_array($request->credito_a_favor)) {
                $arr_dat = $request->credito_a_favor;
                foreach ($arr_dat as $k => $v) {
                    if ($v == 'true') {
                        if ($representant->CreditosAFavorDisponiblesTest($k)) {
                            $credito_aplicado = CreditoAplicado::create([
                                'registro_pago_id' => $registro_id,
                                'credito_a_favor_id' => $k,
                                'credito_aplicado_observations' => $request->credito_aplicado_observations
                            ]);

                            $credito = CreditoAFavor::findOrFail($k);
                            $credito_total = $credito_total + $credito->credito_ammount;
                            $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'credito_a_favor_id' => $credito->id,'status_credito' => 'true']);
                            $creditos_ids .= $k.';';
                            $credito->delete();
                        }
                    }
                }
            }
            //FIN credito_a_favor
            $total_ingreso_credito =  $ingreso_ammount + $credito_total;

            // dd($ingreso,$registro,$credito_aplicado);

            $abono_total = 0;
            $abonos_ids = null;
            $abono_aplicado = new AbonoAplicado();
            if (is_array($request->abono)) {
                $arr_dat = $request->abono;
                foreach ($arr_dat as $k => $v) {
                    if ($v == 'true') {
                        // if ($representant->AbonosDisponiblesTest($k)) {
                            $abono_aplicado = AbonoAplicado::create([
                                'registro_pago_id' => $registro->id,
                                'abono_id' => $k,
                                'abono_aplicado_observations' => $request->abono_aplicado_observations
                            ]);
                            $abono = Abono::findOrFail($k);
                            $ingreso = Ingreso::findOrFail($abono->ingreso_id);
                            $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_abono' => 'true']);
                            $abono_total = $abono_total + $ingreso->ingreso_ammount;
                            $abonos_ids .= $k.';';
                            $abono->delete();
                        // }
                    }
                }
            }
            $total_ingreso_credito_abono =  $total_ingreso_credito + $abono_total;

            /* INI descuentos aplicados */
            $descuento_total = 0;
            if (is_array($request->descuento)) {
                $arr_dat = $request->descuento;
                foreach ($arr_dat as $k => $v) {
                    if ($v == 'true') {
                        if (!empty($estudiant->descuento_ammount($cuentaxpagar->id))) {
                            $descuento = DescuentoAplicado::create([
                                'registro_pago_id' => $registro->id,
                                'descuento_id' => $k,
                                'descuento_aplicado_observations' => $request->credito_aplicado_observations
                            ]);
                            $descuento_ammount = Descuento::findOrFail($k)->descuento_ammount;
                            $descuento_total = $descuento_total + $descuento_ammount;
                        }
                    }
                }
            }
            /* FIN descuentos aplicados */

            // dd($ingreso,$registro,$credito_aplicado,$abono_aplicado,$total_ingreso_credito_abono);

            //INI Concepto Cancelado
            $total_pagado_concepto = 0;
            $total_ingreso_credito_abono_concepto = $total_ingreso_credito_abono;
            foreach ($conceptopagos as $conceptopago) {

                $status_paid_current = (!empty($conceptopago->concepto_cancelado) ) ? $conceptopago->concepto_cancelado->status_paid : false ;

                if (array_key_exists($conceptopago->id, $concepto_pago_arr) && $total_ingreso_credito_abono_concepto>0 && !( $status_paid_current )) {

                    if ($concepto_pago_arr[$conceptopago->id] == "true") {

                        $concepto_ammount =  $conceptopago->concepto_ammount;
                        if ($conceptopago->status_discount =='true' && !empty($estudiant->descuento_ammount($cuentaxpagar->id))) {
                            $concepto_ammount =  $concepto_ammount  * (1 - $estudiant->descuento_ammount($cuentaxpagar->id) / 100);
                        }

                        $concepto_cancelado_ammount = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');

                        $total_concepto_ammount = $concepto_ammount - $concepto_cancelado_ammount;

                        $concepto_ammount = $conceptopago->concepto_ammount - $concepto_cancelado_ammount;

                        // dd($total_ingreso_credito_abono_concepto,$total_concepto_ammount);

                        // if ($total_ingreso_credito_abono_concepto > $total_concepto_ammount) {
                        if ($total_ingreso_credito_abono_concepto >= $total_concepto_ammount) {
                            $status_paid = "true";
                            $total_ingreso_credito_abono_concepto = $total_ingreso_credito_abono_concepto - $total_concepto_ammount;
                        } else {
                            $total_concepto_ammount = $total_ingreso_credito_abono_concepto;
                            $status_paid = "false";
                            $total_ingreso_credito_abono_concepto = 0;
                        }

                        $concepto_create = ConceptoCancelado::create([
                            'registro_pago_id' => $registro->id,
                            'concepto_pago_id' => $conceptopago->id,
                            'concepto_ammount' => $total_concepto_ammount,
                            // 'concepto_ammount' => $concepto_ammount,
                            'status_partial' => "true",
                            'status_paid' => $status_paid,
                        ]);
                        $total_pagado_concepto = $total_pagado_concepto + $total_concepto_ammount;
                    }
                }
            }
            //FIN Concepto Cancelado

            // dd($ingreso,$registro,$credito_aplicado,$abono_aplicado,$total_ingreso_credito_abono,$total_ingreso_credito_abono_concepto,$total_pagado_concepto);

            $abono_ids = '';
            $caf_ids = '';
            if ($request->method_pay_id <> '1') { // el 4 se refiere a credistos a favor (no se registrar ingreso)
                $pago = Pago::create([
                    'registro_pago_id' => $registro->id,
                    'ingreso_id' => $ingreso->id,
                    'pagos_ammount' => $total_pagado_concepto
                ]);
            }
            else{
                $abono_ids = (is_array($request->abono)) ? implode(';',array_keys($request->abono)) : null ;
                $caf_ids = (is_array($request->credito_a_favor)) ? implode(';',array_keys($request->credito_a_favor)) : null ;
                $pago = Pago::create([
                    'registro_pago_id' => $registro->id,
                    'abono_ids' => $abono_ids,
                    'caf_ids' => $caf_ids,
                    'pagos_ammount' => $total_pagado_concepto
                ]);
            }
            // $total_pagado = $total_pagado_concepto;

            // dd($ingreso,$registro,$credito_aplicado,$abono_aplicado,$total_ingreso_credito_abono,$total_ingreso_credito_abono_concepto,$total_pagado_concepto);

            /* INI Creditos a favor generados */
            $credito_ammount = null;
            if ( $total_ingreso_credito_abono > $total_pagado_concepto) {
                $credito_ammount = round(($total_ingreso_credito_abono - $total_pagado_concepto),2);
                // $credito_ammount = round($credito_ammount,2);
                $credito_a_Favor = CreditoAFavor::create([
                    'representant_id' => $representant->id,
                    'estudiant_id' => $estudiant->id,
                    'registro_pago_id' => $registro->id,
                    'ingreso_id' => $ingreso_id,
                    'credito_description' => 'CAF: REGID'.$registro_id.';F'.Carbon::now(),
                    'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado de los registro de pago ID: '.$registro_id.' | Transacción: ID: '.$ingreso_id.' - Número: '.$request->number_i_pay,
                    'credito_ammount' => $credito_ammount,
                ]);
            }
            /* FIN Creditos a favor generados */

            $data_arr = [
                'ingreso'=>$ingreso,'registro'=>$registro,'credito_aplicado'=>$credito_aplicado,
                'abono_aplicado'=>$abono_aplicado,'total_ingreso_credito_abono'=>$total_ingreso_credito_abono,'total_ingreso_credito_abono_concepto'=>$total_ingreso_credito_abono_concepto,
                'total_pagado_concepto'=>$total_pagado_concepto,'credito_ammount'=>$credito_ammount,'status_paid'=>$status_paid,
                'total_concepto_ammount'=>$total_concepto_ammount,//'credito_ammount'=>$credito_ammount,'status_paid'=>$status_paid
            ];

            $messenge = trans('db_oper_result.update_ok');
            $operp = 'operp_ok';
        }
        else{
            $messenge = trans('db_oper_result.update_ok');
            $operp = 'operp_no_ok';
        }

        Session::flash($operp,$messenge);

        // $fix_zero = new FixDBControlller;
        // $fix_zero->fix_paid_zero();

        $id = $estudiant->id;
        return redirect()->route('administracion.registropagos.parcial.create',compact('id'));
    }

    public function representant_store(CreateRegistroPagoRepresentantRequest $request)
    {
        // dd($request->all());
        $representant = Representant::findOrFail($request->representant_id);
        $date_current = Carbon::now()->format('Y-m-d');

        $estudiant = $representant->estudiants->first();
        $ingreso_ammount = $request->ingreso_ammount;
        $cuentaxpagar_arr = $request->cuentaxpagar_id;
        $concepto_pago_arr = $request->concepto_pago;
        // dd($concepto_pago_arr);

        $total_pagado = 0;
        $registro_id = '';
        $ingreso_id = null;

        $combinado = RegistroPagoCombinado::create([
            'representant_id' => $representant->id,
            'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d'),
        ]);

        //INI Ingreso
        if ($request->method_pay_id <> '1') { // el 1 se refiere a credistos a favor (no se registrar ingreso)
            $exchange_rate_current = ExchangeRate::whereDate('date',$request->date_payment)->first(); //dd($rate_current);
            $exchange_ammount = $request->ingreso_ammount / $exchange_rate_current->ammount ;
            $ingreso = Ingreso::create([
                'estudiant_id' => $representant->estudiants()->first()->id,
                'representant_id' => $representant->id,
                'method_pay_id' => $request->method_pay_id,
                'banco_id' => $request->banco_id,
                'number_i_pay' =>$request->number_i_pay,
                'date_transaction' =>$request->date_transaction,
                'date_payment' =>$request->date_payment,
                'ingreso_ammount' => $request->ingreso_ammount,
                'exchange_rate_id' => $exchange_rate_current->id,
                'exchange_ammount' => $exchange_ammount,
                'ingreso_observations' => $request->ingreso_observations,
                'person_bill_ci' => $representant->ci_representant,
                'person_bill_name' =>$representant->name,
            ]);
            $recurso_ingreso = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_ingreso' => 'true']);
        }
        //FIN Ingreso

        foreach ($cuentaxpagar_arr as $estudiant_id => $cuentaxpagars) {

            $ingreso_total = 0;

            foreach ($cuentaxpagars as $cuentaxpagar_id => $cuentaxpagar_monto) {

                $concepto_count = 0;
                $test_conceptopagos = ConceptoPago::where('cuentaxpagar_id',$cuentaxpagar_id)->get();
                foreach ($test_conceptopagos as $conceptopago){
                    if (array_key_exists($estudiant_id,$concepto_pago_arr)) {
                        if (array_key_exists($conceptopago->id,$concepto_pago_arr[$estudiant_id])) {
                            if ($concepto_pago_arr[$estudiant_id][$conceptopago->id]=='true') {
                                $concepto_count=$concepto_count+1;
                            }
                        }
                    }
                }

                if ($concepto_count>0) {

                    $estudiant = Estudiant::findOrFail($estudiant_id);
                    $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);

                    $registro = RegistroPago::create([
                        'estudiant_id' => $estudiant->id,
                        'representant_id' => $representant->id,
                        'registro_pago_combinado_id' => $combinado->id,
                        'cuentaxpagar_id' => $cuentaxpagar_id,
                        'user_id' => Auth::user()->id
                    ]);
                    $registro_arr[] = $registro;
                    $registro_id = $registro_id.'ID: '.$registro->id.' | ';

                    //INI Concepto Cancelado
                    $total_concepto_descuento = 0;
                    $total_concepto_descuento_exchange = 0;
                    $conceptopagos = $cuentaxpagar->conceptopagos;
                    foreach ($conceptopagos as $conceptopago) {
                        $test = (!empty($concepto_pago_arr[$estudiant->id][$conceptopago->id])) ? $concepto_pago_arr[$estudiant->id][$conceptopago->id] : 'false' ;
                        if ($test == 'true') {

                            $concepto_ammount =  $conceptopago->concepto_ammount;
                            $exchange_ammount =  $conceptopago->exchange_ammount;
                            if ($conceptopago->status_discount =='true' && !empty($estudiant->descuento_ammount($cuentaxpagar->id))) {
                                $concepto_ammount =  $concepto_ammount  * (1 - $estudiant->descuento_ammount($cuentaxpagar->id) / 100);
                                $exchange_ammount =  $exchange_ammount  * (1 - $estudiant->descuento_ammount($cuentaxpagar->id) / 100);
                            }

                            $concepto_cancelado_ammount = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                            $total_concepto_ammount = $concepto_ammount - $concepto_cancelado_ammount;
                            $concepto_ammount_sd = $conceptopago->concepto_ammount - $concepto_cancelado_ammount;

                            $concepto_cancelado_ammount_exchange = $conceptopago->AmmountParcial($estudiant->id)->sum('exchange_ammount_parcial');
                            $total_concepto_ammount_exchange = $exchange_ammount - $concepto_cancelado_ammount_exchange;
                            $concepto_ammount_sd_exchange = $conceptopago->exchange_ammount - $concepto_cancelado_ammount_exchange;

                            $status_partial = ($concepto_cancelado_ammount > 0) ? 'true':'false';

                            $concepto_create = ConceptoCancelado::create([
                                'registro_pago_id' => $registro->id,
                                'concepto_pago_id' => $conceptopago->id,
                                // 'status_partial' => "true",
                                'status_partial' => $status_partial,
                                // 'concepto_ammount' => $conceptopago->concepto_ammount,
                                'concepto_ammount' => $concepto_ammount_sd,

                                'exchange_ammount' => $concepto_ammount_sd_exchange,

                            ]);
                            $total_concepto_descuento = $total_concepto_descuento + $total_concepto_ammount;

                            $total_concepto_descuento_exchange = $total_concepto_descuento_exchange + $total_concepto_ammount_exchange;
                        }
                    }
                    //FIN Concepto Cancelado

                    //INI Pagado
                    $abono_ids = '';
                    $caf_ids = '';
                    if ($request->method_pay_id <> '1') { // el 4 se refiere a credistos a favor (no se registrar ingreso)
                        $pago = Pago::create([
                            'registro_pago_id' => $registro->id,
                            'ingreso_id' => $ingreso->id,
                            'pagos_ammount' => $total_concepto_descuento,
                            'exchange_ammount' => $total_concepto_descuento_exchange
                        ]);
                    }
                    else{
                        $abono_ids = (is_array($request->abono)) ? implode(';',array_keys($request->abono)) : null ;
                        $caf_ids = (is_array($request->credito_a_favor)) ? implode(';',array_keys($request->credito_a_favor)) : null ;
                        $pago = Pago::create([
                            'registro_pago_id' => $registro->id,
                            'abono_ids' => $abono_ids,
                            'caf_ids' => $caf_ids,
                            'pagos_ammount' => $total_concepto_descuento,
                            'exchange_ammount' => $total_concepto_descuento_exchange
                        ]);
                    }
                    $total_pagado = $total_pagado + $total_concepto_descuento;
                    //FIN Pagado

                    /* INI descuentos aplicados */
                    $descuento_total = 0;
                    if (is_array($request->descuento)) {
                        $arr_dat = $request->descuento;
                        foreach ($arr_dat as $k => $v) {
                            if ($v == 'true') {
                                if (!empty($estudiant->descuento($cuentaxpagar_id))) {
                                    $descuento = DescuentoAplicado::create([
                                        'registro_pago_id' => $registro->id,
                                        'descuento_id' => $k,
                                        'descuento_aplicado_observations' => $request->credito_aplicado_observations
                                    ]);
                                    $descuento_ammount = Descuento::findOrFail($k)->descuento_ammount;
                                    $descuento_total = $descuento_total + $descuento_ammount;
                                }
                            }
                        }
                    }
                    /* FIN descuentos aplicados */
                }
            }

        }

        $credito_total = 0;
        $creditos_ids = null;
        if (is_array($request->credito_a_favor)) {
            $arr_dat = $request->credito_a_favor;
            foreach ($arr_dat as $k => $v) {
                if ($v == 'true') {
                    if ($representant->CreditosAFavorDisponiblesTest($k)) {
                        $credito = CreditoAplicado::create([
                            'registro_pago_id' => $registro->id,
                            'credito_a_favor_id' => $k,
                            'credito_aplicado_observations' => $request->credito_aplicado_observations
                        ]);
                        $credito = CreditoAFavor::findOrFail($k);
                        $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'credito_a_favor_id' => $credito->id,'status_credito' => 'true']);
                        $credito_total = $credito_total + $credito->credito_ammount;
                        $creditos_ids .= $k.';';
                        $credito->delete();
                    }
                }
            }
        }
        $total_ingreso_credito =  $ingreso_ammount + $credito_total;

        $abono_total = 0;
        $abonos_ids = null;
        if (is_array($request->abono)) {
            $arr_dat = $request->abono;
            foreach ($arr_dat as $k => $v) {
                if ($v == 'true') {
                        $abono = AbonoAplicado::create([
                            'registro_pago_id' => $registro->id,
                            'abono_id' => $k,
                            'abono_aplicado_observations' => $request->abono_aplicado_observations
                        ]);
                        $abono = Abono::findOrFail($k);
                        $ingreso = Ingreso::findOrFail($abono->ingreso_id);
                        $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_abono' => 'true']);
                        $abono_total = $abono_total + $abono->ingreso->ingreso_ammount;
                        $abonos_ids .= $k.';';
                        $abono->delete();
                }
            }
        }
        $total_ingreso_credito_abono =  $total_ingreso_credito + $abono_total;

        /* INI Creditos a favor generados */
        if ( $total_ingreso_credito_abono > $total_pagado) {
            $credito_ammount = $total_ingreso_credito_abono - $total_pagado;
            $credito_ammount = round($credito_ammount,2);
            $credito_a_Favor = CreditoAFavor::create([
                'representant_id' => $representant->id,
                'estudiant_id' => $estudiant->id,
                'registro_pago_id' => $registro->id,
                'ingreso_id' => $ingreso_id,
                'credito_description' => 'CAF: REGID'.$registro_id.';F'.Carbon::now(),
                'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado de los registro de pago ID: '.$registro_id.' | Transacción: ID: '.$ingreso_id.' - Número: '.$request->number_i_pay,
                'credito_ammount' => $credito_ammount,
            ]);
        }
        /* FIN Creditos a favor generados */

        // $fix_zero = new FixDBControlller;
        // $fix_zero->fix_paid_zero();

        $representant       = Representant::findOrFail($request->representant_id);
        $search = $representant->ci_representant;
        $estudiants         = Estudiant::where('representant_id',$representant->id)->active('true')->get();
        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');
        $banco_list         = Banco::banco_list();
        $id = $representant->id;
        Session::flash('operp_ok','Registros guardado exitosamente');
        return redirect()->route('administracion.representants.index',compact('search'));
    }

    public function store(CreateRegistroPagoRequest $request)
    {
        $estudiants = Estudiant::findOrFail($request->estudiant_id);
        $cuentaxpagar = Cuentaxpagar::findOrFail($request->cuentaxpagar_id);
        $search = $estudiants->ci_estudiant;
        $ingreso_ammount = $request->ingreso_ammount;

        $registro = RegistroPago::create([
            'estudiant_id' => $request->estudiant_id,
            'cuentaxpagar_id' => $request->cuentaxpagar_id,
            'user_id' => Auth::user()->id
        ]);

        if ($request->method_pay_id <> '1') { // el 1 se refiere a credistos a favor (no se registrar ingreso)
            $ingreso = Ingreso::create([
                'registro_pago_id' => $registro->id,
                'estudiant_id' => $estudiants->id,
                'method_pay_id' => $request->method_pay_id,
                'banco_id' => $request->banco_id,
                'number_i_pay' =>$request->number_i_pay,
                'date_transaction' =>$request->date_transaction,
                'date_payment' =>$request->date_payment,
                'ingreso_ammount' =>$request->ingreso_ammount,
                'ingreso_observations' => $request->ingreso_observations,
                'person_bill_ci' => $estudiants->representant->ci_representant,
                'person_bill_name' =>$estudiants->representant->name,
            ]);
            $num_oper = $ingreso->number_i_pay;
            $date_oper = $ingreso->date_transaction;
        }

        $credito_total = 0;
        $creditos_ids = null;
        if (is_array($request->credito_a_favor)) {
            $arr_dat = $request->credito_a_favor;
            foreach ($arr_dat as $k => $v) {
                if ($v == 'true') {
                    if ($estudiants->CreditosAFavorDisponiblesTest($k)) {
                        // dd($estudiants);
                        $credito = CreditoAplicado::create([
                            'registro_pago_id' => $registro->id,
                            'credito_a_favor_id' => $k,
                            'credito_aplicado_observations' => $request->credito_aplicado_observations
                        ]);
                        $credito_ammount = CreditoAFavor::findOrFail($k)->credito_ammount;
                        $credito_total = $credito_total + $credito_ammount;
                        $creditos_ids .= $k.';';
                    }
                }
            }
        }
        $total_ingreso_credito =  $ingreso_ammount + $credito_total;

        $descuento_total = 0;
        if (is_array($request->descuento)) {
            $arr_dat = $request->descuento;
            foreach ($arr_dat as $k => $v) {
                if ($v == 'true') {
                    if (!$estudiants->descuento_test) {
                        $descuento = DescuentoAplicado::create([
                            'registro_pago_id' => $registro->id,
                            'descuento_id' => $k,
                            'descuento_aplicado_observations' => $request->credito_aplicado_observations
                        ]);
                        $descuento_ammount = Descuento::findOrFail($k)->descuento_ammount;
                        $descuento_total = $descuento_total + $descuento_ammount;
                    }
                }
            }
        }

        $total_concepto_descuento = 0;
        if (is_array($request->concepto_pago)) {
            $arr_dat = $request->concepto_pago;
            foreach ($arr_dat as $k => $v) {
                if ($v == 'true') {
                    $concepto_create = ConceptoCancelado::create([
                        'registro_pago_id' => $registro->id,
                        'concepto_pago_id' => $k,
                        'concepto_pago_observations' => $request->concepto_pago_observations
                    ]);
                    $concepto = ConceptoPago::findOrFail($k);
                    $concepto_ammount = $concepto->concepto_ammount;
                    if ($concepto->status_discount =='true') {
                        $total_concepto_descuento = $total_concepto_descuento + ($concepto_ammount * (1 - $descuento_total / 100));
                    } else {
                        $total_concepto_descuento = $total_concepto_descuento + $concepto_ammount;
                    }
                }
            }
        }

        if ( $total_ingreso_credito > $total_concepto_descuento ) {

            $pagos_ammount = $total_concepto_descuento;

            $remanente = $total_ingreso_credito - $total_concepto_descuento;

            $ingreso_id = (isset($ingreso->id)) ? $ingreso->id : null ;
            $ingreso_date = (isset($ingreso->date_transaction)) ? $ingreso->date_transaction : null ;
            $ingreso_oper = (isset($ingreso->number_i_pay)) ? $ingreso->number_i_pay : null ;

            $credito_a_Favor = CreditoAFavor::create([
                'representant_id' => $estudiants->representant_id,
                'estudiant_id' => $estudiants->id,
                'registro_pago_id' => $registro->id,
                'ingreso_id' => $ingreso_id,
                'credito_a_favor_ids' => $creditos_ids,
                'credito_description' => 'CAF: REGID'.$registro->id.';F'.$registro->created_at,
                'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado del registro de pago ID: '.$registro->id.' | Transacción: ID: '.$ingreso_id.' - Fecha:'.$ingreso_date.' - Número:'.$ingreso_oper.' | Créditos IDS: '.$creditos_ids,
                'credito_ammount' => $remanente,
            ]);
        }
        else {
            $pagos_ammount = $total_ingreso_credito;
        }

        if ($request->method_pay_id <> '1') { // el 4 se refiere a credistos a favor (no se registrar ingreso)
            $pago = Pago::create([
                'registro_pago_id' => $registro->id,
                'ingreso_id' => $ingreso->id,
                'pagos_ammount' => $pagos_ammount
            ]);
        }

        Session::flash('operp_ok','Registros guardado exitosamente');

        return redirect()->route('administracion.registropagos.create',['id'=>$estudiants->id,'ctaid'=>$cuentaxpagar->id ]);
        // return redirect()->route('administracion.registropagos.index',compact('search','estudiants','cuentaxpagar'));

    }

}

<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Planpago\CreateRegistroPagoAsistentRequest;
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
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Administracion\Email\RegistroPago\sendTicketPaymentController;

class AsistentController extends Controller
{

    public function asistentIndividual(Request $request)
    {
        return view('administracion.registropagos.asistentIndividual');
    }

    public function estructura_create(Request $request)
    {
        return view('administracion.registropagos.estructura_create');
    }

    public function recargo_morosidad(Request $request)
    {
        $representant = Representant::where('ci_representant',$request->ci_representant)->first();

        return view('administracion.registropagos.recargo_morosidad',compact('representant'));
    }

    public function livewire(Request $request)
    {
        $representant = Representant::where('ci_representant',$request->ci_representant)->first(); //dd($representant);
        return view('administracion.registropagos.livewire',compact('representant'));
    }


    public function store_representant_exchange(CreateRegistroPagoAsistentRequest $request)
    {
        DB::beginTransaction();
        try {
        $representant = Representant::findOrFail($request->representant_id);
        $date_current = Carbon::now()->format('Y-m-d');
        $method_pay_id = $request->method_pay_id;

        $ingreso_ammount = $request->ingreso_ammount;
        $date_payment = $request->date_payment;
        $exchange_rate_current = ExchangeRate::whereDate('date',$date_payment)->first();
        $ingreso_exchange_rate_id = ($exchange_rate_current) ? $exchange_rate_current->id : null;
        $ingreso_exchange_ammount = ($exchange_rate_current) ? $request->ingreso_ammount / $exchange_rate_current->ammount : null;
        $exchange_ammount = $ingreso_exchange_ammount;

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

        $registro_arr = array();

        $cuentaxpagar_arr = $request->cuentaxpagar_id; //dd($cuentaxpagar_arr,$request->all());
        $cuentaxpagar_ammount_arr = $request->cuentaxpagar_ammount;

        $credito_arr = (!empty($request->credito)) ? $request->credito : array();
        $credito_ammount_arr = (!empty($request->credito_ammount)) ? $request->credito_ammount : array();
        $credito_exchange_ammount_arr = (!empty($request->credito_exchange_ammount)) ? $request->credito_exchange_ammount : array();
        $cuentaxpagar_unexpired_arr = (!empty($request->cuentaxpagar_unexpired)) ? $request->cuentaxpagar_unexpired : array();

        $registro = null;
        $registro_id = null;
        $ingreso_id = null;

        $combinado = RegistroPagoCombinado::create([
            'representant_id' => $representant->id,
            'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: '.Carbon::now()->format('Y-m-d'),
        ]);
        $pago_combinado_id = $combinado->id;

        //INI Ingreso
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
                'status_late_payment' => $request->status_late_payment,
                'exchange_ammount_late_payment' => $request->exchange_ammount_late_payment,
                'ingreso_observations' => $request->ingreso_observations,
                'person_bill_ci' => $representant->ci_representant,
                'person_bill_name' =>$representant->name,
            ]);
            $recurso_ingreso = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso->id,'status_ingreso' => 'true']);
            $total_recursos = $ingreso_ammount;
        //FIN Ingreso

        //INI Abono
            $abonos_disponibles = $representant->abonos_disponibles;
            $total_abonos_ammount = $abonos_disponibles->sum('abono_ammount');
            $total_abonos_exchange_ammount = $abonos_disponibles->sum('exchange_ammount');
        //FIN Abono

        //INI Creditos
            $creditos_disponibles = $representant->creditos_disponibles;
            $total_creditos_ammount = $creditos_disponibles->sum('credito_ammount');
            $total_creditos_exchange_ammount = $creditos_disponibles->sum('exchange_ammount');
        //FIN Creditos

        $total_recursos_ammount = $ingreso_ammount + $total_abonos_ammount + $total_creditos_ammount;
        $total_recursos_exchange = $ingreso_exchange_ammount + $total_abonos_exchange_ammount + $total_creditos_exchange_ammount;

        //INI Estudiante/Cuentaxpagar
            foreach ($cuentaxpagar_arr as $estudiant_id => $cuentaxpagar_in) {

                $estudiant = Estudiant::findOrFail($estudiant_id);

                foreach ($cuentaxpagar_in as $id => $selected) {

                    if ($total_recursos_exchange>0) {

                        $total_pagado = 0;
                        $total_pagado_exchange = 0;

                        if ($selected=='true') {

                            $cuentaxpagar_id = $id;
                            $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);

                            // $total_exchange_monto_cuentasxpagar_adeudado = round($cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id),2);
                            // hardcode, en acuerdo con admon, se consideran montos superiores a USD 0.009

                            $total_exchange_monto_cuentasxpagar_adeudado = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant_id);
                            
                            if ($total_exchange_monto_cuentasxpagar_adeudado > 0) {

                                //INI RegistroPago
                                    $status_unexpired = (array_key_exists($cuentaxpagar_id, $cuentaxpagar_unexpired_arr)) ? true : false; //dd($cuentaxpagar_ammount);

                                    $registro = RegistroPago::create([
                                        'estudiant_id' => $estudiant->id,
                                        'representant_id' => $representant->id,
                                        'registro_pago_combinado_id' => $combinado->id,
                                        'cuentaxpagar_id' => $cuentaxpagar->id,
                                        'status_unexpired' => $status_unexpired,
                                        'user_id' => Auth::user()->id
                                    ]);
                                    $registro_arr[] = $registro;
                                    $registro_id = $registro_id.'ID: '.$registro->id.' | '; //dd($registro);
                                //FIN RegistroPago

                                //INI Concepto Cancelado se calcula cuantol se va a pagar
                                    $total_concepto_descuento = 0;
                                    $total_concepto_ammount = 0;
                                    $total_concepto_ammount_exchange = 0;
                                    $total_concepto_descuento_exchange = 0;
                                    $conceptopagos = $cuentaxpagar->conceptopagos; //dd($conceptopagos);
                                    foreach ($conceptopagos as $conceptopago) {

                                        if ($total_recursos_exchange > 0) {
                                            $concepto_ammount =  $conceptopago->concepto_ammount;
                                            $exchange_ammount =  $conceptopago->exchange_ammount;

                                            if ($conceptopago->status_discount =='true') {
                                                $descuento_ammount = $estudiant->descuento_ammount($cuentaxpagar->id);
                                                if ($descuento_ammount) {
                                                    $descuento_ammount = 1 - $descuento_ammount / 100;
                                                    $concepto_ammount =  $concepto_ammount * $descuento_ammount;
                                                    $exchange_ammount =  $exchange_ammount * $descuento_ammount;
                                                }
                                            }

                                            $count = $conceptopagos->count();
                                            $concepto_cancelado_ammount_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagarPagado($estudiant->id) / $count;

                                            $diferencia = $exchange_ammount - $concepto_cancelado_ammount_exchange;
                                            if ($diferencia > 0) {

                                                $total_concepto_ammount_exchange = $exchange_ammount - $concepto_cancelado_ammount_exchange;

                                                if ($total_concepto_ammount_exchange > $total_recursos_exchange) {
                                                    $total_concepto_ammount_exchange = $total_recursos_exchange;
                                                }

                                                $factor = $total_concepto_ammount_exchange / $total_recursos_exchange;
                                                $total_concepto_ammount = $factor * $total_recursos_ammount;

                                                $status_partial = ($concepto_cancelado_ammount_exchange > 0) ? 'true':'false';

                                                $concepto_cancelado_create = ConceptoCancelado::create([
                                                    'registro_pago_id' => $registro->id,
                                                    'concepto_pago_id' => $conceptopago->id,
                                                    'status_partial' => $status_partial,
                                                    'concepto_ammount' => $total_concepto_ammount,
                                                    'exchange_ammount' => $total_concepto_ammount_exchange,
                                                ]);
                                                $total_concepto_descuento = $total_concepto_descuento + $total_concepto_ammount;
                                                $total_concepto_descuento_exchange = $total_concepto_descuento_exchange + $total_concepto_ammount_exchange;

                                                $total_recursos_ammount = $total_recursos_ammount - $total_concepto_ammount;
                                                $total_recursos_exchange = $total_recursos_exchange - $total_concepto_ammount_exchange;
                                            }
                                        }
                                    }
                                    $total_pagado_ammount = $total_concepto_descuento;
                                    $total_pagado_ammount_exchange = $total_concepto_descuento_exchange;
                                //FIN Concepto Cancelado

                                //INI Pagado
                                    $pago = Pago::create([
                                        'registro_pago_id' => $registro->id,
                                        'ingreso_id' => $ingreso->id,
                                        'pagos_ammount' => $total_pagado_ammount,
                                        'exchange_ammount' => $total_pagado_ammount_exchange
                                    ]);
                                //FIN Pagado

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

                                /* INI AbnoAplicado */
                                    $abono_total = 0;
                                    $abono_total_exchange = 0;
                                    $abonos_ids = null;
                                    $abonos_disponibles = $representant->abonos_disponibles;
                                    foreach ($abonos_disponibles as $abono) {
                                        $abono_aplicado_create = AbonoAplicado::create([
                                            'registro_pago_id' => $registro->id,
                                            'abono_id' => $abono->id,
                                            'abono_aplicado_observations' => $request->abono_aplicado_observations
                                        ]);

                                        $abono = Abono::findOrFail($abono->id);
                                        $ingreso_aplicado = Ingreso::findOrFail($abono->ingreso_id);
                                        $recurso_credito = Recurso::create(['registro_pago_combinado_id' => $combinado->id,'ingreso_id' => $ingreso_aplicado->id,'status_abono' => 'true']);

                                        $abono_total = $abono_total + $ingreso_aplicado->ingreso_ammount;
                                        $abono_total_exchange = $abono_total_exchange + $ingreso_aplicado->exchange_ammount;
                                        $abonos_ids .= $abono->id.';';
                                        $abono->delete();
                                    }

                                    $total_ingreso_credito_abono =  $total_ingreso_credito + $abono_total;
                                    $total_ingreso_credito_abono_exchange =  $total_ingreso_credito_exchange + $abono_total_exchange; //dd($total_ingreso_credito_abono,$total_ingreso_credito_abono_exchange);
                                /* FIN AbnoAplicado */

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
                }
            }
        //FIN Estudiante/Cuentaxpagar

        $messenge_refund = null;

        //INI CAF generado
            if ($total_recursos_exchange > 0 && $registro) {
                $status_omitted = ($total_recursos_exchange <= 0.09) ? 'true' : 'false' ;
                $credito_a_Favor_new = CreditoAFavor::create([
                    'representant_id' => $representant->id,
                    'estudiant_id' => $estudiant->id,
                    'registro_pago_id' => $registro->id,
                    'ingreso_id' => $ingreso_id,
                    'credito_description' => 'CAF: REGID'.$registro->id.';F'.Carbon::now(),
                    'credito_observations' => 'Crédito generado en la fecha: '.Carbon::now().' | Derivado de los registro de pago ID: '.$registro_id.' | Transacción: ID: '.$ingreso_id.' - Número: '.$request->number_i_pay,
                    'credito_ammount' => $total_recursos_ammount,
                    'exchange_ammount' => $total_recursos_exchange,
                    'status_omitted' => $status_omitted
                ]);

                $messenge_refund = '
                <div>
                    Quiere registrar un Vueltos/Devoluciones?
                    <a title="Registro de Vueltos/Devoluciones" class="btn btn-warning btn-sm" href="'.route('administracion.refunds.index',['registro_pago_combinado_id'=>$combinado->id]).'" role="button">
                        <i class="fas fa-money-bill-alt fa-1x"></i>
                    </a>
                </div>
                ';

                Session::flash('messenge_refund',$messenge_refund);
            }
        //FIN CAF generado

        $exchange_ajuste = null;

        $search = $representant->ci_representant;
        $representant_id = $representant->id;
        $id = $representant->id;
        $help_representante = $representant->ci_representant;

        if (count($registro_arr) > 0) {
            $messenge = 'Registros guardado exitosamente';
            $messenge = ($exchange_ajuste) ? $messenge.' - Monto ajustado: '.f_float($exchange_ajuste) : $messenge ;
            $result = 'operp_ok';
            // Session::flash('operp_ok',$messenge);
        } else {
            $messenge = 'Ocurrieron errores. No se realizó ningún registro de pago!';
            $result = 'operpNoOk';
            $operation= 'error';
            // Session::flash('operpNoOk',$messenge);
        }

        $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
        // ajustes automaticos
        $ammount_round = round($exchange_ammount_expire_bill,2);
        if ($ammount_round > 0.01 && $ammount_round <= 0.2) {
            $now = Carbon::now()->format('Y-m-d');
            $exchange_rate = ExchangeRate::where('date',$now)->first();
            $ingreso_ammount = round($exchange_ammount_expire_bill * $exchange_rate->ammount,8);

            $estudiant_id = $representant->estudiants()->first()->id;
            $representant_id = $representant->id;
            $method_pay_id = 3;
            $banco_id = 7;
            $number_i_pay =  Carbon::now()->timestamp;
            $date_transaction = $now;
            $date_payment =  $now;
            $ingreso_ammount = $ingreso_ammount;
            $exchange_rate_id = $exchange_rate->id;
            $exchange_ammount = $exchange_ammount_expire_bill;
            $ingreso_observations = 'Ajuste automático - SAEFL';
            $person_bill_ci = $representant->ci_representant;
            $person_bill_name =$representant->name;

            $compact = [
                'id','search','help_representante','representant_id','estudiant_id','method_pay_id','banco_id',
                'number_i_pay','date_transaction','date_payment','ingreso_ammount','exchange_rate_id','exchange_ammount','ingreso_observations',
                'person_bill_ci','person_bill_name'
            ];

            $messenge = 'Por favor realice el ajuste, los datos están precargados.';

            Session::flash($result,'Por favor realice el ajuste, los datos están precargados.');

            return redirect()->route('administracion.registropagos.asistent.representant.create',compact($compact));
        }

        DB::commit();

        Session::flash($result,$messenge);
        Session::flash('pago_combinado_id',$pago_combinado_id);

        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('operpNoOk', 'Error en el registro: ' . $e->getMessage());
            return redirect()->back();
    }

        return redirect()->route('administracion.registropagos.asistent.representant.create',compact('representant','id','search','help_representante','representant_id'));
    }

    public function asistent(Request $request)
    {
        $search = (!empty($request->search)) ? $request->search : null ;
        $representants = collect();

        if ($search) {
            $search = $request->get('search');
            $arr_get = [
                'search'=>$search,
            ];

            $representants = Representant::name($arr_get)->OrderBy('created_at', 'desc')->get();
        }

        return view('administracion.registropagos.asistent',compact('representants','search'));
    }

    public function asistent_representant_create(Request $request)
    {
        $id = $request->id;

        $fecha = Carbon::now()->format('Y-m-d');

        $representant       = Representant::findOrFail($id); //dd($representant);

        $estudiants     = Estudiant::where('representant_id',$representant->id)->active('true')->get();

        $method_pay_list    = MetodoPago::list_metodo_pago();

        $banco_list         = Banco::banco_list(); //dd($banco_list);

        $representant_id = $representant->id;
        $help_representante = $representant->ci_representant;
        $list_representant = Representant::list_representant();
        $list_divisas = ['1'=>'1','2'=>'2','5'=>'5','10'=>'10','20'=>'20','50'=>'50','100'=>'100'];

        for ($i=1; $i < 200; $i++) {
            if (!in_array($i,$list_divisas)) {
                for ($j=0; $j <9 ; $j++) {
                    $list_divisas[] = strval($i).'.'.strval($j);
                }
            }
        }

        return view('administracion.registropagos.asistent_representant_create',
        compact('representant','estudiants','method_pay_list','banco_list','representant_id','help_representante','list_representant','list_divisas','fecha','request'));
    }

}

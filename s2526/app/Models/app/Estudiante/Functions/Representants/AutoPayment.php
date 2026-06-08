<?php
namespace App\Models\app\Estudiante\Functions\Representants;

use App\User;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPagoCombinado;

trait AutoPayment
{
    public function payAutomatic(Array $data, Representant $representant)
    {

        $results = Array();
        $result_arr = Array();
        $registro_pago_combinado_id = 1000;
        $registro_pago_id = 1000;
        $ingreso_id = 1000;
        $total_ammount = null;
        $total_exchange_ammount = null;

        $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2);

        $exchange_expire_bill = ($exchange_ammount_expire_bill > 0) ? $representant->exchange_expire_bill_pendientes_auto_payment : $representant->exchange_unexpired_bill_pendientes_auto_payment; //dd($this,$exchange_expire_bill);

        if ($exchange_expire_bill->isNotEmpty()) {

            $exchange_expire_bill = json_decode($exchange_expire_bill->sortBy('date_expiration')) ; // dd($exchange_expire_bill);

            // $ingreso = New Ingreso;
            // $ingreso->fill($data);
            // $ingreso->id = $ingreso_id;

            $ingreso = Ingreso::create($data);
            $result_arr['ingreso'] = $ingreso;

            $arr = [
                'representant_id'=> $representant->id,
                'description'=>'Servicio Botón de Pago',
            ];
            $registro_pago_combinado = RegistroPagoCombinado::create($arr); //dd($registro_pago_combinado);

            // $registro_pago_combinado = New RegistroPagoCombinado; //dd($registro_pago_combinado);
            // $registro_pago_combinado->fill($arr);
            // $registro_pago_combinado->id = $registro_pago_combinado_id;

            $result_arr['registro_pago_combinado'] = $registro_pago_combinado;

            foreach ($exchange_expire_bill as $expire_bill) { // dd($cuentaxpagar);

                $exchange_ammount = round($ingreso->exchange_ammount,2);

                if ($exchange_ammount > 0) {
                    $cuentaxpagar = Cuentaxpagar::find($expire_bill->id);
                    $estudiant = Estudiant::find($expire_bill->estudiant_id); // dd($estudiant);

                    $user = User::user_admin(); //

                    $arr = [
                        'user_id'=>$user->id,
                        'estudiant_id'=>$estudiant->id,
                        'representant_id'=>$representant->id,
                        'cuentaxpagar_id'=>$cuentaxpagar->id,
                        'registro_pago_combinado_id'=>$registro_pago_combinado->id,
                        'status_prepayment'=>false,
                        'status_reconciled'=>null,
                        'status_unexpired'=>null,
                    ];
                    $registro_pago = RegistroPago::create($arr); //dd($registro_pago);

                    // $registro_pago = New RegistroPago;
                    // $registro_pago->fill($arr);
                    // $registro_pago->id = $registro_pago_id; // modo test

                    $datas = $cuentaxpagar->RegistroPagoAutoPayment($registro_pago,$ingreso,$estudiant); //dd($datas);

                    $pago = $datas['pago'];

                    $total_ammount = $total_ammount + $pago->pagos_ammount;
                    $total_exchange_ammount = $total_exchange_ammount + $pago->exchange_ammount;

                    $results[] = $datas;

                    $ingreso->exchange_ammount = $datas['total_recursos_exchange'];

                    $exchange_rate_ammount = ExchangeRate::getAmmountExchange();
                    $ingreso->ingreso_ammount = $ingreso->exchange_ammount * $exchange_rate_ammount;
                }
            }

            $result_arr['cuentaxpagar'] = $results;

            $result_arr['total_ammount'] = $total_ammount;
            $result_arr['total_exchange_ammount'] = $total_exchange_ammount;

            //dd($result_arr);

            // $exchange_ammount = round($ingreso->exchange_ammount,2);

            if ($exchange_ammount > 0) {

                $data = [
                    'representant_id'=>$representant->id,
                    'method_pay_id'=>1, //Metodo de pago ABN/CAF
                    'banco_id'=>1, // Banco ABN/CAF
                    'number_i_pay'=> $ingreso->number_i_pay.'-ABN',
                    'date_transaction'=>$ingreso->date_transaction,
                    'date_payment'=>$ingreso->date_payment,
                    'date_reported'=>$ingreso->date_reported,
                    'ingreso_ammount'=>$ingreso->ingreso_ammount,
                    'exchange_ammount'=>$ingreso->exchange_ammount,
                    'ingreso_observations'=>'Abono',
                    'person_bill_ci'=>$representant->ci_representant,
                    'person_bill_name'=>$representant->name,
                ];
                $ingreso_abono_caf = Ingreso::create($data);

                $data = [
                    'representant_id'=>$representant->id,
                    'estudiant_id'=>$estudiant->id,
                    'ingreso_id'=>$ingreso_abono_caf->id,
                    'abono_description'=>'Abono automático',
                ];
                $abono = Abono::create($data);

                $result_arr['abono'] = $abono;
            }

        }

        return $result_arr;

    }

}

?>

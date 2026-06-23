<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\app\Services\Transaction;

use App\Models\app\Estudiante\Representant;

use App\Models\app\Planpago\MetodoPago;

use App\Models\app\Institucion\Banco;

trait RegistroPagoTrait
{
	private function registroPago($response)
    {
        $representant = Representant::where('ci_representant', $this->ci_representant)->first(); //dd($representant);

        // $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill; //dd($exchange_ammount_expire_bill);

        $info_created_at = substr($response->info->created_at,0,19); //dd($info_created_at);

        $created_at = Carbon::parse($info_created_at);
        $date = $created_at->format('Y-m-d');

        $ingreso_exchange_ammount = ($this->exchange_rate_ammount) ? $response->amount / $this->exchange_rate_ammount : 0;

        $metodo_pago = MetodoPago::where('code','BTNCC')->first(); // Metodo de pago para este servicio BTNCC
        $banco = Banco::where('name','BANCARIBE')->first(); //Proveedor del servicio BANCARIBE

        //dd($response,$response->payment_method);

        $data = [
            'representant_id'=>$representant->id,
            'method_pay_id'=>$metodo_pago->id, //Metodo de pago (Boton de pago)
            'banco_id'=>$banco->id, // Banco credicard
            'number_i_pay'=> $response->credicard->approval,
            'date_transaction'=>$date,
            'date_payment'=>$date,
            'date_reported'=>$date,
            'ingreso_ammount'=>$response->amount,
            'exchange_ammount_rate'=>$this->exchange_rate_ammount,
            'exchange_ammount'=>$ingreso_exchange_ammount,
            'ingreso_observations'=>'Procesado por Credicard, TDC/TDD: '.$response->payment_method->payment_card->masked_account_number,
            'person_bill_ci'=>$response->payment_method->payment_card->holder_id,
            'person_bill_name'=>$response->payment_method->payment_card->holder_name,
            'terminal_pos'=>$response->credicard->terminal,
            'approval_pos'=> $response->credicard->approval,
            'sequence_pos'=>$response->credicard->sequence,
            'trace_pos'=>$response->credicard->trace,
        ];

        $result = $representant->payAutomatic($data, $representant); //dd($result);

        if (array_key_exists('registro_pago_combinado', $result)) {
            $registro_pago_combinado = $result['registro_pago_combinado'];
            $this->bill_number = $registro_pago_combinado->id;
            $this->bill_created_at = $registro_pago_combinado->created_at;
            $this->bill_ammount = (array_key_exists('total_ammount', $result)) ? $result['total_ammount'] : null;
            $this->bill_ammount_exchange = (array_key_exists('total_exchange_ammount', $result)) ? $result['total_exchange_ammount'] : null;

            $this->status_payment_success = true; //dd($this->status_payment_success);

            $representant = Representant::where('ci_representant', $this->ci_representant)->first();
            $this->exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2);
            $this->ammount_expire_bill = round($this->exchange_ammount_expire_bill * $this->exchange_rate_ammount,2);

            if (array_key_exists("cuentaxpagar",$result)) {
                $cuentaxpagar_name = null;
                $cuentaxpagars = $result['cuentaxpagar'];
                foreach ($cuentaxpagars as $cuentaxpagar) {
                    if (array_key_exists("concepto_cancelados",$cuentaxpagar)) {
                        $concepto_cancelados = $cuentaxpagar['concepto_cancelados']; //dd($concepto_cancelados);
                        foreach ($concepto_cancelados as $concepto_cancelado) {
                            if (array_key_exists("cuentaxpagar_name",$concepto_cancelado)) {
                                $cuentaxpagar_name .= $concepto_cancelado['cuentaxpagar_name']. '['.$concepto_cancelado['estudiant_name'].']' .' || '; //dd($cuentaxpagar_name);
                            }
                        }
                    }
                }
                $this->cuentaxpagar_name = $cuentaxpagar_name ;
            }

            if (array_key_exists("abono",$result)) {
                $abono = $result['abono'];
                $this->abono_exchange_ammount = $abono->ingreso->exchange_ammount;
                $this->abono_ingreso_ammount = $abono->ingreso->ingreso_ammount;
            }

            session()->flash('messengeRegistroPago', '<b>SAEFL</b> registró su pago exitosamente.');
        }
        else {
            session()->flash('messengeError', 'No se registro ningún pago. Contacte a la Direcciòn de administración.');
        }

        return $result;

    }

    private function createTransaction($response)
    {
        $representant = Representant::where('ci_representant', $this->ci_representant)->first();
        $transaction = Transaction::create([
                'representant_id'=>$representant->id,
                'data'=>json_encode($response),
            ]
        );
        return $transaction;
    }

}


?>

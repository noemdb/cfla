<?php

namespace App\Http\Requests\Administracion\Planpago;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Models\app\Planpago\ExchangeRate;

class CreateRegistroPagoRepresentantRequest extends FormRequest
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request->all();
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = Request::All(); //dd($request);
        $ingreso_ammount = (is_float($request['ingreso_ammount'])) ? $request['ingreso_ammount'] : 0;
        $total_abono_credito_exchange = 0;
        $total_abono_exchange = 0;
        $total_credito_exchange = 0;
        $ingreso_ammount_min = 0;

        $exchange_rate = ExchangeRate::whereDate('date',$request['date_payment'])->first();
        $exchange_rate_ammount = ($exchange_rate) ? $exchange_rate->ammount : null ;
        $ingreso_ammount_exchange = ($exchange_rate_ammount) ? $ingreso_ammount / $exchange_rate_ammount : null ; //dd($ingreso_ammount_min);

        /* INI Total a pagar seleccionado */
            $cuentaxpagar_ammount = $request['cuentaxpagar_ammount']; //dd($cuentaxpagar_ammount);
            $cuentaxpagar_id = $request['cuentaxpagar_id']; //dd($cuentaxpagar_ammount);
            $total_cuentaxpagar_exchange = 0;
            foreach ($cuentaxpagar_ammount as $estudiant_id => $cuentaxpagar_arr ) {
                foreach ($cuentaxpagar_arr as $id => $ammount) {
                    $select = $cuentaxpagar_id[$estudiant_id][$id]; //dd($select);
                    if ($select=="true") {
                        $total_cuentaxpagar_exchange = $total_cuentaxpagar_exchange + $ammount;
                    }
                }
            }
            // dd(
            //     'request',$request,
            //     'cuentaxpagar_ammount',$cuentaxpagar_ammount,
            //     'cuentaxpagar_id',$cuentaxpagar_id,
            //     'total_cuentaxpagar_exchange',$total_cuentaxpagar_exchange,
            // );
        /* FIN Total a pagar seleccionado */

        /* INI Monto total de CAF seleccionados */
            $credito = (array_key_exists('credito', $request)) ? $request['credito'] : null; //dd($credito);
            if (is_array($credito)) {
                $credito_ammount = (array_key_exists('credito_ammount', $request)) ? $request['credito_ammount'] : null; //dd($cuentaxpagar_ammount);
                if (is_array($credito_ammount)) {
                    $total_credito_exchange = 0;
                    foreach ($credito_ammount as $credito_id => $ammount) {
                        $select = $credito[$credito_id]; //dd($select);
                        if ($select=="true") {
                            $total_credito_exchange = $total_credito_exchange + $ammount;
                        }
                    } //dd($total_credito_exchange);
                }
            } //dd();
            $total_credito_exchange = $total_credito_exchange + 0; //dd($total_credito_exchange);
        /* INI Monto total de CAF seleccionados */

        /* INI Monto total de Abonos seleccionados */
            $abono = (array_key_exists('abono', $request)) ? $request['abono'] : null; //dd($cuentaxpagar_ammount);
            if (is_array($abono)) {
                $abono_ammount = (array_key_exists('abono_ammount', $request)) ? $request['abono_ammount'] : null; //dd($cuentaxpagar_ammount);
                if (is_array($abono_ammount)) {
                    //$abono_ammount = $request['abono_ammount']; //dd($cuentaxpagar_ammount);
                    $total_abono_exchange = 0;
                    foreach ($abono_ammount as $abono_id => $ammount) {
                        $select = $abono[$abono_id]; //dd($select);
                        if ($select=="true") {
                            $total_abono_exchange = $total_abono_exchange + $ammount;
                        }
                    } //dd($total_abono_exchange);
                }
            }
            $total_abono_credito_exchange = $total_abono_exchange + $total_credito_exchange;
        /* INI Monto total de Abonos seleccionados */

        $total_cuentaxpagar_recursos_exchange = $total_cuentaxpagar_exchange - $total_abono_credito_exchange;
        $total_cuentaxpagar_recursos = $total_cuentaxpagar_recursos_exchange * $exchange_rate_ammount;

        $ingreso_ammount_min = $total_cuentaxpagar_recursos;
        // dd(
        //     'exchange_rate_ammount',$exchange_rate_ammount,
        //     'total_abono_credito_exchange',$total_abono_credito_exchange,
        //     'total_cuentaxpagar_recursos_exchange',$total_cuentaxpagar_recursos_exchange,
        //     'total_cuentaxpagar_recursos',$total_cuentaxpagar_recursos,
        //     'ingreso_ammount_min',$ingreso_ammount_min
        // );

        return [
            'ingreso_ammount' => 'required|numeric|regex: /^[0-9]+(\\.[0-9]+)?$/|min:'.$ingreso_ammount_min,
            'representant_id' => 'required|integer',
            'estudiant_id' => 'required|integer',
            'method_pay_id' => 'required|integer',
            'banco_id' => 'required|integer',
            'number_i_pay' => 'required|max:30|unique:ingresos',
            'date_payment' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d').'|after_or_equal:date_payment',
            'crt_checkboxes' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            // 'ingreso_ammount.min' => 'No es sufiente el monto a total (transacción + crédito a favor) a registrar, éste debe ser mayor o igual: :min',
            // 'ingreso_ammount.min' => 'No es sufiente el monto a registrar, este debe ser mayor o igual: '.f_float($ingreso_ammount_min,6),
            'method_pay_id.unique' => 'La referencia ya exíste.',
            'sum_ammount.gt' => 'Nohay cuentas para pagar.',
            'crt_checkboxes.array' => 'No hay cuentas por pagar seleccionadas.',
            'crt_checkboxes.required' => 'No hay cuentas por pagar seleccionadas.',
            'ingreso_ammount.numeric' => 'El monto de la operación debe ser numérico.',
            'ingreso_ammount.regex' => 'El monto de la operación no tiene el formato correcto.',
        ];
    }
    public function attributes()
    {
        return [
            'number_i_pay' => 'Referencia Bancaria / Número de la transacción',
            'date_payment' => 'Fecha de Pago',
            'date_transaction' => 'Fecha en Banco',
            'ingreso_ammount' => 'Monto de la operación',
        ];
    }
    public function withValidator($validator)
    {
        // dd($validator);
        // dd(Request::All());
        // $validator->after(function ($validator) {
        //     if ($this->somethingElseIsInvalid()) {
        //         $validator->errors()->add('field', 'Something is wrong with this field!');
        //     }
        // });
    }
}

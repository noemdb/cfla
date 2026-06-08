<?php

namespace App\Http\Requests\Administracion\Planpago;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CreateRegistroPagoRequest extends FormRequest
{
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
        $request = Request::All();

        $sum_concepto = 0;
        if (array_key_exists('concepto_pago',$request)) {
            if (is_array($request['concepto_pago'])) {
                $arr = $request['concepto_pago'];
                $ammount = $request['concepto_ammount'];
                foreach ($arr as $k => $v) {
                    if ($v == 'true') {
                        $sum_concepto = $sum_concepto  + $ammount[$k];
                    }
                }
            }
        }

        // dd($request,$sum_concepto);
        $sum_credito = 0;
        if (array_key_exists('credito_a_favor', $request)) {
            if (is_array($request['credito_a_favor'])) {
                $arr = $request['credito_a_favor'];
                $ammount = $request['credito_ammount'];
                foreach ($arr as $k => $v) {
                    if ($v == 'true') {
                        $sum_credito = $sum_credito  + $ammount[$k];
                    }
                }
            }
        }       
        // dd($request,$sum_concepto,$sum_credito);

        $sum_ammount = $sum_concepto - $sum_credito;

        // dd($sum_ammount,$request['ingreso_ammount']);

        return [
            'estudiant_id' => 'required|integer',
            'cuentaxpagar_id' => 'required|integer',
            'method_pay_id' => 'required|integer',
            'banco_id' => 'required|integer',
            'number_i_pay' => 'required|max:30',
            'ingreso_ammount' => 'required|numeric|min:'.$sum_ammount,
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            // 'person_bill_ci' => 'required|integer',
            // 'person_bill_name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ingreso_ammount.min' => 'No es sufiente el monto a registrar (transacción + crédito a favor) aún faltán: :min',
            'date_transaction' => 'Fecha de la transacción',
            // 'body.required'  => 'A message is required',
        ];
    }
}

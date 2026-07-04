<?php

namespace App\Http\Requests\Administracion\Planpago;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateRegistroPagoAsistentRequest extends FormRequest
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
    public function rules()
    {
        $request = Request::All(); //dd($request);
        $ingreso_ammount = (is_float($request['ingreso_ammount'])) ? $request['ingreso_ammount'] : null;
        $representant_id = $request['representant_id'];
        $representant = Representant::findOrFail($representant_id);

        $total_credito_exchange = $representant->TotalCreditoExchange;
        $total_abono_exchange = $representant->TotalAbonoExchange;
        $total_recursos_exchange = $total_abono_exchange + $total_credito_exchange + $ingreso_ammount;

        //dd($ingreso_ammount,$representant,$total_credito_exchange,$total_abono_exchange,$total_recursos_exchange);

        $rules_ingreso_ammount = 'nullable|numeric|regex: /^[0-9]+(\\.[0-9]+)?$/';
        $rules_ingreso_ammount = ($total_recursos_exchange == 0) ? $rules_ingreso_ammount.'|gt:0' : $rules_ingreso_ammount ;

        //dd($rules);

        $rules = [
            'ingreso_ammount' => $rules_ingreso_ammount,
            'representant_id' => 'required|integer',
            'estudiant_id' => 'required|integer',
            'method_pay_id' => 'required|integer',
            'banco_id' => 'required|integer',
            'number_i_pay' => 'required|max:30|unique:ingresos',
            'date_payment' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d').'|after_or_equal:date_payment',
            'cuentaxpagar_id' => 'required|array',
            // 'crt_checkboxes' => 'required|array',
        ];

        //dd($rules);

        return $rules;
    }

    public function messages()
    {
        return [
            'method_pay_id.unique' => 'La referencia ya exíste',
            'sum_ammount.gt' => 'No hay cuentas para pagar',
            'crt_checkboxes.array' => 'No hay cuentas por pagar seleccionadas',
            'crt_checkboxes.required' => 'No hay cuentas por pagar seleccionadas',
            'ingreso_ammount.gt' => 'El monto de la orperación debe ser mayor a cero',
            'ingreso_ammount.numeric' => 'El monto de la orperación no tiene el formato correcto',
        ];
    }
    public function attributes()
    {
        return [
            'number_i_pay' => 'Referencia Bancaria / Número de la transacción',
            'date_payment' => 'Fecha de Pago',
            'date_transaction' => 'Fecha en Banco',
            'ingreso_ammount' => 'Monto de la operación',
            'method_pay_id' => 'Método de Pago',
            'banco_id' => 'Banco receptor',
            'estudiant_id' => 'Estudiante',
            'representant_id' => 'Representante',
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

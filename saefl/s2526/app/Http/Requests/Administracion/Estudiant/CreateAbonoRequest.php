<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CreateAbonoRequest extends FormRequest
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
        return [
            'estudiant_id' => 'integer',
            'representant_id' => 'required|integer',
            'ingreso_ammount' => 'required|numeric',
            'number_i_pay' => 'required|max:30|unique:ingresos',
            'banco_id' => 'required|integer',
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            'date_payment' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            // 'ingreso_id' => 'required|integer',
        ];
    }
    public function messages()
    {
        return [
            'ingreso_ammount.numeric' => 'El monto de debe ser un número',
            'method_pay_id.unique' => 'La referencia ya exíste',
            'date_transaction.date' => 'El campo de fecha en banco debe contener una fecha válida',
            'date_transaction.required' => 'El campo de fecha en banco es requerido',
            'date_payment.date' => 'El campo de fecha de pago debe contener una fecha válida',
            'date_payment.required' => 'El campo de fecha de pago es requerido',
            // 'body.required'  => 'A message is required',
        ];
    }
    public function attributes()
    {
        return [
            'number_i_pay' => 'Referencia/Número',
            'date_payment' => 'Fecha de Pago',
            'date_transaction' => 'Fecha en Banco',
            'ingreso_ammount' => 'Monto de la operación',
        ];
    }
}

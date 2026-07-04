<?php

namespace App\Http\Requests\Administracion\Planpago;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CreateRegistroParcialRequest extends FormRequest
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
            'ingreso_ammount' => 'required|numeric',
            'representant_id' => 'required|integer',
            'estudiant_id' => 'required|integer',
            'cuentaxpagar_id' => 'required|integer',
            'method_pay_id' => 'required|integer',
            'banco_id' => 'required|integer',
            'number_i_pay' => 'required|max:30|unique:ingresos',
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
        ];
    }
    public function messages()
    {
        return [
            // 'ingreso_ammount.min' => 'No es sufiente el monto a total (transacción + crédito a favor) a registrar, éste debe ser mayor o igual: :min',
            'ingreso_ammount.numeric' => 'El monto debe ser numérico',
            'method_pay_id.unique' => 'La referencia ya exíste',
            // 'date_transaction.date_format' => 'Revisar fecha de operación',

        ];
    }
    public function attributes()
    {
        return [
            'number_i_pay' => 'Referencia Bancaria / Número de la transacción',
            'date_transaction' => 'Fecha de la operación',
            'representant_id' => 'Representante',
            'estudiant_id' => 'Estudiante',
            'cuentaxpagar_id' => 'Concepto',
        ];
    }
}

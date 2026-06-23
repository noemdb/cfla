<?php

namespace App\Http\Requests\Administracion\Planpago;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreatePrepagoRequest extends FormRequest
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
            'ingreso_ammount' => 'required|numeric|',
            'representant_id' => 'required|integer',
            'estudiant_id' => 'required|integer',
            'method_pay_id' => 'required|integer',
            'banco_id' => 'required|integer',
            'number_i_pay' => 'required|max:30|unique:ingresos',
            'date_transaction' => 'required|date|date_format:"Y-m-d"|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            'cuentasxpagars' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'ingreso_ammount.required' => 'EL monto es requerido',
            'cuentasxpagars.required' => 'No se ha seleccionado ningún concepto de cobro',
            'method_pay_id.unique' => 'La referencia ya exíste',
        ];
    }
    public function attributes()
    {
        return [
            'number_i_pay' => 'Referencia Bancaria / Número de la transacción',
            'date_transaction' => 'Fecha de la transacción',
        ];
    }
}

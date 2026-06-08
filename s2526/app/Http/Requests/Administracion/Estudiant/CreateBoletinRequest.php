<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;

class CreateBoletinRequest extends FormRequest
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
        ];
    }
    public function messages()
    {
        return [
            'ingreso_ammount.numeric' => 'El monto de debe ser un número',
            'date_transaction.date' => 'El campo de fecha de la transacción debe contener una fecha válida',
            'date_transaction.required' => 'El campo de fecha es requerido',
            // 'body.required'  => 'A message is required',
        ];
    }
}

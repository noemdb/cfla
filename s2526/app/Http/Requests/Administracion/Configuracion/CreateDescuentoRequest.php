<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class CreateDescuentoRequest extends FormRequest
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
            'descuento_ammount' => 'required|numeric|min:1|max:100',
        ];
    }
    public function messages()
    {
        return [
            'descuento_ammount.numeric' => 'El monto de debe ser un número entero del 1 al 100',
            'descuento_ammount.min' => 'El monto de debe ser mayor a o igual 1',
            'descuento_ammount.max' => 'El monto de debe ser menor o igual a 100',
        ];
    }
}

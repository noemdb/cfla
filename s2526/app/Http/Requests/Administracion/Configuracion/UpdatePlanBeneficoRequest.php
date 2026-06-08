<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanBeneficoRequest extends FormRequest
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
            'estudiant_id' => 'required|integer',
            'descuento_id' => 'required|integer',
            'created_at' => 'required|date',
            'ffinal' => 'required|date',
        ];
    }
    public function messages()
    {
        return [
            'estudiant_id.required' => 'El estudiante es requerido',
            'descuento_id.required' => 'El descuento es requerido',
            'created_at.required' => 'La fecha inicial es requerida',
            'ffinal.required' => 'La fecha final es requerida',
        ];
    }
}

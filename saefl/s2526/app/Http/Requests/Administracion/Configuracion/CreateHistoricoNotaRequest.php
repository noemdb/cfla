<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class CreateHistoricoNotaRequest extends FormRequest
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
            'pestudio_id' => 'required|integer',
            'estudiant_id' => 'required|integer',
        ];
    }
    public function messages()
    {
        return [
            // 'code.unique' => 'El código ya exíste',
            // 'code_sm.unique' => 'La abreviación ya exíste',
            // 'code.max' => 'El código debe contener :max caracteres como máximo.',
            // 'code_sm.max' => 'La Abreviación debe contener :max caracteres como máximo.',
        ];
    }
}

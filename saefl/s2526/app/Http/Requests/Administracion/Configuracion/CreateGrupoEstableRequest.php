<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class CreateGrupoEstableRequest extends FormRequest
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
            'code' => 'required|max:10|unique:grupo_estables',
            'code_sm' => 'required|max:4',
            'name' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'code.unique' => 'El código ya exíste',
            // 'code_sm.unique' => 'La abreviación ya exíste',
            'code.max' => 'El código debe contener :max caracteres como máximo.',
            'code_sm.max' => 'La Abreviación debe contener :max caracteres como máximo.',
            'name.required' => 'El campo nombre es obligatorio.',
        ];
    }
}

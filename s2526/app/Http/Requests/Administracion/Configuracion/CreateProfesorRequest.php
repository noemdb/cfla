<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfesorRequest extends FormRequest
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
            'ci_profesor' => 'required|integer|unique:profesors',
            'gsemail' => 'nullable|email|unique:profesors',
        ];
    }
    public function messages()
    {
        return [
            'ci_profesor.unique' => 'La CI ya exíste',
            'ci_profesor.integer' => 'La CI debe ser sólo numeros',
            'gsemail.unique' => 'La dirección de correo GSuite ya existe',
        ];
    }
}

<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;

class CreateEstudiantRequest extends FormRequest
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
     * Get the validation rules that apply to the request. 'email' => 'unique:users,email_address'
     *
     * @return array
     */
    public function rules()
    {
        return [
            'planpago_id' => 'required|integer',
            'grado_inicial_id' => 'required|integer',
            'seccion_inicial' => 'required|integer',
            'ci_estudiant' => 'required|unique:estudiants',
            'name' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'city_birth' => 'required',
            'dir_address' => 'required',
            'status_active' => 'required',
            'gsemail' => 'nullable|email'
        ];
    }
    public function messages()
    {
        return [
            'ci_estudiant.required' => 'El campo cédula es requerido',
            'ci_estudiant.integer' => 'El campo cédula solo admite números',
            'status_active.required' => 'El Estado es requerido, seleccione: Activo o Desactivo',
            'gsemail.email' => 'Debe ser un dirección de correo válida'
        ];
    }
}

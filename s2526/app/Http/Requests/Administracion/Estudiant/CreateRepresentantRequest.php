<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;

class CreateRepresentantRequest extends FormRequest
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
            'ci_representant' => 'required|integer|unique:representants',
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
        ];
    }
    public function messages()
    {
        return [
            'ci_representant.required' => 'El campo cédula es requerido',
            'ci_representant.integer' => 'El campo cédula solo admite números',
        ];
    }
}

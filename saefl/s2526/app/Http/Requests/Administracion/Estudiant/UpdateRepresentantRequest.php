<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateRepresentantRequest extends FormRequest
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
        $request = Request::All(); //dd($request);

        return [
            'ci_representant' => 'required|integer',
            'name' => 'required',
            // 'email' => 'required|unique:users,email,{$this->representant->user_id}|email',
            // 'email' => 'required|unique:users,email,profmarielagimenez@hotmail.com|email',
            'email'=>'email',
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

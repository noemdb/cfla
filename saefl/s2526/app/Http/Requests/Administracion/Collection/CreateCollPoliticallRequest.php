<?php

namespace App\Http\Requests\Administracion\Collection;

use Illuminate\Foundation\Http\FormRequest;

class CreateCollPoliticallRequest extends FormRequest
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
            //political
            'pescolar_id' => 'required|integer',
            'name' => 'required',
            'code' => 'required',
            'description' => 'required',
            'canon' => 'required',
            'finicial' => 'required|date',
            'ffinal' => 'required|date|after:finicial',
            'status_debts' => 'required'
        ];
    }
    public function attributes()
    {
        return [
            //political
            'pescolar_id' => 'Período Escolar',
            'name' => 'Nombre',
            'code' => 'Código',
            'description' => 'Descripción',
            'finicial' => 'Fecha inicial',
            'ffinal' => 'Fecha Final',
            'canon' => 'Criterio de Agrupamiento',
            'status' => 'Estado',
            'status_debts' => 'Sólo a deudores?',
            'status_approved' => 'Estado de aprobación',
        ];
    }
}

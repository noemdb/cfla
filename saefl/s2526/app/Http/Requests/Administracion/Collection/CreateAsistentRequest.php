<?php

namespace App\Http\Requests\Administracion\Collection;

use Illuminate\Foundation\Http\FormRequest;

class CreateAsistentRequest extends FormRequest
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
            'status_debts' => 'required',

            //messege
            'subject' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'greeting' => 'required',
            'sentence' => 'required',
            'footer' => 'required',
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

            //messege
            'user_id' => 'Usuario',
            'coll_nivel_id' => 'Nivel',
            'subject' => 'Asunto',
            'title' => 'Título',
            'subtitle' => 'Subtítulo',
            'greeting' => 'Saludo formal',
            'consider' => 'Considerando',
            'sentence' => 'Solicitud',
            'waiting' => 'Esperando pronta respuesta',
            'footer' => 'Despedida',
        ];
    }
}

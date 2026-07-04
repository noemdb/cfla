<?php

namespace App\Http\Requests\Administracion\Collection;

use Illuminate\Foundation\Http\FormRequest;

class CreateCollMessegeRequest extends FormRequest
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

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class CreateMessegeRequest extends FormRequest
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
        
        $request = Request::All();

        return [
            'user_id' => 'required',
            'destino_user_id' => 'required',
            'mensaje' => 'required|max:255',
            'tipo' => 'required',
            'estado' => 'required',
        ];
    }
}

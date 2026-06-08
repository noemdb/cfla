<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class CreateRolRequest extends FormRequest
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

        //INI validando que finicial no esté vacio
        $date_after = '';
        if(isset($request['finicial'])){
            $date_after = "|after:".$request['finicial'];
        }
        //FIN validando que finicial no esté vacio

        return [
            'user_id' => 'required',
            'rol' => 'required|max:16',
            'area' => 'required|max:16',
            'descripcion' => 'required|max:255|min:3',
            'finicial' => 'required|date',
            'ffinal' => 'required|date'.$date_after,
            // 'finicial' => 'required|date|date_format:"Y-m-d"',
            // 'ffinal' => 'required|date|date_format:"Y-m-d"|after:'.$request['finicial'],
        ];
    }
}

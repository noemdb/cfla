<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateAutoridadRequest extends FormRequest
{
    private $route;

    public function __construct(Route $route){

        $this->route = $route;

    }
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

        // dd($this->route->parameter);
        // dd($this->route->parameter('tipo_id'));

        //INI validando que finicial no esté vacio
        // $date_after = '';
        // if(isset($request['finicial'])){
        //     $date_after = "|after:".$request['finicial'];
        // }
        //FIN validando que finicial no esté vacio
        return [
            'institucion_id' => 'required|integer',
            'pescolar_id' => 'required|integer',
            'name' => 'required',
            'lastname' => 'required',
            'position' => 'required',
            'user_id' => 'required|max:255|unique:autoridads,user_id,'.$this->route->parameter('id'),
            'tipo_id' => 'required|max:255|unique:autoridads,tipo_id,'.$this->route->parameter('id'),

            // 'finicial' => 'required|date|date_format:"Y-m-d"',
            // 'ffinal' => 'required|date|date_format:"Y-m-d"'.$date_after,
        ];
    }
    public function messages()
    {
        return [
            'tipo_id.unique' => 'El tipo de autoridad seleccionado ya está en uso',
            'user_id.unique' => 'El Usuario de autoridad seleccionado ya está en uso',
        ];
    }
}

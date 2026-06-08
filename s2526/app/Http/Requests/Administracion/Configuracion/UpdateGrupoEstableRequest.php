<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateGrupoEstableRequest extends FormRequest
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
    private $route;

    public function __construct(Route $route){
        $this->route = $route;
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
            'code' => 'required|max:10|unique:grupo_estables,code,'.$this->route->parameter('id'),
            'code_sm' => 'required|max:4',
            'name' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'code.unique' => 'El código ya exíste',
            // 'code_sm.unique' => 'La abreviación ya exíste',
            'code.max' => 'El código debe contener :max caracteres como máximo.',
            'code_sm.max' => 'La Abreviación debe contener :max caracteres como máximo.',
            'name.required' => 'El campo nombre es obligatorio.',
        ];
    }
}

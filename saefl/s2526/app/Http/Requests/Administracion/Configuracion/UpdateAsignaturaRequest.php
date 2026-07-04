<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateAsignaturaRequest extends FormRequest
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

        // dd($this->route->parameter('id'));

        return [
            // 'code' => 'required|max:10|unique:asignaturas,code,'.$this->route->parameter('id'),
            'code' => 'required|max:10|unique:asignaturas,code,'.$this->route->parameter('id'),
            'code_sm' => 'required|max:4',
        ];
    }
    public function messages()
    {
        return [
            'code.unique' => 'El código ya exíste',
            'code_sm.unique' => 'La abreviación ya exíste',
            'code.max' => 'El código debe contener :max caracteres como máximo.',
            'code_sm.max' => 'La Abreviación debe contener :max caracteres como máximo.',
        ];
    }
}

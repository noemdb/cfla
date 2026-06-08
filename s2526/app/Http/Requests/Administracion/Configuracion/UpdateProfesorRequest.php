<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateProfesorRequest extends FormRequest
{
    private $route; public function __construct(Route $route){ $this->route = $route; }

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
            'ci_profesor' => 'required|integer|unique:profesors,ci_profesor,'.$this->route->parameter('id'),
            'gsemail' => 'nullable|email|unique:profesors,gsemail,'.$this->route->parameter('id'),
        ];
    }
    public function messages()
    {
        return [
            'ci_profesor.unique' => 'La CI ya exíste',
            'ci_profesor.integer' => 'La CI debe ser sólo numeros',
            'gsemail.unique' => 'La dirección de correo GSuite ya existe',
        ];
    }
}

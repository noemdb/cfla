<?php

namespace App\Http\Requests\Administracion\Estudiant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateEstudiantRequest extends FormRequest
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
            'ci_estudiant'  => 'required|unique:estudiants,ci_estudiant,'.$this->route->parameter('id'),
            'name' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'city_birth' => 'required',
            'dir_address' => 'required',
            'status_active' => 'required',
            'gsemail' => 'nullable|email'
        ];
    }
    public function messages()
    {
        return [
            'ci_estudiant.required' => 'El campo cédula es requerido',
            'ci_estudiant.integer' => 'El campo cédula solo admite números',
            'status_active.required' => 'El Estado es requerido, seleccione: Activo o Desactivo',
            'gsemail.email' => 'Debe ser un dirección de correo válida'
        ];
    }
}

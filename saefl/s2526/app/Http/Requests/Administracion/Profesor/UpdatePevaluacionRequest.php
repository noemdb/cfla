<?php

namespace App\Http\Requests\Administracion\Profesor;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdatePevaluacionRequest extends FormRequest
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
        return [
            'pensum_id' => 'required|integer',
            'profesor_id' => 'required|integer',
            'description' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'pensum_id.required' => 'El pensum es requerido',
            'profesor_id.required' => 'El profesor es requerido',
            'description.required' => 'La descripción es requerida',
        ];
    }
}

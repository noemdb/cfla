<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateInstitucionRequest extends FormRequest
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
        //dd(Request::All());
        return [
            'name' => 'required',
            'rif_institution' => 'required',
            'email_institution' => 'required|max:255',

            // 'finicial' => 'required|date|date_format:"Y-m-d"',
            // 'ffinal' => 'required|date|date_format:"Y-m-d"'.$date_after,
        ];
    }
}

<?php

namespace App\Http\Requests\Administracion\Planpago;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateConceptoPagoRequest extends FormRequest
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
            'cuentaxpagar_id' => 'required|integer',
            'nom_concepto_pago_id' => 'required|integer',
            'concepto_ammount' => 'required|numeric|',
            'exchange_ammount' => 'required|numeric'
        ];
    }
    public function messages()
    {
        return [
            'concepto_ammount.numeric' => 'EL campo Monto debe ser numérico',
            'concepto_ammount.numeric' => 'EL campo Monto Cambiario debe ser numérico'
            // 'body.required'  => 'A message is required',
        ];
    }
}

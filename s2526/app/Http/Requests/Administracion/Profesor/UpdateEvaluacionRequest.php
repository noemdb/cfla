<?php

namespace App\Http\Requests\Administracion\Profesor;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\app\Pescolar\Escala;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;

class UpdateEvaluacionRequest extends FormRequest
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

        // dd($request);

        $valor_ctr = '0';
        if ($request['nota_type'] == 'ACUMULATIVA') {
            $pevaluacion = Pevaluacion::findOrFail($request['pevaluacion_id']);
            $escala_maximo = Escala::findOrFail($request['escala_id'])->maximo;

            // dd($pevaluacion->escala->maximo);

            $nota_max = intval($pevaluacion->escala->maximo);
            $nota_total = $pevaluacion->nota_total;
            $valor_max = intval( $nota_max - $nota_total - $escala_maximo );
            $valor_ctr = ($escala_maximo > $valor_max) ? 10 : 0 ;
        }

        // dd($valor_ctr,$nota_max,$nota_total,$valor_max,$request['nota_max']);

        return [
            'escala_id' => 'required|integer',
            'pevaluacion_id' => 'required',
            'nota_ctr' => 'required|min:'.$valor_ctr,
        ];
    }
    public function messages()
    {
        return [
            'escala_id.required' => 'La escala es requerida',
            'pevaluacion_id.required' => 'El plan de evealución es requerido',
            'nota_ctr.min' => 'la nota máxima o sumatoría de las notas máximas es mayor a lo permitido según escala del Plan de Evaluación',
        ];
    }
}

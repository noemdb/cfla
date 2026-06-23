<?php

namespace App\Http\Requests\Administracion\Configuracion;

use App\Models\app\Pescolar\ProfesorGuia;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CreateProfesorGuiaRequest extends FormRequest
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

        $profesors =
        ProfesorGuia::All()
            ->where('grado_id',$request['grado_id'])
            ->where('seccion_id',$request['seccion_id']);
            // ->where('lapso_id',$request['lapso_id']);
            // ->count();
        
        if ($request['lapso_id']<>'&all&') {
            $profesors = $profesors->where('lapso_id',$request['lapso_id']);            
        }

        $count = $profesors->count();

        $maximo = ($count>0) ? 0 : 1 ;        

        return [
            'maximo' => 'max:'.$maximo,
        ];
    }
    public function messages()
    {
        return [
            'maximo.max' => 'El grado seleccionado ya tiene profesor guía para las opciones selecionadas',
        ];
    }
    public function attributes()
    {
        return [
            'maximo' => 'Máximo',
        ];
    }
}

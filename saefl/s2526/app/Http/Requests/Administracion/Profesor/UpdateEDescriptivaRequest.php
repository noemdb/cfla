<?php

namespace App\Http\Requests\Administracion\Profesor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEDescriptivaRequest extends FormRequest
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
        return [
            'estudiant_id' => 'required|integer',
            'lapso_id' => 'max:4',
        ];
    }
    public function messages()
    {
        return [
            'estudiant_id.required' => 'El estudiante es requerido',
            'lapso_id.max' => 'Ya se ha evaluado cualitativamente al estudiante',
        ];
    }
}

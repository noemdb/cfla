<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Routing\Route;
use Illuminate\Http\Request;

use App\Models\app\Institucion\Banco;

class UpdateBancoRequest extends FormRequest
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
            'institucion_id' => 'required|integer',
            'name' => 'required',
            'number_id_bank' => 'required|integer',
            'number_acount_bank' => 'required',
            'status_active_bank' => 'required',
            'currency_id' => 'required'
        ];
    }
    public function attributes()
    {
        $list_comment = Banco::COLUMN_COMMENTS;
        return [
            'institucion_id' => $list_comment['institucion_id'],
            'name' => $list_comment['name'],
            'number_id_bank' => $list_comment['number_id_bank'],
            'number_acount_bank' => $list_comment['number_acount_bank'],
            'status_active_bank' => $list_comment['status_active_bank'],
            'currency_id' => $list_comment['currency_id']
        ];
    }
}

<?php

namespace App\Http\Requests\Administracion\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

use App\Models\app\Planpago\ExchangeRate;

class CreateExchangeRateRequest extends FormRequest
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
            'date' => 'required|date|date_format:"Y-m-d"|unique:exchange_rates,date|before_or_equal:'.Carbon::now()->format('Y-m-d'),
            'ammount' => 'required|numeric',
            'currency_id' => 'required',
            'currency_referential_id' => 'required',
            'source' => 'required',
            'user_id' => 'required|integer',
        ];
    }

    public function attributes()
    {
        $list_comment = ExchangeRate::COLUMN_COMMENTS;
        return [
            'date' => $list_comment['date'],
            'ammount' => $list_comment['ammount'],
            'currency_id' => $list_comment['currency_id'],
            'currency_referential_id' => $list_comment['currency_referential_id'],
            'source' => $list_comment['source'],
            'user_id' => $list_comment['user_id']
        ];
    }
}

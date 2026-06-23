<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateSelectOptRequest extends FormRequest
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
            'view' => 'required|max:255',
            'table' => 'required|max:255',
            'name' => 'required|max:255',
            'value' => 'required|max:255',
        ];
    }
}

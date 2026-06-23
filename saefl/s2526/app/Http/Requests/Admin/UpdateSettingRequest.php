<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Routing\Route;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
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

        return [
            'user_id' => 'required',
            'name' =>'required',
            'value' => 'required',
        ];
    }
}

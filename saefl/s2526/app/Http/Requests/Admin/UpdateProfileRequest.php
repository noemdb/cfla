<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class UpdateProfileRequest extends FormRequest
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
            'firstname' => 'required|max:255|min:3',
            'lastname' => 'required|max:255|min:3',
            // 'email' => 'required|max:255|unique:profiles,email,'.$this->route->parameter('profile'),
            // 'user_id' => 'required',
        ];
    }
}

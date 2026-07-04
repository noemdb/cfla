<?php

namespace App\Http\Requests\Admin;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateUserRequest extends FormRequest
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
        // $request = Request::All();
        // dd($request);
        return [
            'username' => 'required|min:4|max:255|unique:users',
            'password' => 'required|min:4',
            'email' => 'required|unique:users,email|email',
            'is_active' => 'required',
            //'worker_order' => 'integer',
            //
        ];
    }

    public function attributes()
    {
        $list_comment = User::COLUMN_COMMENTS;
        return [
            'worker_order' => $list_comment['worker_order']
        ];
    }
}

<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//validation request
use App\Http\Requests\Admin\UpdateUserRequest;

//models
use App\User;

class UserController extends Controller
{
    /*
        Constructor, verifica login del usuario - Profile
    */
    public function __construct(){
        $this->middleware(['auth','is_profesor']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rule_pass = (! empty($request['password'])) ? 'required|min:6' : '' ;
        $request->validate([
            'username' => 'required|max:255|unique:users,username,'.$id,
            'password' => $rule_pass,
        ]);

        $user->fill($request->all());

        $user->save();

        $messenge = trans('db_oper_result.user_update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('profesors.home');
    }
}

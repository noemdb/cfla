<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use App\User;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); //dd($user);

        $user->fill(['username'=>$request->username,'password'=>$request->password,'status_update'=>true]);

        $user->save();

        $messenge = trans('db_oper_result.user_update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('academicos.home');
    }
}

<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;

use App\Http\Requests\Admin\UpdateUserRequest;

//models
use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\Models\sys\SelectOpt;

class UserController extends Controller
{
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); //dd($user);

        $user->fill(['username'=>$request->username,'password'=>$request->password,'status_update'=>true]);

        $user->save();

        $messenge = trans('db_oper_result.user_update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('representants.home');
    }
}

<?php

namespace App\Http\Controllers\Moviles;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthCustomController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $redirect = (empty($request->redirect)) ? 'dashboard' : $request->redirect;

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended($redirect)
                        ->withSuccess('Signed in');
        }
        // return redirect("login")->withSuccess('Login details are not valid');
        return redirect($redirect)->with('status', 'Error en las credenciales ingresadas');
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect('movile/android/welcome');
    }

    public function resetpassword() {
        Session::flush();
        Auth::logout();
        return Redirect('movile/android/welcome');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ✅ Usa 'username' en lugar de 'email'
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Solo usuarios activos pueden iniciar sesión
        $credentials['is_active'] = 'enable';

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirigir según el rol, ignorando "intended" para evitar
            // que usuarios sin privilegios accedan a rutas protegidas.
            if ($user->is_admin || $user->is_diagnostic) {
                return redirect()->to('/admin');
            }

            if ($user->is_planner) {
                return redirect()->route('app.planning.index');
            }

            if ($user->isProfesor()) {
                return redirect()->to('/app/profesors/home');
            }

            return redirect()->to('/');
        }

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

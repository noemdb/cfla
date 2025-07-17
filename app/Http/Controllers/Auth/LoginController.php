<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        dd('123');
        // Si ya está autenticado y es admin, redirigir al dashboard
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Si está autenticado pero no es admin, cerrar sesión
        if (auth()->check() && !auth()->user()->is_admin) {
            Auth::logout();
            session()->flash('error', 'No tienes permisos de administrador.');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Intentar autenticar
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Verificar si es admin
            if (auth()->user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Si no es admin, cerrar sesión y mostrar error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'No tienes permisos de administrador.',
            ])->withInput($request->except('password'));
        }

        // Credenciales incorrectas
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->except('password'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}

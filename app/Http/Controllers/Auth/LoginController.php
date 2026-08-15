<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Binnacle;
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

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Bitácora de auditoría (Spec BINNACLE-001, Fase 1).
            Binnacle::logAuthEvent('user_login', [
                'subject' => $user,
                'title' => 'Inicio de sesión',
                'description' => "El usuario {$user->username} inició sesión",
                'severity' => 'info',
            ]);

            // Redirigir según el rol, ignorando "intended" para evitar
            // que usuarios sin privilegios accedan a rutas protegidas.
            if ($user->is_admin || $user->is_diagnostic) {
                return redirect()->to('/admin');
            }

            if ($user->is_planner) {
                return redirect()->route('app.planning.index');
            }

            if ($user->is_coordinacion) {
                return redirect()->route('app.coordinacion.index');
            }

            if ($user->is_leadership) {
                return redirect()->route('app.leadership.dashboard');
            }

            if ($user->isDirector()) {
                return redirect()->route('app.director.index');
            }

            if ($user->isProfesor()) {
                return redirect()->to('/app/profesors/home');
            }

            if ($user->is_student) {
                return redirect()->to('/app/estudiante/home');
            }

            return redirect()->to('/');
        }

        // Bitácora de auditoría (Spec BINNACLE-001, Fase 1): intento fallido.
        Binnacle::logAuthEvent('user_login_failed', [
            'subject_identifier' => $request->input('username'),
            'title' => 'Intento de inicio de sesión fallido',
            'description' => "Intento fallido de inicio de sesión para: {$request->input('username')}",
            'severity' => 'warning',
        ]);

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        // Bitácora de auditoría (Spec BINNACLE-001, Fase 1).
        Binnacle::logAuthEvent('user_logout', [
            'subject' => $user,
            'title' => 'Cierre de sesión',
            'description' => $user ? "El usuario {$user->username} cerró sesión" : 'Cierre de sesión',
            'severity' => 'info',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

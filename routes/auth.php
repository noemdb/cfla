<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| These are the routes for authentication: login, registration, password
| reset, and email verification.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', fn (string $token) => view('auth.reset-password', ['token' => $token]))
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Enviar email de verificación
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/confirm-password', fn () => view('auth.confirm-password'))->name('password.confirm');
    Route::post('/confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    // Actualizar contraseña (perfil de usuario)
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])
        ->name('password.update');

    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
        ->name('logout');
});
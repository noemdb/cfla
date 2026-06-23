<?php

// use App\Http\Controllers\Bienestar\Tab\ResendController;
use App\Http\Controllers\Administracion\Tab\ResendController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/resend', [ResendController::class, 'index'])->name('administracion.resend.index');
    Route::get('/resend/{id}', [ResendController::class, 'show'])->name('administracion.resend.show');
});

/* resource */
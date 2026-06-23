<?php

use App\Http\Controllers\Bienestar\Tab\ResendController;
use Illuminate\Support\Facades\Route;

Route::get('/resend', [ResendController::class, 'index'])->name('bienestar.resend.index');
Route::get('/resend/{id}', [ResendController::class, 'show'])->name('bienestar.resend.show');

/* resource */

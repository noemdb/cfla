<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\HomeInicialController;

Route::get('/home', [HomeInicialController::class,'home'])->name('inicials.home');
Route::get('/use-cases', [HomeInicialController::class,'useCases'])->name('inicials.use-cases');

?>

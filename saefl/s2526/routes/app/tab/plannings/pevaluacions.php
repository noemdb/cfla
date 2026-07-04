<?php

/* resource */

use App\Http\Controllers\Planning\Tab\PevaluacionController;

Route::get('/pevaluacions/index', [PevaluacionController::class, 'index'] )->name('plannings.pevaluacions.index');
Route::get('/pevaluacions/evaluacions/index', [PevaluacionController::class, 'index'] )->name('plannings.evaluacions.index');

?>
<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\PevaluacionController;

Route::get('/pevaluacions/index', [PevaluacionController::class, 'pevaluacions'] )->name('evaluacions.pevaluacions.index');
Route::get('/pevaluacions/evaluacions/index', [PevaluacionController::class, 'index'] )->name('evaluacions.pevaluacions.evaluacions.index');

?>
<?php

/* resource */


use App\Http\Controllers\Evaluacion\Tab\ProfesorController;

Route::get('/profesors/index', [ProfesorController::class, 'index'] )->name('evaluacions.profesors.index');
?>
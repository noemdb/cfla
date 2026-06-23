<?php

/* resource */


use App\Http\Controllers\Planning\Tab\ProfesorController;

Route::get('/profesors/index', [ProfesorController::class, 'index'] )->name('plannings.profesors.index');

?>
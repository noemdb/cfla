<?php

/* resource */
use App\Http\Controllers\Planning\Tab\EstudiantController;

Route::get('/estudiants/index', [EstudiantController::class, 'index'] )->name('plannings.estudiants.index');
Route::get('/estudiants/format/{id}', [EstudiantController::class, 'format'] )->name('plannings.estudiants.format');


?>

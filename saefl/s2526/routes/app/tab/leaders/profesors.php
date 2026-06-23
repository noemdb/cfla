<?php

/* resource */

use App\Http\Controllers\Leader\Tab\ProfesorController;

Route::get('/profesors/index', 'Tab\ProfesorController@index')->name('leaders.profesors.index');
// Route::get('/pevaluacions/evaluacions/index', 'Tab\ProfesorController@evaluacions')->name('leaders.pevaluacions.evaluacions.index');
Route::get('/pevaluacions/evaluacions/index', [ProfesorController::class,'evaluacions'])->name('leaders.pevaluacions.evaluacions.index');


?>

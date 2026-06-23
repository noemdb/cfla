<?php

/* resource */
use App\Http\Controllers\Bienestar\Tab\ActivityController;

Route::get('/activities', 'Tab\ActivityController@index')->name('bienestars.activities.index');

Route::get('/activities/format/{id}', [ActivityController::class, 'format'])->name('bienestars.activities.format');
Route::get('/activities/resume/{id}', [ActivityController::class, 'resume'])->name('bienestars.activities.resume');


?>
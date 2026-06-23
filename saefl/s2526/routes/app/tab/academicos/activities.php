<?php

/* resource */

use App\Http\Controllers\Academico\Tab\ActivitController;

Route::get('/activities/index', [ActivitController::class, 'index'])->name('academicos.activities.index');
Route::get('/activities/format/{id}', [ActivitController::class, 'format'])->name('academicos.activities.format');
Route::get('/activities/resume/{id}', [ActivitController::class, 'resume'])->name('academicos.activities.resume');

?>

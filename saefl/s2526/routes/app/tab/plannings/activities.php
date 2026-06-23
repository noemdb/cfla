<?php

/* resource */
use App\Http\Controllers\Planning\Tab\ActivityController;

Route::get('/activities/index', [ActivityController::class, 'index'] )->name('plannings.activities.index');
Route::get('/activities/format/{id}', [ActivityController::class, 'format'] )->name('plannings.activities.format');
Route::get('/activities/resume/{id}', [ActivityController::class, 'resume'] )->name('plannings.activities.resume');


?>

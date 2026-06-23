<?php

/* resource */

use App\Http\Controllers\Bienestar\Tab\CatchmentController;

Route::get('/matriculations/catchments/index', [CatchmentController::class, 'index'])->name('bienestars.matriculations.catchments.index');
Route::get('/matriculations/interviews/index', [CatchmentController::class, 'interviews'])->name('bienestars.matriculations.interviews.index');
Route::delete('/matriculations/catchments/{id}', [CatchmentController::class, 'destroy'])->name('bienestars.matriculations.catchments.destroy');
Route::delete('/matriculations/catchments/force_destroy/{id}', [CatchmentController::class, 'force_destroy'])->name('bienestars.matriculations.catchments.force_destroy');
Route::delete('/matriculations/interviews/{id}', [CatchmentController::class, 'destroy_interview'])->name('bienestars.matriculations.interviews.destroy');

Route::get('/matriculations/interviews/edit/{id}', [CatchmentController::class, 'edit_interview'])->name('bienestars.matriculations.interviews.edit');
Route::put('/matriculations/interviews/update/{id}', [CatchmentController::class, 'update_interview'])->name('bienestars.matriculations.interviews.update');


// Route::get('/catchments', [CatchmentController::class,'index'])->name('bienestars.catchments.index');
// Route::get('/catchments/batch', 'Tab\EnrollmentController@batch')->name('bienestars.catchments.batch');

// Route::get('/catchments/summaries', 'Tab\EnrollmentController@summaries')->name('bienestars.catchments.summaries');

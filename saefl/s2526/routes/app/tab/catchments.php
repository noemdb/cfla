<?php

/* resource */

use App\Http\Controllers\Administracion\Tab\Matriculation\CatchmentController;

Route::get('/matriculations/catchments/index', [CatchmentController::class,'index'])->name('administracion.matriculations.catchments.index');
Route::get('/matriculations/catchments/indicators', [CatchmentController::class,'indicators'])->name('administracion.matriculations.catchments.indicators');
Route::delete('/matriculations/catchments/destroy/{id}', [CatchmentController::class,'destroy'])->name('administracion.matriculations.catchments.destroy');
Route::delete('/matriculations/catchments/force_destroy/{id}', [CatchmentController::class,'force_destroy'])->name('administracion.matriculations.catchments.force_destroy');

Route::get('/matriculations/catchment_groups/index', [CatchmentController::class,'index_groups'])->name('administracion.matriculations.catchment_groups.index');
Route::get('/matriculations/catchment_groups/show', [CatchmentController::class,'show_groups'])->name('administracion.matriculations.catchment_groups.show');
Route::get('/matriculations/catchment_groups/edit/{id}', [CatchmentController::class,'edit_group'])->name('administracion.matriculations.catchment_groups.edit');
Route::put('/matriculations/catchment_groups/update/{id}', [CatchmentController::class,'update_group'])->name('administracion.matriculations.catchment_groups.update');

Route::get('/matriculations/catchment_activities/index', [CatchmentController::class,'index_activity'])->name('administracion.matriculations.catchment_activities.index');
Route::get('/matriculations/catchment_activities/show', [CatchmentController::class,'show_activity'])->name('administracion.matriculations.catchment_activities.show');
Route::get('/matriculations/catchment_activities/edit/{id}', [CatchmentController::class,'edit_activity'])->name('administracion.matriculations.catchment_activities.edit');
Route::put('/matriculations/catchment_activities/update/{id}', [CatchmentController::class,'update_activity'])->name('administracion.matriculations.catchment_activities.update');

Route::get('/matriculations/interviews/index', [CatchmentController::class,'interviews'])->name('administracion.matriculations.interviews.index');
Route::get('/matriculations/interviews/edit/{id}', [CatchmentController::class,'edit_interview'])->name('administracion.matriculations.interviews.edit');
Route::put('/matriculations/interviews/update/{id}', [CatchmentController::class,'update_interview'])->name('administracion.matriculations.interviews.update');
Route::delete('/matriculations/interviews/destroy/{id}', [CatchmentController::class,'destroy_interview'])->name('administracion.matriculations.interviews.destroy');


Route::get('/matriculations/catchment_notifications/eventualities', [CatchmentController::class,'eventualities'])->name('administracion.matriculations.catchment_notifications.eventualities');

// Route::get('/matriculations/catchments/index', [CatchmentController::class,'index'])->name('administracion.matriculations.catchments.index');

?>
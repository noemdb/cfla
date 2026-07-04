<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\PaseController;

Route::get('/permissions/pases/index', [PaseController::class,'index'])->name('evaluacions.permissions.pases.index');

Route::get('/permissions/pases/create', [PaseController::class,'create'])->name('evaluacions.permissions.pases.create');
Route::post('/permissions/pases/store', [PaseController::class,'store'])->name('evaluacions.permissions.pases.store');

Route::get('/permissions/pases/edit/{id}', [PaseController::class,'edit'])->name('evaluacions.permissions.pases.edit');
Route::put('/permissions/pases/update/{id}', [PaseController::class,'update'])->name('evaluacions.permissions.pases.update');

Route::get('/permissions/pases/send/{id}', [PaseController::class,'send'])->name('evaluacions.permissions.pases.send');

Route::get('/permissions/pases/view/{id}', [PaseController::class,'view'])->name('evaluacions.permissions.pases.view');

?>
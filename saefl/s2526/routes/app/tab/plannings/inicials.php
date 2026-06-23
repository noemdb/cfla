<?php

/* resource */

use App\Http\Controllers\Planning\Tab\InicialController;

Route::get('/inicials/index', [InicialController::class, 'index'])->name('plannings.inicials.index');

// Route::get('/inicials/eiplanningwks/format/index/{id}', [InicialController::class,'format_eiplanningwk'])->name('plannings.eiplanningwks.format.index');
// Route::get('/inicials/eiprojectks/format/index/{id}', [InicialController::class,'format_eiprojectks'])->name('plannings.eiprojectks.format.index');

Route::get('/inicials/eiplanningwks/format/index/{id}', [InicialController::class,'format_eiplanningwk'])->name('plannings.eiplanningwks.format.index');
Route::get('/inicials/eiplanningbwks/format/index/{id}', [InicialController::class,'format_eiplanningbwk'])->name('plannings.eiplanningwbks.format.index');
Route::get('/inicials/eiprojectks/format/index/{id}', [InicialController::class,'format_eiprojectks'])->name('plannings.eiprojectks.format.index');
Route::get('/inicials/eispecialks/format/index/{id}', [InicialController::class,'format_eispecialks'])->name('plannings.eispecialks.format.index');
Route::get('/inicials/eievaluationks/format/index/{id}', [InicialController::class,'format_eievaluationks'])->name('plannings.eievaluationks.format.index');

?>

<?php

/* resource */

use App\Http\Controllers\Academico\Tab\InicialController;

Route::get('/inicials/index', [InicialController::class, 'index'])->name('academicos.inicials.index');
Route::get('/inicials/eiplanningwks/format/index/{id}', [InicialController::class,'format_eiplanningwk'])->name('academicos.eiplanningwks.format.index');
Route::get('/inicials/eiprojectks/format/index/{id}', [InicialController::class,'format_eiprojectks'])->name('academicos.eiprojectks.format.index');

?>

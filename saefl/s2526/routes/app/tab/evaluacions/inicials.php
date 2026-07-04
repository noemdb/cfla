<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\InicialController;
use App\Http\Controllers\Inicial\Tab\HomeInicialController;

Route::get('/inicials/index', [InicialController::class, 'index'])->name('evaluacions.inicials.index');
Route::get('/inicials/eiplanningwks/format/index/{id}', [InicialController::class,'format_eiplanningwk'])->name('evaluacions.eiplanningwks.format.index');
Route::get('/inicials/eiplanningbwks/format/index/{id}', [InicialController::class,'format_eiplanningbwk'])->name('evaluacions.eiplanningwbks.format.index');
Route::get('/inicials/eiprojectks/format/index/{id}', [InicialController::class,'format_eiprojectks'])->name('evaluacions.eiprojectks.format.index');
Route::get('/inicials/eispecialks/format/index/{id}', [InicialController::class,'format_eispecialks'])->name('evaluacions.eispecialks.format.index');
Route::get('/inicials/eievaluationks/format/index/{id}', [InicialController::class,'format_eievaluationks'])->name('evaluacions.eievaluationks.format.index');

Route::get('/eifinalks/estudiant/{estudiant}/lapso/{lapso}/print-all', [InicialController::class, 'printAllforLapso'])->name('evaluacions.eifinalks.print-all-for-lapso');

Route::get('/use-cases', [InicialController::class,'useCases'])->name('evaluacions.inicials.use-cases');

?>

<?php

use App\Http\Controllers\Helper\ManulasController;

// Rutas para partials del modal de navegación
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Helper'], function () {
    Route::get('/helpers/evaluacion/diagnostic', [ManulasController::class, 'diagnostic'])->name('helpers.evaluacion.diagnostic');
    Route::get('/helpers/evaluacion/pases', [ManulasController::class, 'pases'])->name('helpers.evaluacion.pases');
    Route::get('/helpers/evaluacion/excecutions', [ManulasController::class, 'excecutions'])->name('helpers.evaluacion.excecutions');
});

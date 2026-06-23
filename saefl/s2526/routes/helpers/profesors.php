<?php

use App\Http\Controllers\Helper\ManulasController;

// Rutas para partials del modal de navegación
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Helper'], function () {
    Route::get('/helpers/profesors/activities', [ManulasController::class, 'activities'])->name('helpers.profesors.activities');
});

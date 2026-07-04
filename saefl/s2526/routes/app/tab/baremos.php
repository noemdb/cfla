<?php

/* resource */

use App\Http\Controllers\Administracion\Tab\BaremoController;

Route::get('/configuraciones/baremos/index', [BaremoController::class, "index"])->name('administracion.configuraciones.baremos.index');

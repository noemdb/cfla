<?php

/* resource */

use App\Http\Controllers\Administracion\Tab\Diagnostic\ReferentController;

Route::get('/diagnostics/referents/index', [ReferentController::class,"index"])->name('administracion.diagnostics.referents.index');
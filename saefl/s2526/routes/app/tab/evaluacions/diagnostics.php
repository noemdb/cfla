<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\DiagnosticController;

Route::get('/diagnostics/index', [DiagnosticController::class, 'index'])->name('evaluacions.diagnostics.index');

?>

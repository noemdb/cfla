<?php

/* resource */

use App\Http\Controllers\Profesor\Tab\DiagnosticController;

Route::get('/diagnostics/index', [DiagnosticController::class, 'index'])->name('profesors.diagnostics.index');

?>

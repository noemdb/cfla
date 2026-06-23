<?php

/* resource */

use App\Http\Controllers\Planning\Tab\DiagnosticController;

Route::get('/diagnostics/index', [DiagnosticController::class, 'index'])->name('plannings.diagnostics.index');

?>

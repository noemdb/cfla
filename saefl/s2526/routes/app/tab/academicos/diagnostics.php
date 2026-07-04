<?php

/* resource */
use App\Http\Controllers\Academico\Tab\DiagnosticController;

Route::get('/diagnostics/index', [DiagnosticController::class, 'index'])->name('academicos.diagnostics.index');

?>

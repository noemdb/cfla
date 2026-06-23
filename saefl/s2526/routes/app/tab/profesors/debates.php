<?php

/* resource */

use App\Http\Controllers\Profesor\Tab\DebateController;

Route::get('/debates/index', [DebateController::class, 'index'])->name('profesors.debates.index');

?>

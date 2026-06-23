<?php

/* resource */

use App\Http\Controllers\Profesor\Tab\DebateController;

Route::get('/competitions/index', [DebateController::class, 'competitions'])->name('profesors.competitions.index');

?>

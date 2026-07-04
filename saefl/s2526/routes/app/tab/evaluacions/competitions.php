<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\CompetitionController;

Route::get('/competitions/index', [CompetitionController::class, 'index'] )->name('evaluacions.competitions.index');


?>

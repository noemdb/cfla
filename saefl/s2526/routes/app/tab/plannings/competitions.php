<?php

/* resource */

use App\Http\Controllers\Planning\Tab\CompetitionController;

Route::get('/competitions/index', [CompetitionController::class, 'index'] )->name('plannings.competitions.index');
Route::get('/competitions/indicators', [CompetitionController::class, 'indicators'] )->name('plannings.competitions.indicators');


?>

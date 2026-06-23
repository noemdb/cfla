<?php

/* resource */

use App\Http\Controllers\Leader\Tab\CompetitionController;

Route::get('/competitions/index', [CompetitionController::class, 'index'] )->name('leaders.competitions.index');
Route::get('/competitions/indicators', [CompetitionController::class, 'indicators'] )->name('leaders.competitions.indicators');

?>

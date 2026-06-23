<?php

/* PDF */

use App\Http\Controllers\Leader\PDF\CompetitionController;

Route::get('/competitions/list/{grado_id}', [CompetitionController::class, 'batch'])->name('leaders.competitions.batch.pdf');
Route::get('/competitions/cards/{pensum_id}/{category}', [CompetitionController::class, 'cards'])->name('leaders.competitions.cards.pdf');
Route::get('/competitions/list/{pensum_id}/{category}', [CompetitionController::class, 'list'])->name('leaders.competitions.list.pdf');

?>

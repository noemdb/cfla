<?php
/* PDF */

use App\Http\Controllers\Planning\PDF\CompetitionController;

Route::get('/competitions/cards/{pensum_id}/{category}', [CompetitionController::class, 'cards'] )->name('evaluacions.competitions.cards.pdf');
Route::get('/competitions/list/{pensum_id}/{category}', [CompetitionController::class, 'list'] )->name('evaluacions.competitions.list.pdf');

?>

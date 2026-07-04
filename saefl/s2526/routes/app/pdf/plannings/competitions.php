<?php
/* PDF */

Route::get('/competitions/list/{grado_id}', 'PDF\CompetitionController@batch')->name('plannings.competitions.batch.pdf');
Route::get('/competitions/cards/{pensum_id}/{category}', 'PDF\CompetitionController@cards')->name('plannings.competitions.cards.pdf');
Route::get('/competitions/list/{pensum_id}/{category}', 'PDF\CompetitionController@list')->name('plannings.competitions.list.pdf');

?>

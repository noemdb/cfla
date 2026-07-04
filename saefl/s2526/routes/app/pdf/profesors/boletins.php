<?php
/* PDF */

Route::get('/boletins/boletin/pdf/{id}', 'PDF\BoletinController@boletin')->name('profesors.boletins.boletin.pdf');
// Route::get('/boletins/boletin/pdf/{id}', 'PDF\BoletinController@boletin')->name('profesors.boletins.boletin.pdf');
// Route::get('/boletins/boletin/sabana/pdf/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\BoletinController@sabana')->name('profesors.boletins.sabana.pdf');
Route::get('/boletins/boletin/sabana/pdf/{pevaluacion_id}', 'PDF\BoletinController@sabana')->name('profesors.boletins.sabana.pdf');
Route::get('/boletins/boletin/sabana_single/pdf/{pensum_id}/{seccion_id}', 'PDF\BoletinController@sabana_single')->name('profesors.boletins.sabana_single.pdf');




?>

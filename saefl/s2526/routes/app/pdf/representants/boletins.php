<?php
/* PDF */

Route::get('/boletins/boletin/pdf/{estudiant_id}/{lapso_id}', 'PDF\BoletinController@boletin')
->middleware('check.solvency:estudiant')
->name('representants.boletins.boletin.pdf');
Route::get('/boletins/boletin/sabana_single/pdf/{pensum_id}/{seccion_id}', 'PDF\BoletinController@sabana_single')->name('representants.boletins.sabana_single.pdf');
Route::get('/boletins/edescriptiva/pdf/{estudiant_id}', 'PDF\BoletinController@edescriptiva')
->name('representants.boletins.edescriptiva.pdf');

Route::get('/boletins/boletin/corte/pdf/{estudiant_id}/{lapso_id}', 'PDF\BoletinController@corte')
->middleware('check.solvency:estudiant')
->name('representants.boletins.corte.pdf');




?>

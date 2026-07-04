<?php
/* PDF */

Route::get('/boletins/boletin/pdf/{estudiant_id}/{lapso_id}', 'PDF\BoletinController@boletin')->name('administracion.boletins.boletin.pdf');

Route::get('/boletins/boletin/corte/pdf/{estudiant_id}/{lapso_id}', 'PDF\BoletinController@corte')->name('administracion.boletins.corte.pdf');
Route::get('/boletins/boletin/lote/corte/pdf/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\BoletinController@lote_corte')->name('administracion.boletins.boletin.lote.corte.pdf');

Route::get('/boletins/boletin/lote/pdf/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\BoletinController@lote_boletin')->name('administracion.boletins.boletin.lote.pdf');
Route::get('/boletins/boletin/lote/sabana/pdf/{pestudio_id}/{lapso_id}', 'PDF\BoletinController@lote_sabana')->name('administracion.boletins.lote.sabana.pdf');
Route::get('/boletins/boletin/sabana/pdf/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\BoletinController@sabana')->name('administracion.boletins.sabana.pdf');
Route::get('/boletins/boletin/sabanafull/pdf/{grado_id}/{seccion_id}', 'PDF\BoletinController@sabanafull')->name('administracion.boletins.sabanafull.pdf');
// Route::get('/boletins/boletin/sabana_simple/pdf/{pevaluacion_id}', 'PDF\BoletinController@sabana_simple')->name('administracion.boletins.sabana_simple.pdf');
Route::get('/boletins/boletin/sabana_simple/pdf/{grado_id}/{seccion_id}/{lapso_id}/{pensum_id}', 'PDF\BoletinController@sabana_simple')->name('administracion.boletins.sabana_simple.pdf');
Route::get('/boletins/boletin/lote/sabanafull/pdf/{pestudio_id}/{lapso_id}', 'PDF\BoletinController@lote_sabanafull')->name('administracion.boletins.lote.sabanafull.pdf');
Route::get('/boletins/boletin/sabana_profesor/pdf/{pevaluacion_id}', 'PDF\BoletinController@sabana_profesor')->name('administracion.boletins.sabana_profesor.pdf');
Route::get('/boletins/boletin/resumen_final/pdf/{seccion_id}', 'PDF\BoletinController@resumen_final')->name('administracion.boletins.resumen_final.pdf');

Route::get('/boletins/boletin/positions/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\BoletinController@positions')->name('administracion.boletins.positions.pdf');

?>

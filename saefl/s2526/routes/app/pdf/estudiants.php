<?php
/* PDF */
Route::get('/estudiants/listado/pdf', 'PDF\EstudiantController@listado')->name('administracion.estudiants.listado.pdf');
Route::get('/estudiants/recibo/pdf/{id}', 'PDF\EstudiantController@recibo')->name('administracion.estudiants.recibo.pdf');
Route::get('/estudiants/pdf/carta_bconducta/{estudiant_id}', 'PDF\EstudiantController@carta_bconducta')->name('administracion.estudiants.pdf.carta_bconducta');

?>

<?php
/* PDF */

Route::get('/titulos/pdf/titulo/{titulo_id}', 'PDF\TituloController@titulo')->name('administracion.titulos.pdf.titulo');
Route::get('/titulos/pdf/titulo/lote/{pestudio_id}', 'PDF\TituloController@titulo_lote')->name('administracion.titulos.pdf.titulo.lote');
Route::get('/titulos/pdf/carta_culminacion/{estudiant_id}/{registro_titulo_id}', 'PDF\TituloController@carta_culminacion')->name('administracion.titulos.pdf.carta_culminacion');


?>

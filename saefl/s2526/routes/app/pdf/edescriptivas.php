<?php
/* PDF */

Route::get('/edescriptivas/pdf/{estudiant_id}', 'PDF\EDescriptivaController@edescriptiva')->name('administracion.edescriptivas.edescriptiva.pdf');
Route::get('/edescriptivas/lote/pdf/{grado_id}/{seccion_id}/{lapso_id}', 'PDF\EDescriptivaController@lote_edescriptiva')->name('administracion.edescriptivas.edescriptiva.lote.pdf');

?>

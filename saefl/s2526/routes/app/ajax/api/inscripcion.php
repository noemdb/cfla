<?php
/* retiro administrativo de estudiantes create */

//Apis
Route::get('/ajax/api/inscripciones/retiro/{id}', 'Ajax\Api\InscripcionController@retiro')->name('administracion.ajax.api.inscripciones.retiro');

?>

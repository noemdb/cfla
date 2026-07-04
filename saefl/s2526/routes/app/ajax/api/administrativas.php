<?php
/* retiro administrativo de estudiantes create */

//Apis
Route::get('/ajax/api/administrativas/retiro/{id}', 'Ajax\Api\AdministrativaController@retiro')->name('administracion.ajax.api.administrativas.retiro');

?>

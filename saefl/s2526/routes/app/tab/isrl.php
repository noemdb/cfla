<?php

/* resource */
Route::get('/isrl/index', 'Tab\IsrlController@index')->name('administracion.isrl.index');
Route::get('/isrl/conceptopagos/outstanding', 'Tab\IsrlController@outStanding')->name('administracion.isrl.conceptopagos.outstanding');
Route::get('/isrl/conceptopagos/paids', 'Tab\IsrlController@paids')->name('administracion.isrl.conceptopagos.paids');

?>

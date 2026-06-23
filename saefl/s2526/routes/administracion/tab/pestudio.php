<?php
Route::get('/configuraciones/pestudio', 'Tab\Configuracion\PestudioController@index')
->name('administracion.configuraciones.pestudio');
Route::get('/configuraciones/pestudio/{id}', 'Tab\Configuracion\PestudioController@edit')
->name('administracion.configuraciones.pestudio.edit');
Route::put('/configuraciones/pestudio/{id}', 'Tab\Configuracion\PestudioController@update')
->name('administracion.configuraciones.pestudio.update');
<?php
Route::get('/configuraciones/peducativo', 'Tab\Configuracion\PeducativoController@index')
->name('administracion.configuraciones.peducativo');
Route::get('/configuraciones/peducativo/{id}', 'Tab\Configuracion\PeducativoController@edit')
->name('administracion.configuraciones.peducativo.edit');
Route::put('/configuraciones/peducativo/{id}', 'Tab\Configuracion\PeducativoController@update')
->name('administracion.configuraciones.peducativo.update');
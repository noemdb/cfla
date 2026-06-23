<?php
Route::get('/configuraciones/seccion', 'Tab\Configuracion\SeccionController@index')
->name('administracion.configuraciones.seccion');
Route::get('/configuraciones/seccion/{id}', 'Tab\Configuracion\SeccionController@edit')
->name('administracion.configuraciones.seccion.edit');
Route::put('/configuraciones/seccion/{id}', 'Tab\Configuracion\SeccionController@update')
->name('administracion.configuraciones.seccion.update');
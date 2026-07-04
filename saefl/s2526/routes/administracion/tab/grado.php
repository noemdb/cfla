<?php
Route::get('/configuraciones/grado', 'Tab\Configuracion\GradoController@index')
->name('administracion.configuraciones.grado');
Route::get('/configuraciones/grado/{id}', 'Tab\Configuracion\GradoController@edit')
->name('administracion.configuraciones.grado.edit');
Route::put('/configuraciones/grado/{id}', 'Tab\Configuracion\GradoController@update')
->name('administracion.configuraciones.grado.update');
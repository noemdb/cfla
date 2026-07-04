<?php

Route::get('/configuraciones/grados', 'Tab\Configuracion\GradoController@index')->name('administracion.configuraciones.grados');
Route::get('/configuraciones/grados/index', 'Tab\Configuracion\GradoController@index')->name('administracion.configuraciones.grados.index');

Route::get('/configuraciones/grados/create', 'Tab\Configuracion\GradoController@create')->name('administracion.configuraciones.grados.create');
Route::post('/configuraciones/grados/store', 'Tab\Configuracion\GradoController@store')->name('administracion.configuraciones.grados.store');

Route::get('/configuraciones/grados/{id}', 'Tab\Configuracion\GradoController@edit')->name('administracion.configuraciones.grados.edit');
Route::put('/configuraciones/grados/{id}', 'Tab\Configuracion\GradoController@update')->name('administracion.configuraciones.grados.update');

Route::delete('/configuraciones/grados/destroy/{id}', 'Tab\Configuracion\GradoController@destroy')->name('administracion.configuraciones.grados.destroy');
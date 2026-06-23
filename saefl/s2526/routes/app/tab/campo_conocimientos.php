<?php
Route::get('/configuraciones/campo_conocimientos', 'Tab\Configuracion\CampoConocimientoController@index')->name('administracion.configuraciones.campo_conocimientos');
Route::get('/configuraciones/campo_conocimientos/index', 'Tab\Configuracion\CampoConocimientoController@index')->name('administracion.configuraciones.campo_conocimientos.index');

Route::get('/configuraciones/campo_conocimientos/create', 'Tab\Configuracion\CampoConocimientoController@create')->name('administracion.configuraciones.campo_conocimientos.create');
Route::post('/configuraciones/campo_conocimientos/store', 'Tab\Configuracion\CampoConocimientoController@store')->name('administracion.configuraciones.campo_conocimientos.store');

Route::get('/configuraciones/campo_conocimientos/{id}', 'Tab\Configuracion\CampoConocimientoController@edit')->name('administracion.configuraciones.campo_conocimientos.edit');
Route::put('/configuraciones/campo_conocimientos/{id}', 'Tab\Configuracion\CampoConocimientoController@update')->name('administracion.configuraciones.campo_conocimientos.update');

Route::delete('/configuraciones/campo_conocimientos/destroy/{id}', 'Tab\Configuracion\CampoConocimientoController@destroy')->name('administracion.configuraciones.campo_conocimientos.destroy');

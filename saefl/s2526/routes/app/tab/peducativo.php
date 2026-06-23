<?php
Route::get('/configuraciones/peducativos', 'Tab\Configuracion\PeducativoController@index')->name('administracion.configuraciones.peducativos');
Route::get('/configuraciones/peducativos/index', 'Tab\Configuracion\PeducativoController@index')->name('administracion.configuraciones.peducativos.index');

Route::get('/configuraciones/peducativos/create', 'Tab\Configuracion\PeducativoController@create')->name('administracion.configuraciones.peducativos.create');
Route::post('/configuraciones/peducativos/store', 'Tab\Configuracion\PeducativoController@store')->name('administracion.configuraciones.peducativos.store');

Route::get('/configuraciones/peducativos/{id}', 'Tab\Configuracion\PeducativoController@edit')->name('administracion.configuraciones.peducativos.edit');
Route::put('/configuraciones/peducativos/{id}', 'Tab\Configuracion\PeducativoController@update')->name('administracion.configuraciones.peducativos.update');

Route::delete('/configuraciones/peducativos/destroy/{id}', 'Tab\Configuracion\PeducativoController@destroy')->name('administracion.configuraciones.peducativos.destroy');
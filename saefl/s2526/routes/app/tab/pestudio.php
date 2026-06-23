<?php
Route::get('/configuraciones/pestudios', 'Tab\Configuracion\PestudioController@index')->name('administracion.configuraciones.pestudios');
Route::get('/configuraciones/pestudios/index', 'Tab\Configuracion\PestudioController@index')->name('administracion.configuraciones.pestudios.index');

Route::get('/configuraciones/pestudios/create', 'Tab\Configuracion\PestudioController@create')->name('administracion.configuraciones.pestudios.create');
Route::post('/configuraciones/pestudios/store', 'Tab\Configuracion\PestudioController@store')->name('administracion.configuraciones.pestudios.store');

Route::get('/configuraciones/pestudios/edit/{id}', 'Tab\Configuracion\PestudioController@edit')->name('administracion.configuraciones.pestudios.edit');
Route::put('/configuraciones/pestudios/update/{id}', 'Tab\Configuracion\PestudioController@update')->name('administracion.configuraciones.pestudios.update');

Route::delete('/configuraciones/pestudios/destroy/{id}', 'Tab\Configuracion\PestudioController@destroy')->name('administracion.configuraciones.pestudios.destroy');

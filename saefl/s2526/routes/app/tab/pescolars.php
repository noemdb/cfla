<?php
Route::get('/configuraciones/pescolars/index', 'Tab\Configuracion\PestudioController@index')->name('administracion.configuraciones.pescolars.index');

Route::get('/configuraciones/pescolars/create', 'Tab\Configuracion\PestudioController@create')->name('administracion.configuraciones.pescolars.create');
Route::post('/configuraciones/pescolars/store', 'Tab\Configuracion\PestudioController@store')->name('administracion.configuraciones.pescolars.store');

Route::get('/configuraciones/pescolars/edit/{id}', 'Tab\Configuracion\PestudioController@edit')->name('administracion.configuraciones.pescolars.edit');
Route::put('/configuraciones/pescolars/update/{id}', 'Tab\Configuracion\PestudioController@update')->name('administracion.configuraciones.pescolars.update');

Route::delete('/configuraciones/pescolars/destroy/{id}', 'Tab\Configuracion\PestudioController@destroy')->name('administracion.configuraciones.pescolars.destroy');

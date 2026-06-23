<?php
Route::get('/configuraciones/lapsos', 'Tab\Configuracion\LapsoController@index')->name('administracion.configuraciones.lapsos');
Route::get('/configuraciones/lapsos/index', 'Tab\Configuracion\LapsoController@index')->name('administracion.configuraciones.lapsos.index');

Route::get('/configuraciones/lapsos/create', 'Tab\Configuracion\LapsoController@create')->name('administracion.configuraciones.lapsos.create');
Route::post('/configuraciones/lapsos/store', 'Tab\Configuracion\LapsoController@store')->name('administracion.configuraciones.lapsos.store');

Route::get('/configuraciones/lapsos/{id}', 'Tab\Configuracion\LapsoController@edit')->name('administracion.configuraciones.lapsos.edit');
Route::put('/configuraciones/lapsos/{id}', 'Tab\Configuracion\LapsoController@update')->name('administracion.configuraciones.lapsos.update');

Route::delete('/configuraciones/lapsos/destroy/{id}', 'Tab\Configuracion\LapsoController@destroy')->name('administracion.configuraciones.lapsos.destroy');

Route::get('/configuraciones/lapsos/census/index/{id}', 'Tab\Configuracion\LapsoController@census')->name('administracion.configuraciones.lapsos.census.index');
Route::get('/configuraciones/lapsos/census/indicators/{id}', 'Tab\Configuracion\LapsoController@censusIndicators')->name('administracion.configuraciones.lapsos.census.indicators');

Route::get('/configuraciones/lapsos/census/edit/{id}', 'Tab\Configuracion\LapsoController@censusEdit')->name('administracion.configuraciones.lapsos.census.edit');
Route::put('/configuraciones/lapsos/census/update/{id}', 'Tab\Configuracion\LapsoController@censusUpdate')->name('administracion.configuraciones.lapsos.census.update');
Route::delete('/configuraciones/lapsos/census/destroy/{id}', 'Tab\Configuracion\LapsoController@censusDestroy')->name('administracion.configuraciones.lapsos.census.destroy');

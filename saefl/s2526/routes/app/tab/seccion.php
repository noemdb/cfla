<?php
Route::get('/configuraciones/seccions', 'Tab\Configuracion\SeccionController@index')->name('administracion.configuraciones.seccions');
Route::get('/configuraciones/seccions/index', 'Tab\Configuracion\SeccionController@index')->name('administracion.configuraciones.seccions.index');

Route::get('/configuraciones/seccions/create', 'Tab\Configuracion\SeccionController@create')->name('administracion.configuraciones.seccions.create');
Route::post('/configuraciones/seccions/store', 'Tab\Configuracion\SeccionController@store')->name('administracion.configuraciones.seccions.store');

Route::get('/configuraciones/seccions/{id}', 'Tab\Configuracion\SeccionController@edit')->name('administracion.configuraciones.seccions.edit');
Route::put('/configuraciones/seccions/{id}', 'Tab\Configuracion\SeccionController@update')->name('administracion.configuraciones.seccions.update');

Route::delete('/configuraciones/seccions/destroy/{id}', 'Tab\Configuracion\SeccionController@destroy')->name('administracion.configuraciones.seccions.destroy');

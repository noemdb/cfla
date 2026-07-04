<?php

/* resource */

Route::get('/configuraciones/profesors/index', 'Tab\Configuracion\ProfesorController@index')->name('administracion.configuraciones.profesors.index'); 

Route::get('/configuraciones/profesors/create', 'Tab\Configuracion\ProfesorController@create')->name('administracion.configuraciones.profesors.create');
Route::post('/configuraciones/profesors/store', 'Tab\Configuracion\ProfesorController@store')->name('administracion.configuraciones.profesors.store');

Route::get('/configuraciones/profesor/edit/{id}', 'Tab\Configuracion\ProfesorController@edit')->name('administracion.configuraciones.profesors.edit');
Route::put('/configuraciones/profesor/update/{id}', 'Tab\Configuracion\ProfesorController@update')->name('administracion.configuraciones.profesors.update');

Route::delete('/configuraciones/profesors/destroy/{id}', 'Tab\Configuracion\ProfesorController@destroy')->name('administracion.configuraciones.profesors.destroy');

?>
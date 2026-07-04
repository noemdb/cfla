<?php

/* resource */

Route::get('/configuraciones/oinstitucions/index', 'Tab\Configuracion\OinstitucionController@index')->name('administracion.configuraciones.oinstitucions.index');

Route::get('/configuraciones/oinstitucions/create', 'Tab\Configuracion\OinstitucionController@create')->name('administracion.configuraciones.oinstitucions.create');
Route::post('/configuraciones/oinstitucions/store', 'Tab\Configuracion\OinstitucionController@store')->name('administracion.configuraciones.oinstitucions.store');

Route::get('/configuraciones/oinstitucions/edit/{id}', 'Tab\Configuracion\OinstitucionController@edit')->name('administracion.configuraciones.oinstitucions.edit');
Route::put('/configuraciones/oinstitucions/update/{id}', 'Tab\Configuracion\OinstitucionController@update')->name('administracion.configuraciones.oinstitucions.update');

Route::delete('/configuraciones/oinstitucions/destroy/{id}', 'Tab\Configuracion\OinstitucionController@destroy')->name('administracion.configuraciones.oinstitucions.destroy');

?>

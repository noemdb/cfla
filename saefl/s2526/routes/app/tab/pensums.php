<?php

/* resource */

Route::get('/configuraciones/pensums/index', 'Tab\Configuracion\PensumController@index')->name('administracion.configuraciones.pensums.index');
Route::get('/configuraciones/pensums/crud', 'Tab\Configuracion\PensumController@crud')->name('administracion.configuraciones.pensums.crud');

Route::get('/configuraciones/pensums/create', 'Tab\Configuracion\PensumController@create')->name('administracion.configuraciones.pensums.create');
Route::post('/configuraciones/pensums/store', 'Tab\Configuracion\PensumController@store')->name('administracion.configuraciones.pensums.store');

Route::get('/configuraciones/pensum/edit/{id}', 'Tab\Configuracion\PensumController@edit')->name('administracion.configuraciones.pensums.edit');
Route::put('/configuraciones/pensum/update/{id}', 'Tab\Configuracion\PensumController@update')->name('administracion.configuraciones.pensums.update');

Route::delete('/configuraciones/pensum/destroy/{id}', 'Tab\Configuracion\PensumController@destroy')->name('administracion.configuraciones.pensums.destroy');

?>

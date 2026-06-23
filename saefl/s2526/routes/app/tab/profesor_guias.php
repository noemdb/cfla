<?php

/* resource */

Route::get('/configuraciones/profesor_guias/index', 'Tab\Configuracion\ProfesorGuiaController@index')->name('administracion.configuraciones.profesor_guias.index');
Route::get('/configuraciones/profesor_guias/asignacion', 'Tab\Configuracion\ProfesorGuiaController@asignacion')->name('administracion.configuraciones.profesor_guias.asignacion');
Route::get('/configuraciones/profesor_guias/crud', 'Tab\Configuracion\ProfesorGuiaController@crud')->name('administracion.configuraciones.profesor_guias.crud');

Route::get('/configuraciones/profesor_guias/clone/{id}', 'Tab\Configuracion\ProfesorGuiaController@clone')->name('administracion.configuraciones.profesor_guias.clone');

Route::get('/configuraciones/profesor_guias/create', 'Tab\Configuracion\ProfesorGuiaController@create')->name('administracion.configuraciones.profesor_guias.create');
Route::post('/configuraciones/profesor_guias/store', 'Tab\Configuracion\ProfesorGuiaController@store')->name('administracion.configuraciones.profesor_guias.store');

Route::get('/configuraciones/profesor_guias/edit/{id}', 'Tab\Configuracion\ProfesorGuiaController@edit')->name('administracion.configuraciones.profesor_guias.edit');
Route::put('/configuraciones/profesor_guias/update/{id}', 'Tab\Configuracion\ProfesorGuiaController@update')->name('administracion.configuraciones.profesor_guias.update');

Route::delete('/configuraciones/profesor_guias/destroy/{id}', 'Tab\Configuracion\ProfesorGuiaController@destroy')->name('administracion.configuraciones.profesor_guias.destroy');

?>

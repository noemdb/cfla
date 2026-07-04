<?php

/* resource */

Route::get('/configuraciones/asignatura/index', 'Tab\Configuracion\AsignaturaController@index')->name('administracion.configuraciones.asignaturas.index'); 

Route::get('/configuraciones/asignatura/create', 'Tab\Configuracion\AsignaturaController@create')->name('administracion.configuraciones.asignaturas.create');
Route::post('/configuraciones/asignatura/store', 'Tab\Configuracion\AsignaturaController@store')->name('administracion.configuraciones.asignaturas.store');

Route::get('/configuraciones/asignatura/edit/{id}', 'Tab\Configuracion\AsignaturaController@edit')->name('administracion.configuraciones.asignaturas.edit');
Route::put('/configuraciones/asignatura/update/{id}', 'Tab\Configuracion\AsignaturaController@update')->name('administracion.configuraciones.asignaturas.update');

Route::delete('/configuraciones/asignatura/destroy/{id}', 'Tab\Configuracion\AsignaturaController@destroy')->name('administracion.configuraciones.asignaturas.destroy');

?>
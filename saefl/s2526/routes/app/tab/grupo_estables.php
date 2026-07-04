<?php

/* resource */

Route::get('/configuraciones/grupo_estables/index', 'Tab\Configuracion\GrupoEstableController@index')->name('administracion.configuraciones.grupo_estables.index');

Route::get('/configuraciones/grupo_estables/create', 'Tab\Configuracion\GrupoEstableController@create')->name('administracion.configuraciones.grupo_estables.create');
Route::post('/configuraciones/grupo_estables/store', 'Tab\Configuracion\GrupoEstableController@store')->name('administracion.configuraciones.grupo_estables.store');

Route::get('/configuraciones/grupo_estables/edit/{id}', 'Tab\Configuracion\GrupoEstableController@edit')->name('administracion.configuraciones.grupo_estables.edit');
Route::put('/configuraciones/grupo_estables/update/{id}', 'Tab\Configuracion\GrupoEstableController@update')->name('administracion.configuraciones.grupo_estables.update');

Route::delete('/configuraciones/grupo_estables/destroy/{id}', 'Tab\Configuracion\GrupoEstableController@destroy')->name('administracion.configuraciones.grupo_estables.destroy');

?>

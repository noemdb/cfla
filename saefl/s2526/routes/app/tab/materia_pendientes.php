<?php

/* resource */

Route::get('/materia_pendientes/index', 'Tab\MateriaPendienteController@index')->name('administracion.materia_pendientes.index');

Route::get('/materia_pendientes/create/{id}', 'Tab\MateriaPendienteController@create')->name('administracion.materia_pendientes.create');

Route::post('/materia_pendientes/store', 'Tab\MateriaPendienteController@store')->name('administracion.materia_pendientes.store');

Route::get('/materia_pendientes/edit/{id}', 'Tab\MateriaPendienteController@edit')->name('administracion.materia_pendientes.edit');

Route::put('/materia_pendientes/update/{id}', 'Tab\MateriaPendienteController@update')->name('administracion.materia_pendientes.update');


?>

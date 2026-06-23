<?php

/* resource */

Route::get('/registro_titulos/index', 'Tab\RegistroTituloController@index')->name('administracion.registro_titulos.index');
Route::get('/registro_titulos/crud', 'Tab\RegistroTituloController@crud')->name('administracion.registro_titulos.crud');

Route::get('/registro_titulos/create_edit', 'Tab\RegistroTituloController@create_edit')->name('administracion.registro_titulos.create_edit');

Route::get('/registro_titulos/create', 'Tab\RegistroTituloController@create')->name('administracion.registro_titulos.create');
Route::post('/registro_titulos/store', 'Tab\RegistroTituloController@store')->name('administracion.registro_titulos.store');

Route::get('/registro_titulos/show/{id}', 'Tab\RegistroTituloController@show')->name('administracion.registro_titulos.show');

Route::get('/registro_titulos/edit/{id}', 'Tab\RegistroTituloController@edit')->name('administracion.registro_titulos.edit');
Route::put('/registro_titulos/update/{id}', 'Tab\RegistroTituloController@update')->name('administracion.registro_titulos.update');

Route::delete('/registro_titulos/destroy/{id}', 'Tab\RegistroTituloController@destroy')->name('administracion.registro_titulos.destroy');

// Route::get('/registro_titulos/create_clone/{id}', 'Tab\RegistroTituloController@create_clone')->name('administracion.registro_titulos.create_clone');
// Route::post('/registro_titulos/store_clone', 'Tab\RegistroTituloController@store_clone')->name('administracion.registro_titulos.store_clone');

?>

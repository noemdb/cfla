<?php

/* resource */

Route::get('/titulos/index', 'Tab\TituloController@index')->name('administracion.titulos.index');
Route::get('/titulos/crud', 'Tab\TituloController@crud')->name('administracion.titulos.crud');

Route::get('/titulos/create', 'Tab\TituloController@create')->name('administracion.titulos.create');
Route::post('/titulos/store', 'Tab\TituloController@store')->name('administracion.titulos.store');
Route::post('/titulos/aprove', 'Tab\TituloController@aprove')->name('administracion.titulos.aprove');

Route::get('/titulos/show/{id}', 'Tab\TituloController@show')->name('administracion.titulos.show');

Route::get('/titulos/edit/{id}', 'Tab\TituloController@edit')->name('administracion.titulos.edit');
Route::put('/titulos/update/{id}', 'Tab\TituloController@update')->name('administracion.titulos.update');

Route::delete('/titulos/destroy/{id}', 'Tab\TituloController@destroy')->name('administracion.titulos.destroy');


?>

<?php

/* resource */

Route::get('/ecualitativas/index', 'Tab\EcualitativaController@index')->name('administracion.ecualitativas.index');
Route::get('/ecualitativas/crud', 'Tab\EcualitativaController@crud')->name('administracion.ecualitativas.crud');

Route::get('/ecualitativas/create', 'Tab\Configuracion\DescuentoController@create')->name('administracion.ecualitativas.create');
Route::post('/ecualitativas/store', 'Tab\EcualitativaController@store')->name('administracion.ecualitativas.store');

Route::get('/boletin/edit/{id}', 'Tab\EcualitativaController@edit')->name('administracion.ecualitativas.edit');
Route::put('/boletin/update/{id}', 'Tab\EcualitativaController@update')->name('administracion.ecualitativas.update');

Route::delete('/boletin/destroy/{id}', 'Tab\EcualitativaController@destroy')->name('administracion.ecualitativas.destroy');

?>

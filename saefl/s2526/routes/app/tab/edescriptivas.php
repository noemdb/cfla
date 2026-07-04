<?php

/* resource */

Route::get('/edescriptivas/index', 'Tab\EDescriptivaController@index')->name('administracion.edescriptivas.index');
Route::get('/edescriptivas/crud', 'Tab\EDescriptivaController@crud')->name('administracion.edescriptivas.crud');

Route::get('/edescriptivas/create', 'Tab\Configuracion\DescuentoController@create')->name('administracion.edescriptivas.create');
Route::post('/edescriptivas/store', 'Tab\EDescriptivaController@store')->name('administracion.edescriptivas.store');

Route::get('/edescriptivas/edit/{id}', 'Tab\EDescriptivaController@edit')->name('administracion.edescriptivas.edit');
Route::put('/edescriptivas/update/{id}', 'Tab\EDescriptivaController@update')->name('administracion.edescriptivas.update');

Route::delete('/edescriptivas/destroy/{id}', 'Tab\EDescriptivaController@destroy')->name('administracion.edescriptivas.destroy');

?>

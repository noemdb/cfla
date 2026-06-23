<?php

/* resource */

Route::get('/preinscripcions/index', 'Tab\PreinscripcionController@index')->name('administracion.preinscripcions.index');
Route::get('/preinscripcions/crud', 'Tab\PreinscripcionController@crud')->name('administracion.preinscripcions.crud');
Route::get('/preinscripcions/book', 'Tab\PreinscripcionController@book')->name('administracion.preinscripcions.book');

Route::get('/preinscripcions/validations', 'Tab\PreinscripcionController@validations')->name('administracion.preinscripcions.validations');

Route::get('/preinscripcions/carga/csv/', 'Tab\PreinscripcionController@cargaCSV')->name('administracion.preinscripcions.carga.csv');
Route::post('/preinscripcions/carga/csv/', 'Tab\PreinscripcionController@cargaCSVPost')->name('administracion.preinscripcions.carga.csv.post');
Route::post('/preinscripcions/store/csv', 'Tab\PreinscripcionController@storeCSV')->name('administracion.preinscripcions.store.csv');

Route::get('/preinscripcions/edit/{id}', 'Tab\PreinscripcionController@edit')->name('administracion.preinscripcions.edit');
Route::put('/preinscripcions/update/{id}', 'Tab\PreinscripcionController@update')->name('administracion.preinscripcions.update');

Route::get('/preinscripcions/create', 'Tab\PreinscripcionController@create')->name('administracion.preinscripcions.create');
Route::post('/preinscripcions/store', 'Tab\PreinscripcionController@store')->name('administracion.preinscripcions.store');

Route::delete('/preinscripcions/destroy/{id}', 'Tab\PreinscripcionController@destroy')->name('administracion.preinscripcions.destroy');

//ajax


?>

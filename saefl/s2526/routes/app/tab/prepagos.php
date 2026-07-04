<?php

/* resource */

Route::get('/prepagos/index', 'Tab\PrepagoController@index')->name('administracion.prepagos.index');

Route::get('/prepagos/create', 'Tab\PrepagoController@create')->name('administracion.prepagos.create');
Route::post('/prepagos/store', 'Tab\PrepagoController@store')->name('administracion.prepagos.store');

//1er Formulario de google
Route::get('/prepagos/carga/csv/', 'Tab\PrepagoController@cargaCSV')->name('administracion.prepagos.carga.csv');
Route::post('/prepagos/carga/csv/', 'Tab\PrepagoController@cargaCSVPost')->name('administracion.prepagos.carga.csv.post');
Route::post('/prepagos/store/csv', 'Tab\PrepagoController@storeCSV')->name('administracion.prepagos.store.csv');

//2do Formulario de google preinscripcions
Route::get('/prepagos/preinscripcions/carga/csv/', 'Tab\PrepagoController@PreinscripcionCargaCSV')->name('administracion.prepagos.preinscripcions.carga.csv');
Route::post('/prepagos/preinscripcions/carga/csv/', 'Tab\PrepagoController@PreinscripcionCargaCSVPost')->name('administracion.prepagos.preinscripcions.carga.csv.post');
Route::post('/prepagos/preinscripcions/store/csv', 'Tab\PrepagoController@PreinscripcionStoreCSV')->name('administracion.prepagos.preinscripcions.store.csv');

Route::get('/prepagos/validations', 'Tab\PrepagoController@validations')->name('administracion.prepagos.validations');
Route::get('/prepagos/crud', 'Tab\PrepagoController@crud')->name('administracion.prepagos.crud');
Route::get('/prepagos/associated', 'Tab\PrepagoController@associated')->name('administracion.prepagos.associated');

Route::get('/prepagos/edit/{id}', 'Tab\PrepagoController@edit')->name('administracion.prepagos.edit');
Route::put('/prepagos/update/{id}', 'Tab\PrepagoController@update')->name('administracion.prepagos.update');

Route::get('/prepagos/book', 'Tab\PrepagoController@book')->name('administracion.prepagos.book');

// Route::get('/prepagos/create/{id}/{ctaid}', 'Tab\PrepagoController@create')->name('administracion.prepagos.create');
Route::post('/prepagos/abono/store', 'Tab\PrepagoController@storeAbono')->name('administracion.prepagos.abono.store');
Route::post('/prepagos/pago/store', 'Tab\PrepagoController@storePago')->name('administracion.prepagos.pago.store');

// Route::get('/prepagos/create', 'Tab\Configuracion\ProfesorGuiaController@create')->name('administracion.configuraciones.profesor_guias.create');
// Route::post('/prepagos/store', 'Tab\Configuracion\ProfesorGuiaController@store')->name('administracion.configuraciones.profesor_guias.store');


Route::delete('/prepagos/destroy/{id}', 'Tab\PrepagoController@destroy')->name('administracion.prepagos.destroy');

//ajax


?>

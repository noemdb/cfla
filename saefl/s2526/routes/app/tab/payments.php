<?php

/* resource */

Route::get('/payments/index', 'Tab\PaymentController@index')->name('administracion.payments.index');
Route::get('/payments/crud', 'Tab\PaymentController@crud')->name('administracion.payments.crud');
Route::get('/payments/inscriptions', 'Tab\PaymentController@inscriptions')->name('administracion.payments.inscriptions');
Route::get('/payments/charts', 'Tab\PaymentController@charts')->name('administracion.payments.charts');

Route::get('/payments/create', 'Tab\PaymentController@create')->name('administracion.payments.create');
Route::post('/payments/store', 'Tab\PaymentController@store')->name('administracion.payments.store');

//1er Formulario de google
Route::get('/payments/carga/csv/', 'Tab\PaymentController@cargaCSV')->name('administracion.payments.carga.csv');
Route::post('/payments/carga/csv/', 'Tab\PaymentController@cargaCSVPost')->name('administracion.payments.carga.csv.post');
Route::post('/payments/store/csv', 'Tab\PaymentController@storeCSV')->name('administracion.payments.store.csv');

//2do Formulario de google preinscripcions
Route::get('/payments/preinscripcions/carga/csv/', 'Tab\PaymentController@PreinscripcionCargaCSV')->name('administracion.payments.preinscripcions.carga.csv');
Route::post('/payments/preinscripcions/carga/csv/', 'Tab\PaymentController@PreinscripcionCargaCSVPost')->name('administracion.payments.preinscripcions.carga.csv.post');
Route::post('/payments/preinscripcions/store/csv', 'Tab\PaymentController@PreinscripcionStoreCSV')->name('administracion.payments.preinscripcions.store.csv');

Route::get('/payments/validations', 'Tab\PaymentController@validations')->name('administracion.payments.validations');
// Route::get('/payments/crud', 'Tab\PaymentController@crud')->name('administracion.payments.crud');
Route::get('/payments/associated', 'Tab\PaymentController@associated')->name('administracion.payments.associated');

Route::get('/payments/edit/{id}', 'Tab\PaymentController@edit')->name('administracion.payments.edit');
Route::put('/payments/update/{id}', 'Tab\PaymentController@update')->name('administracion.payments.update');

Route::get('/payments/book', 'Tab\PaymentController@book')->name('administracion.payments.book');

// Route::get('/payments/create/{id}/{ctaid}', 'Tab\PaymentController@create')->name('administracion.payments.create');
Route::post('/payments/abono/store', 'Tab\PaymentController@storeAbono')->name('administracion.payments.abono.store');
Route::post('/payments/pago/store', 'Tab\PaymentController@storePago')->name('administracion.payments.pago.store');

// Route::get('/payments/create', 'Tab\Configuracion\ProfesorGuiaController@create')->name('administracion.configuraciones.profesor_guias.create');
// Route::post('/payments/store', 'Tab\Configuracion\ProfesorGuiaController@store')->name('administracion.configuraciones.profesor_guias.store');


Route::delete('/payments/destroy/{id}', 'Tab\PaymentController@destroy')->name('administracion.payments.destroy');



//ajax


?>

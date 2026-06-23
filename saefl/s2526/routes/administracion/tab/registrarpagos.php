<?php

/* resource */
Route::get('/registropagos/dashboard', 'Tab\RegistroPagosController@dashboard')->name('administracion.registropagos.dashboard');

Route::get('/registropagos/individual', 'Tab\RegistroPagosController@individual')->name('administracion.registropagos.individual');

Route::get('/registropagos/list/view', 'Tab\RegistroPagosController@listview')->name('administracion.registropagos.list.view');

Route::get('/registropagos/index', 'Tab\RegistroPagosController@index')->name('administracion.registropagos.index');

Route::get('/registropagos/book', 'Tab\RegistroPagosController@book')->name('administracion.registropagos.book');

Route::get('/registropagos/create/{id}/{ctaid}', 'Tab\RegistroPagosController@create')->name('administracion.registropagos.create');

Route::post('/registropagos/store', 'Tab\RegistroPagosController@store')->name('administracion.registropagos.store');

Route::get('/registropagos/edit/{id}', 'Tab\RegistroPagosController@edit')->name('administracion.registropagos.edit');

Route::get('/registropagos/show/{id}', 'Tab\RegistroPagosController@show')->name('administracion.registropagos.show');

Route::put('/registropagos/update/{id}', 'Tab\RegistroPagosController@update')->name('administracion.registropagos.update');

Route::get('/registropagos/batchs', 'Tab\RegistroPagosController@batchs')->name('administracion.registropagos.batchs');

//ajax
Route::get('/registropagos/create/gradoByseccion/{id}', 'Tab\RegistroPagosController@gradoByseccion');

Route::get('/registropagos/edit/gradoByseccion/{id}', 'Tab\RegistroPagosController@gradoByseccion');


?>
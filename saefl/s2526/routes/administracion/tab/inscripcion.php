<?php

/* resource */
Route::get('/inscripciones/dashboard', 'Tab\InscripcionController@dashboard')->name('administracion.inscripciones.dashboard');

Route::get('/inscripciones/individual', 'Tab\InscripcionController@individual')->name('administracion.inscripciones.individual');

Route::get('/inscripciones/list/view', 'Tab\InscripcionController@listview')->name('administracion.inscripciones.list.view');

Route::get('/inscripciones/index', 'Tab\InscripcionController@index')->name('administracion.inscripciones.index');

Route::get('/inscripciones/book', 'Tab\InscripcionController@book')->name('administracion.inscripciones.book');

Route::get('/inscripciones', 'Tab\InscripcionController@edit')->name('administracion.inscripciones.inscripcionedit');

Route::get('/inscripciones/create/{id}', 'Tab\InscripcionController@create')->name('administracion.inscripciones.create');

Route::post('/inscripciones/store', 'Tab\InscripcionController@store')->name('administracion.inscripciones.store');

Route::get('/inscripciones/edit/{id}', 'Tab\InscripcionController@edit')->name('administracion.inscripciones.edit');

Route::put('/inscripciones/update/{id}', 'Tab\InscripcionController@update')->name('administracion.inscripciones.update');

Route::get('/inscripciones/batchs', 'Tab\InscripcionController@batchs')->name('administracion.inscripciones.batchs');

//ajax
Route::get('/inscripciones/create/gradoByseccion/{id}', 'Tab\InscripcionController@gradoByseccion');
Route::get('/inscripciones/edit/gradoByseccion/{id}', 'Tab\InscripcionController@gradoByseccion');


?>
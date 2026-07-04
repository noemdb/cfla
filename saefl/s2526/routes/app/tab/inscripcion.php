<?php

/* resource */
Route::get('/inscripciones/dashboard', 'Tab\InscripcionController@dashboard')->name('administracion.inscripciones.dashboard');


Route::get('/inscripciones/asignar', 'Tab\InscripcionController@asignar')->name('administracion.inscripciones.asignar');
Route::post('/inscripciones/asignarStore', 'Tab\InscripcionController@asignarStore')->name('administracion.inscripciones.asignarStore');


Route::get('/inscripciones/crud', 'Tab\InscripcionController@crud')->name('administracion.inscripciones.crud');
Route::get('/inscripciones/movement', 'Tab\InscripcionController@movement')->name('administracion.inscripciones.movement');

Route::get('/inscripciones/prosecucion', 'Tab\InscripcionController@prosecucion')->name('administracion.inscripciones.prosecucion');

Route::get('/inscripciones/individual', 'Tab\InscripcionController@individual')->name('administracion.inscripciones.individual');

Route::get('/inscripciones/book', 'Tab\InscripcionController@book')->name('administracion.inscripciones.book');

Route::get('/inscripciones/index', 'Tab\InscripcionController@index')->name('administracion.inscripciones.index');

Route::get('/inscripciones/retiro', 'Tab\InscripcionController@retiro')->name('administracion.inscripciones.retiro');

Route::get('/inscripciones', 'Tab\InscripcionController@edit')->name('administracion.inscripciones.inscripcionedit');

Route::get('/inscripciones/create/{id}', 'Tab\InscripcionController@create')->name('administracion.inscripciones.create');

Route::post('/inscripciones/store', 'Tab\InscripcionController@store')->name('administracion.inscripciones.store');

Route::get('/inscripciones/edit/{id}', 'Tab\InscripcionController@edit')->name('administracion.inscripciones.edit');

Route::put('/inscripciones/update/{id}', 'Tab\InscripcionController@update')->name('administracion.inscripciones.update');

Route::get('/inscripciones/batchs', 'Tab\InscripcionController@batchs')->name('administracion.inscripciones.batchs');
Route::get('/inscripciones/unregistered', 'Tab\InscripcionController@unregistered')->name('administracion.inscripciones.unregistered');

Route::get('/inscripciones/register', 'Tab\InscripcionController@register')->name('administracion.inscripciones.register');

Route::delete('/inscripciones/destroy/{id}', 'Tab\InscripcionController@destroy')->name('administracion.inscripciones.destroy');


//formatos
Route::get('/inscripciones/list/view', 'Tab\InscripcionController@listview')->name('administracion.inscripciones.list.view');
Route::get('/inscripciones/list/matricula/inicial', 'Tab\InscripcionController@matricula_inicial')->name('administracion.inscripciones.matricula.inicial');

//ajax
// Route::get('/inscripciones/create/gradoByseccion/{id}', 'Tab\InscripcionController@gradoByseccion');
// Route::get('/inscripciones/edit/gradoByseccion/{id}', 'Tab\InscripcionController@gradoByseccion');


?>

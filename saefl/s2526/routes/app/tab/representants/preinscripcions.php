<?php

/* resource */

Route::get('/preinscripcions/index', 'Tab\PreinscripcionController@index')->name('representants.preinscripcions.index');

Route::get('/preinscripcions/crud', 'Tab\PreinscripcionController@crud')->name('representants.preinscripcions.crud');

Route::get('/preinscripcions/edit/{id}', 'Tab\PreinscripcionController@edit')->name('representants.preinscripcions.edit');
Route::put('/preinscripcions/update/{id}', 'Tab\PreinscripcionController@update')->name('representants.preinscripcions.update');

Route::get('/preinscripcions/book', 'Tab\PreinscripcionController@book')->name('representants.preinscripcions.book');

Route::get('/preinscripcions/create', 'Tab\PreinscripcionController@create')->name('representants.preinscripcions.create');
Route::post('/preinscripcions/store', 'Tab\PreinscripcionController@store')->name('representants.preinscripcions.store');

Route::delete('/preinscripcions/destroy/{id}', 'Tab\PreinscripcionController@destroy')->name('representants.preinscripcions.destroy');

//ajax


?>

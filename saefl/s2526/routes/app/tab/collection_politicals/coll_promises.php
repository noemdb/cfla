<?php

/* coll_promises */
Route::get('/coll_promises/index', 'Tab\Collection\CollPromiseController@index')->name('administracion.collections.coll_promises.index');

Route::get('/coll_promises/asistent', 'Tab\Collection\CollPromiseController@asistent')->name('administracion.collections.coll_promises.asistent');
Route::post('/coll_promises/ajax/load/representant', 'Tab\Collection\CollPromiseController@loadRepresentant')->name('administracion.collections.coll_promises.load.representant');
Route::post('/coll_promises/ajax/preview', 'Tab\Collection\CollPromiseController@preview')->name('administracion.collections.coll_promises.preview');
Route::post('/coll_promises/asistent/store', 'Tab\Collection\CollPromiseController@asistentStore')->name('administracion.collections.coll_promises.asistent.store');

Route::get('/coll_promises/crud/', 'Tab\Collection\CollPromiseController@crud')->name('administracion.collections.coll_promises.crud');
Route::get('/coll_promises/create', 'Tab\Collection\CollPromiseController@create')->name('administracion.collections.coll_promises.create');
Route::post('/coll_promises/store', 'Tab\Collection\CollPromiseController@store')->name('administracion.collections.coll_promises.store');
Route::get('/coll_promises/edit/{id}', 'Tab\Collection\CollPromiseController@edit')->name('administracion.collections.coll_promises.edit');
Route::put('/coll_promises/update/{id}', 'Tab\Collection\CollPromiseController@update')->name('administracion.collections.coll_promises.update');
Route::delete('/coll_promises/destroy/{id}', 'Tab\Collection\CollPromiseController@destroy')->name('administracion.collections.coll_promises.destroy');



?>

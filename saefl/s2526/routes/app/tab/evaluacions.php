<?php

/* resource */

Route::get('/evaluacions/index', 'Tab\EvaluacionController@index')->name('administracion.evaluacions.index');
Route::get('/evaluacions/crud', 'Tab\EvaluacionController@crud')->name('administracion.evaluacions.crud');

Route::get('/evaluacions/create/{id}', 'Tab\EvaluacionController@create')->name('administracion.evaluacions.create');
Route::post('/evaluacions/store_pevaluacions', 'Tab\EvaluacionController@store_pevaluacions')->name('administracion.evaluacions.store_pevaluacions');
Route::post('/evaluacions/store', 'Tab\EvaluacionController@store')->name('administracion.evaluacions.store');

Route::get('/evaluacions/edit/{id}', 'Tab\EvaluacionController@edit')->name('administracion.evaluacions.edit');
Route::put('/evaluacions/update/{id}', 'Tab\EvaluacionController@update')->name('administracion.evaluacions.update');

Route::delete('/evaluacions/destroy/{id}', 'Tab\EvaluacionController@destroy')->name('administracion.evaluacions.destroy');

Route::get('/evaluacions/create_clone/{id}', 'Tab\EvaluacionController@create_clone')->name('administracion.evaluacions.create_clone');
Route::post('/evaluacions/store_clone', 'Tab\EvaluacionController@store_clone')->name('administracion.evaluacions.store_clone');


?>

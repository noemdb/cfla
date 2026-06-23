<?php

/* resource */

Route::get('/evaluacions/index', 'Tab\EvaluacionController@index')->name('profesors.evaluacions.index');
Route::get('/evaluacions/crud', 'Tab\EvaluacionController@crud')->name('profesors.evaluacions.crud');

Route::get('/evaluacions/create/{id}', 'Tab\EvaluacionController@create')->name('profesors.evaluacions.create');
Route::post('/evaluacions/store', 'Tab\EvaluacionController@store')->name('profesors.evaluacions.store');

Route::get('/evaluacions/edit/{id}', 'Tab\EvaluacionController@edit')->name('profesors.evaluacions.edit');
Route::put('/evaluacions/update/{id}', 'Tab\EvaluacionController@update')->name('profesors.evaluacions.update');

Route::delete('/evaluacions/destroy/{id}', 'Tab\EvaluacionController@destroy')->name('profesors.evaluacions.destroy');

Route::get('/evaluacions/create_clone/{id}', 'Tab\EvaluacionController@create_clone')->name('profesors.evaluacions.create_clone');
Route::post('/evaluacions/store_clone', 'Tab\EvaluacionController@store_clone')->name('profesors.evaluacions.store_clone');


?>

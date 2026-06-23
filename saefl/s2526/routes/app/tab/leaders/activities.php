<?php

/* resource */

Route::get('/activities/index', 'Tab\ActivityController@index')->name('leaders.activities.index');
Route::get('/activities/format/{id}', 'Tab\ActivityController@format')->name('leaders.activities.format');
Route::get('/activities/resume/{id}', 'Tab\ActivityController@resume')->name('leaders.activities.resume');
Route::get('/activities/crud', 'Tab\ActivityController@crud')->name('leaders.activities.crud');

Route::get('/activities/create/{id}', 'Tab\ActivityController@create')->name('leaders.activities.create');
Route::post('/activities/store', 'Tab\ActivityController@store')->name('leaders.activities.store');

Route::get('/activities/edit/{id}', 'Tab\ActivityController@edit')->name('leaders.activities.edit');
Route::put('/activities/update/{id}', 'Tab\ActivityController@update')->name('leaders.activities.update');

Route::delete('/activities/destroy/{id}', 'Tab\ActivityController@destroy')->name('leaders.activities.destroy');

Route::get('/activities/create_clone/{id}', 'Tab\ActivityController@create_clone')->name('leaders.activities.create_clone');
Route::post('/activities/store_clone', 'Tab\ActivityController@store_clone')->name('leaders.activities.store_clone');


?>

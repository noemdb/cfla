<?php

/* resource */

Route::get('/activities/index', 'Tab\ActivityController@index')->name('profesors.activities.index');
Route::get('/activities/format/{id}', 'Tab\ActivityController@format')->name('profesors.activities.format');
Route::get('/activities/resume/{id}', 'Tab\ActivityController@resume')->name('profesors.activities.resume');
Route::get('/activities/crud', 'Tab\ActivityController@crud')->name('profesors.activities.crud');

Route::get('/activities/create/{id}', 'Tab\ActivityController@create')->name('profesors.activities.create');
Route::post('/activities/store', 'Tab\ActivityController@store')->name('profesors.activities.store');

Route::get('/activities/edit/{id}', 'Tab\ActivityController@edit')->name('profesors.activities.edit');
Route::put('/activities/update/{id}', 'Tab\ActivityController@update')->name('profesors.activities.update');

Route::delete('/activities/destroy/{id}', 'Tab\ActivityController@destroy')->name('profesors.activities.destroy');

Route::get('/activities/clone/{id}', 'Tab\ActivityController@clone')->name('profesors.activities.clone');
Route::post('/activities/store_clone', 'Tab\ActivityController@store_clone')->name('profesors.activities.store_clone');


?>

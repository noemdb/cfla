<?php

/* coll_activities */
Route::get('/coll_activities/index', 'Tab\Collection\CollActivityController@index')->name('administracion.collections.coll_activities.index');
Route::get('/coll_activities/generate', 'Tab\Collection\CollActivityController@generate')->name('administracion.collections.coll_activities.generate');
Route::get('/coll_activities/crud/', 'Tab\Collection\CollActivityController@crud')->name('administracion.collections.coll_activities.crud');
Route::get('/coll_activities/create', 'Tab\Collection\CollActivityController@create')->name('administracion.collections.coll_activities.create');
Route::post('/coll_activities/store', 'Tab\Collection\CollActivityController@store')->name('administracion.collections.coll_activities.store');
Route::get('/coll_activities/edit/{id}', 'Tab\Collection\CollActivityController@edit')->name('administracion.collections.coll_activities.edit');
Route::put('/coll_activities/update/{id}', 'Tab\Collection\CollActivityController@update')->name('administracion.collections.coll_activities.update');
Route::delete('/coll_activities/destroy/{id}', 'Tab\Collection\CollActivityController@destroy')->name('administracion.collections.coll_activities.destroy');


?>

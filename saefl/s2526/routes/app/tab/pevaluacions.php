<?php

/* resource */

Route::get('/pevaluacions/index', 'Tab\PevaluacionController@index')->name('administracion.pevaluacions.index'); 
Route::get('/pevaluacions/carga', 'Tab\PevaluacionController@carga')->name('administracion.pevaluacions.carga'); 
Route::get('/pevaluacions/crud', 'Tab\PevaluacionController@crud')->name('administracion.pevaluacions.crud'); 

Route::get('/pevaluacions/create/{grado_id}/{seccion_id}/{pensum_id}/{lapso_id}', 'Tab\PevaluacionController@create')->name('administracion.pevaluacions.create');
Route::post('/pevaluacions/store', 'Tab\PevaluacionController@store')->name('administracion.pevaluacions.store');

Route::get('/pevaluacions/edit/{id}', 'Tab\PevaluacionController@edit')->name('administracion.pevaluacions.edit');
Route::put('/pevaluacions/update/{id}', 'Tab\PevaluacionController@update')->name('administracion.pevaluacions.update');

Route::delete('/pevaluacions/destroy/{id}', 'Tab\PevaluacionController@destroy')->name('administracion.pevaluacions.destroy');

Route::get('/pevaluacions/create_clone/{id}', 'Tab\PevaluacionController@create_clone')->name('administracion.pevaluacions.create_clone');
Route::post('/pevaluacions/store_clone', 'Tab\PevaluacionController@store_clone')->name('administracion.pevaluacions.store_clone');

?>
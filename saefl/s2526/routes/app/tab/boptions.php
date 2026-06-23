<?php

// Route::get('/boptions/dashboard', 'Bot\BoptionController@dashboard')->name('administracion.autoresponders.boptions.dashboard');

Route::get('/autoresponders/boptions/index', 'Bot\BoptionController@index')->name('administracion.autoresponders.boptions.index');

Route::get('/autoresponders/boptions/create', 'Bot\BoptionController@create')->name('administracion.autoresponders.boptions.create');
Route::post('/autoresponders/boptions/store', 'Bot\BoptionController@store')->name('administracion.autoresponders.boptions.store');

Route::get('/autoresponders/boptions/edit/{id}', 'Bot\BoptionController@edit')->name('administracion.autoresponders.boptions.edit');
Route::put('/autoresponders/boptions/update/{id}', 'Bot\BoptionController@update')->name('administracion.autoresponders.boptions.update');

Route::delete('/autoresponders/boptions/destroy/{id}', 'Bot\BoptionController@destroy')->name('administracion.autoresponders.boptions.destroy');

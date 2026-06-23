<?php

Route::get('/estudiants/dashboard', 'Tab\EstudiantesController@dashboard')->name('administracion.estudiants.dashboard');

// Route::resource('tab/estudiants/','Tab\EstudiantesController');
// Route::resource('estudiants','Tab\EstudiantesController');
Route::get('/estudiants/index', 'Tab\EstudiantesController@index')->name('administracion.estudiants.index');

Route::get('/estudiants/create', 'Tab\EstudiantesController@create')->name('administracion.estudiants.create');
Route::post('/estudiants/store', 'Tab\EstudiantesController@store')->name('administracion.estudiants.store');

Route::get('/estudiants/edit/{id}', 'Tab\EstudiantesController@edit')->name('administracion.estudiants.edit');
Route::put('/estudiants/update/{id}', 'Tab\EstudiantesController@update')->name('administracion.estudiants.update');
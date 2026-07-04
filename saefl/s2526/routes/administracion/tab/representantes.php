<?php

Route::get('/representants/dashboard', 'Tab\RepresentantsController@dashboard')->name('administracion.representants.dashboard');
Route::get('/representants/index', 'Tab\RepresentantsController@index')->name('administracion.representants.index');

Route::get('/representants/create','Tab\RepresentantsController@create')->name('administracion.representants.create');
Route::post('/representants/store','Tab\RepresentantsController@store')->name('administracion.representants.store');

Route::get('/representants/edit/{id}','Tab\RepresentantsController@edit')->name('administracion.representants.edit');
Route::put('/representants/update/{id}','Tab\RepresentantsController@update')->name('administracion.representants.update');


?>
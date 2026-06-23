<?php

/* resource */
Route::get('/boletin_revisions/index', 'Tab\BoletinRevisionController@index')->name('administracion.boletin_revisions.index');
Route::get('/boletin_revisions/crud', 'Tab\BoletinRevisionController@crud')->name('administracion.boletin_revisions.crud');
Route::get('/boletin_revisions/resumen_revision/', 'Tab\BoletinRevisionController@resumen_revision')->name('administracion.boletins.resumen_revision');

// Route::get('/boletin_revisions/create/{id}', 'Tab\BoletinRevisionController@create')->name('administracion.boletin_revisions.create');
// Route::post('/boletin_revisions/store', 'Tab\BoletinRevisionController@store')->name('administracion.boletin_revisions.store');

// Route::get('/boletin_revisions/edit/{id}', 'Tab\BoletinRevisionController@edit')->name('administracion.boletin_revisions.edit');
// Route::put('/boletin_revisions/update/{id}', 'Tab\BoletinRevisionController@update')->name('administracion.boletin_revisions.update');

// Route::delete('/boletin_revisions/destroy/{id}', 'Tab\BoletinRevisionController@destroy')->name('administracion.boletin_revisions.destroy');

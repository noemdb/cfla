<?php

/* resource */

Route::get('/abonos/index', 'Tab\AbonoController@index')->name('administracion.abonos.index');
Route::get('/abonos/create/{id}', 'Tab\AbonoController@create')->name('administracion.abonos.create');
Route::post('/abonos/store', 'Tab\AbonoController@store')->name('administracion.abonos.store');
Route::delete('/abonos/destroy/{id}', 'Tab\AbonoController@destroy')->name('administracion.abonos.destroy');

Route::get('/abonos/crud', 'Tab\AbonoController@crud')->name('administracion.abonos.crud');

Route::get('/abonos/edit/{id}', 'Tab\AbonoController@edit')->name('administracion.abonos.edit');
Route::put('/abonos/update/{id}', 'Tab\AbonoController@update')->name('administracion.abonos.update');

?>

<?php

/* resource */

Route::get('/creditoafavors/crud', 'Tab\CreditoAFavorController@crud')->name('administracion.creditoafavors.crud');

Route::get('/creditoafavors/omit', 'Tab\CreditoAFavorController@omit')->name('administracion.creditoafavors.omit');
Route::put('/creditoafavors/set/omit/{id}', 'Tab\CreditoAFavorController@setOmit')->name('administracion.creditoafavors.set.omit');
Route::get('/creditoafavors/set/ajax/omit/{id}', 'Tab\CreditoAFavorController@setAjaxOmit')->name('administracion.creditoafavors.set.ajax.omit');

Route::delete('/creditoafavors/destroy/{id}', 'Tab\CreditoAFavorController@destroy')->name('administracion.creditoafavors.destroy');

Route::get('/creditoafavors/edit/{id}', 'Tab\CreditoAFavorController@edit')->name('administracion.creditoafavors.edit');
Route::put('/creditoafavors/update/{id}', 'Tab\CreditoAFavorController@update')->name('administracion.creditoafavors.update');

/*
Route::get('/abonos/dashboard', 'Tab\AbonoController@dashboard')->name('administracion.abonos.dashboard');

Route::get('/abonos/individual', 'Tab\AbonoController@individual')->name('administracion.abonos.individual');

Route::get('/abonos/list/view', 'Tab\AbonoController@listview')->name('administracion.abonos.list.view');

Route::get('/abonos/book', 'Tab\AbonoController@book')->name('administracion.abonos.book');

Route::post('/abonos/store', 'Tab\AbonoController@store')->name('administracion.abonos.store');

Route::get('/abonos/edit/{id}', 'Tab\AbonoController@edit')->name('administracion.abonos.edit');

Route::get('/abonos/show/{id}', 'Tab\AbonoController@show')->name('administracion.abonos.show');

Route::put('/abonos/update/{id}', 'Tab\AbonoController@update')->name('administracion.abonos.update');

Route::get('/abonos/batchs', 'Tab\AbonoController@batchs')->name('administracion.abonos.batchs');

//ajax
Route::get('/abonos/create/gradoByseccion/{id}', 'Tab\AbonoController@gradoByseccion');

Route::get('/abonos/edit/gradoByseccion/{id}', 'Tab\AbonoController@gradoByseccion');

*/
?>

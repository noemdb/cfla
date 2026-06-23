<?php

/* resource */

Route::get('/libro/banco', 'Tab\Configuracion\BancoController@libro')->name('administracion.libro.banco');
Route::get('/libro/banco/libro/abonos', 'Tab\Configuracion\BancoController@libroAbonos')->name('administracion.libro.banco.libro.abonos');

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

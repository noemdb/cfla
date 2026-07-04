<?php

/* resource */

Route::get('/receibts/index', 'Tab\Receibt\ReciboController@index')->name('administracion.receibts.recibos.index');
Route::get('/receibts/crud', 'Tab\Receibt\ReciboController@crud')->name('administracion.receibts.recibos.crud');
Route::post('/receibts/store', 'Tab\Receibt\ReciboController@store')->name('administracion.receibts.store');
// Route::get('/receibts/create/{id}', 'Tab\Receibt\ReciboController@create')->name('administracion.receibts.create');
// Route::delete('/receibts/destroy/{id}', 'Tab\Receibt\ReciboController@destroy')->name('administracion.receibts.destroy');

// Route::get('/receibts/crud', 'Tab\ReciboController@crud')->name('administracion.receibts.crud');

?>

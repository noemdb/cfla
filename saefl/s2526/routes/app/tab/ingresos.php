<?php

/* resource */

Route::get('/ingresos/crud', 'Tab\IngresoController@crud')->name('administracion.ingresos.crud');
Route::get('/ingresos/edit/{id}', 'Tab\IngresoController@edit')->name('administracion.ingresos.edit');
Route::put('/ingresos/update/{id}', 'Tab\IngresoController@update')->name('administracion.ingresos.update');
Route::delete('/ingresos/ingresos/{id}', 'Tab\IngresoController@destroy')->name('administracion.ingresos.destroy');

?>
<?php

/* resource */

Route::get('/refunds/index', 'Tab\RefundController@index')->name('administracion.refunds.index');

Route::get('/refunds/create', 'Tab\RefundController@create')->name('administracion.refunds.create');
Route::post('/refunds/store', 'Tab\RefundController@store')->name('administracion.refunds.store');

?>
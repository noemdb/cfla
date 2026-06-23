<?php

/* resource */

Route::get('/deudas_anterior/crud', 'Tab\DeudaAnteriorController@crud')->name('administracion.deudas_anterior.crud'); 
Route::get('/deudas_anterior/edit/{id}', 'Tab\DeudaAnteriorController@edit')->name('administracion.deudas_anterior.edit');
Route::put('/deudas_anterior/update/{id}', 'Tab\DeudaAnteriorController@update')->name('administracion.deudas_anterior.update');

?>
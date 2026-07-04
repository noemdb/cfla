<?php

Route::get('/cuentaxpagars/dashboard', 'Tab\Configuracion\CuentaXPagarController@dashboard')->name('administracion.configuraciones.cuentaxpagars.dashboard');

Route::get('/cuentaxpagars/index', 'Tab\Configuracion\CuentaXPagarController@index')->name('administracion.configuraciones.cuentaxpagars.index');
Route::get('/cuentaxpagars/crud', 'Tab\Configuracion\CuentaXPagarController@crud')->name('administracion.configuraciones.cuentaxpagars.crud');

Route::get('/cuentaxpagars/create', 'Tab\Configuracion\CuentaXPagarController@create')->name('administracion.configuraciones.cuentaxpagars.create');
Route::post('/cuentaxpagars/store', 'Tab\Configuracion\CuentaXPagarController@store')->name('administracion.configuraciones.cuentaxpagars.store');

Route::get('/cuentaxpagars/edit/{id}', 'Tab\Configuracion\CuentaXPagarController@edit')->name('administracion.configuraciones.cuentaxpagars.edit');
Route::put('/cuentaxpagars/update/{id}', 'Tab\Configuracion\CuentaXPagarController@update')->name('administracion.configuraciones.cuentaxpagars.update');

Route::delete('/cuentaxpagars/destroy/{id}', 'Tab\Configuracion\CuentaXPagarController@destroy')->name('administracion.configuraciones.cuentaxpagars.destroy');

Route::get('/cuentaxpagars/account_bad', 'Tab\Configuracion\CuentaXPagarController@account_bad')->name('administracion.configuraciones.cuentaxpagars.account_bad');

Route::get('/cuentaxpagars/pestudios/late_payment', 'Tab\Configuracion\CuentaXPagarController@late_payment')->name('administracion.configuraciones.cuentaxpagars.pestudios.late_payment');
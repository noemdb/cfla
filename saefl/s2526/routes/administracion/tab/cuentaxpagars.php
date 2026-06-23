<?php

Route::get('/cuentaxpagars/dashboard', 'Tab\Configuracion\CuentaXPagarController@dashboard')->name('administracion.configuraciones.cuentaxpagars.dashboard');

Route::get('/cuentaxpagars/index', 'Tab\Configuracion\CuentaXPagarController@index')->name('administracion.configuraciones.cuentaxpagars.index');

Route::get('/cuentaxpagars/create', 'Tab\Configuracion\CuentaXPagarController@create')->name('administracion.configuraciones.cuentaxpagars.create');
Route::post('/cuentaxpagars/store', 'Tab\Configuracion\CuentaXPagarController@store')->name('administracion.configuraciones.cuentaxpagars.store');

Route::get('/cuentaxpagars/edit/{id}', 'Tab\Configuracion\CuentaXPagarController@edit')->name('administracion.configuraciones.cuentaxpagars.edit');
Route::put('/cuentaxpagars/update/{id}', 'Tab\Configuracion\CuentaXPagarController@update')->name('administracion.configuraciones.cuentaxpagars.update');
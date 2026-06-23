<?php

Route::get('/planpagos/dashboard', 'Tab\Configuracion\PlanPagoController@dashboard')->name('administracion.configuraciones.planpagos.dashboard');

Route::get('/planpagos/index', 'Tab\Configuracion\PlanPagoController@index')->name('administracion.configuraciones.planpagos.index');

Route::get('/planpagos/create', 'Tab\Configuracion\PlanPagoController@create')->name('administracion.configuraciones.planpagos.create');
Route::post('/planpagos/store', 'Tab\Configuracion\PlanPagoController@store')->name('administracion.configuraciones.planpagos.store');

Route::get('/planpagos/edit/{id}', 'Tab\Configuracion\PlanPagoController@edit')->name('administracion.configuraciones.planpagos.edit');
Route::put('/planpagos/update/{id}', 'Tab\Configuracion\PlanPagoController@update')->name('administracion.configuraciones.planpagos.update');
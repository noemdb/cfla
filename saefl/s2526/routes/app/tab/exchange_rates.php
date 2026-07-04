<?php

// Route::get('/exchange_rates/dashboard', 'Tab\Configuracion\ExchangeRateController@dashboard')->name('administracion.configuraciones.exchange_rates.dashboard');

Route::get('/exchange_rates/index', 'Tab\Configuracion\ExchangeRateController@index')->name('administracion.configuraciones.exchange_rates.index');

// Route::get('/exchange_rates/asignar/{id}', 'Tab\Configuracion\ExchangeRateController@asignar')->name('administracion.configuraciones.exchange_rates.asignar');
// Route::post('/exchange_rates/set', 'Tab\Configuracion\ExchangeRateController@set_plan')->name('administracion.configuraciones.exchange_rates.set.plan');

Route::get('/exchange_rates/create', 'Tab\Configuracion\ExchangeRateController@create')->name('administracion.configuraciones.exchange_rates.create');
Route::post('/exchange_rates/store', 'Tab\Configuracion\ExchangeRateController@store')->name('administracion.configuraciones.exchange_rates.store');

Route::get('/exchange_rates/edit/{id}', 'Tab\Configuracion\ExchangeRateController@edit')->name('administracion.configuraciones.exchange_rates.edit');
Route::put('/exchange_rates/update/{id}', 'Tab\Configuracion\ExchangeRateController@update')->name('administracion.configuraciones.exchange_rates.update');

Route::delete('/configuraciones/exchange_rates/destroy/{id}', 'Tab\Configuracion\ExchangeRateController@destroy')->name('administracion.configuraciones.exchange_rates.destroy');

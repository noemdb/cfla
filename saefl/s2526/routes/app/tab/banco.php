<?php

Route::get('/configuraciones/banco', 'Tab\Configuracion\BancoController@banco')->name('administracion.configuraciones.banco');

Route::get('/configuraciones/banco/create', 'Tab\Configuracion\BancoController@create')->name('administracion.configuraciones.banco.create');
Route::post('/configuraciones/banco/store', 'Tab\Configuracion\BancoController@store')->name('administracion.configuraciones.banco.store');

Route::get('/configuraciones/banco/{id}', 'Tab\Configuracion\BancoController@edit')->name('administracion.configuraciones.bancoedit');
Route::put('/configuraciones/banco/{id}', 'Tab\Configuracion\BancoController@update')->name('administracion.configuraciones.bancoupdate');

<?php

Route::get('/configuraciones/banco', 'Tab\Configuracion\BancoController@banco')->name('administracion.configuraciones.banco');
Route::get('/configuraciones/banco/{id}', 'Tab\Configuracion\BancoController@edit')
        ->name('administracion.configuraciones.bancoedit');
Route::put('/configuraciones/banco/{id}', 'Tab\Configuracion\BancoController@update')
        ->name('administracion.configuraciones.bancoupdate');
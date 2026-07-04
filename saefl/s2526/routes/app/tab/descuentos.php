<?php

// Route::get('/descuentos/index', 'Tab\Configuracion\DescuentoController@index')->name('administracion.configuraciones.descuentos.index');

Route::get('/descuentos/create', 'Tab\Configuracion\DescuentoController@create')->name('administracion.configuraciones.descuentos.create');

Route::post('/descuentos/store', 'Tab\Configuracion\DescuentoController@store')->name('administracion.configuraciones.descuentos.store');

Route::get('/descuentos/crud', 'Tab\Configuracion\DescuentoController@crud')->name('administracion.configuraciones.descuentos.crud');

Route::delete('/descuentos/destroy/{id}', 'Tab\Configuracion\DescuentoController@destroy')->name('administracion.configuraciones.descuentos.destroy');




?>

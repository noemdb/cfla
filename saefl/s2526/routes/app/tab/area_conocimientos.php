<?php
Route::get('/configuraciones/area_conocimientos', 'Tab\Configuracion\AreaConocimientoController@index')->name('administracion.configuraciones.area_conocimientos');
Route::get('/configuraciones/area_conocimientos/index', 'Tab\Configuracion\AreaConocimientoController@index')->name('administracion.configuraciones.area_conocimientos.index');

Route::get('/configuraciones/area_conocimientos/create', 'Tab\Configuracion\AreaConocimientoController@create')->name('administracion.configuraciones.area_conocimientos.create');
Route::post('/configuraciones/area_conocimientos/store', 'Tab\Configuracion\AreaConocimientoController@store')->name('administracion.configuraciones.area_conocimientos.store');

Route::get('/configuraciones/area_conocimientos/{id}', 'Tab\Configuracion\AreaConocimientoController@edit')->name('administracion.configuraciones.area_conocimientos.edit');
Route::put('/configuraciones/area_conocimientos/{id}', 'Tab\Configuracion\AreaConocimientoController@update')->name('administracion.configuraciones.area_conocimientos.update');

Route::delete('/configuraciones/area_conocimientos/destroy/{id}', 'Tab\Configuracion\AreaConocimientoController@destroy')->name('administracion.configuraciones.area_conocimientos.destroy');

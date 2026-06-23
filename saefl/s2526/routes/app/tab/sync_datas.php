<?php

Route::get('/sync_datas/index', 'Tab\Configuracion\SyncDataController@index')->name('administracion.configuraciones.sync_datas.index');

Route::get('/sync_datas/create', 'Tab\Configuracion\SyncDataController@create')->name('administracion.configuraciones.sync_datas.create');

Route::post('/sync_datas/store', 'Tab\Configuracion\SyncDataController@store')->name('administracion.configuraciones.sync_datas.store');

Route::get('/sync_datas/crud', 'Tab\Configuracion\SyncDataController@crud')->name('administracion.configuraciones.sync_datas.crud');

?>

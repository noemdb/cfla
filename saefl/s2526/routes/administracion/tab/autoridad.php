<?php
Route::get('/configuraciones/autoridad', 'Tab\Configuracion\AutoridadController@autoridad')
->name('administracion.configuraciones.autoridad');
Route::put('/configuraciones/autoridad/{id}', 'Tab\Configuracion\AutoridadController@AutoridadUpdate')
->name('administracion.configuraciones.autoridadupdate');
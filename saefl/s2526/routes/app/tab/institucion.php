<?php

Route::get('/configuraciones/institucion', 'Tab\Configuracion\InstitucionController@institucion')->name('administracion.configuraciones.institucion');
Route::put('/configuraciones/institucion/{id}', 'Tab\Configuracion\InstitucionController@InstitucionUpdate')->name('administracion.configuraciones.institucionupdate');
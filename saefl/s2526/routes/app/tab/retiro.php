<?php

/* resource */

// Route::get('/estudiants/retirar', 'Tab\EstudiantesController@retirar')->name('administracion.estudiants.retirar');
// Route::post('/estudiants/set/retirar/{id}', 'Tab\EstudiantesController@set_retirar')->name('administracion.estudiants.set.retirar');

Route::get('/retiro/index', 'Tab\RetiroController@index')->name('administracion.retiros.index');
Route::get('/retiro/crud', 'Tab\RetiroController@crud')->name('administracion.retiros.crud');
Route::post('/retiro/store/{id}', 'Tab\RetiroController@store')->name('administracion.retiros.store');

?>
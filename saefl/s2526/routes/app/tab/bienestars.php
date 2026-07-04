<?php

/* resource */
Route::get('/bienestars/index', 'Tab\BienestarController@index')->name('administracion.bienestars.index');
Route::get('/bienestars/indicators', 'Tab\BienestarController@indicators')->name('administracion.bienestars.indicators');
Route::get('/bienestars/batch', 'Tab\BienestarController@batch')->name('administracion.bienestars.batch');

?>

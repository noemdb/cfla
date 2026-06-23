<?php

/* resource */

Route::get('/mbancarios/crud', 'Tab\MbancarioController@crud')->name('administracion.mbancarios.crud');
Route::get('/mbancarios/carga/csv/', 'Tab\MbancarioController@cargaCSV')->name('administracion.mbancarios.carga.csv');
Route::post('/mbancarios/carga/csv/', 'Tab\MbancarioController@cargaCSVPost')->name('administracion.mbancarios.carga.csv.post');
Route::post('/mbancarios/store/csv', 'Tab\MbancarioController@storeCSV')->name('administracion.mbancarios.store.csv');

Route::delete('/mbancarios/destroy/{id}', 'Tab\MbancarioController@destroy')->name('administracion.mbancarios.destroy');

//ajax


?>

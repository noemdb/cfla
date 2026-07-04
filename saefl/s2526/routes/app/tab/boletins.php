<?php

/* resource */

Route::get('/boletins/corte', 'Tab\BoletinController@corte')->name('administracion.boletins.corte');
Route::get('/boletins/positions', 'Tab\BoletinController@positions')->name('administracion.boletins.positions');
Route::get('/boletins/indicators', 'Tab\BoletinController@indicators')->name('administracion.boletins.indicators');

Route::get('/boletins/index', 'Tab\BoletinController@index')->name('administracion.boletins.index');
Route::get('/boletins/crud', 'Tab\BoletinController@crud')->name('administracion.boletins.crud');
Route::get('/boletins/crud_ajuste', 'Tab\BoletinController@crud_ajuste')->name('administracion.boletins.crud_ajuste');

Route::get('/boletins/boletin', 'Tab\BoletinController@boletin')->name('administracion.boletins.boletin');
Route::get('/boletins/carga', 'Tab\BoletinController@carga')->name('administracion.boletins.carga');
Route::get('/boletins/sabana/', 'Tab\BoletinController@sabana')->name('administracion.boletins.sabana');
Route::get('/boletins/sabanafull/', 'Tab\BoletinController@sabanafull')->name('administracion.boletins.sabanafull');

Route::get('/boletins/resumen_final/', 'Tab\BoletinController@resumen_final')->name('administracion.boletins.resumen_final');

Route::get('/boletins/carga/xls/', 'Tab\BoletinController@cargaXls')->name('administracion.boletins.carga.xls');
Route::post('/boletins/carga/xls/', 'Tab\BoletinController@cargaXlsPost')->name('administracion.boletins.carga.xls.post');
Route::post('/boletins/store/xls', 'Tab\BoletinController@store_xls')->name('administracion.boletins.store.xls');

Route::get('/boletins/planilla_notas', 'Tab\BoletinController@planilla_notas')->name('administracion.boletins.planilla_notas');

Route::get('/boletins/ajustes/', 'Tab\BoletinController@ajustes')->name('administracion.boletins.ajustes');
Route::post('/boletin/store_ajustes', 'Tab\BoletinController@store_ajustes')->name('administracion.boletins.store_ajustes');

Route::delete('/boletin/destroy/ajuste/{id}', 'Tab\BoletinController@destroyAjuste')->name('administracion.boletins.ajuste.destroy');

Route::get('/boletin/create', 'Tab\BoletinController@create')->name('administracion.boletins.create');
Route::post('/boletin/store', 'Tab\BoletinController@store')->name('administracion.boletins.store');

Route::get('/boletin/edit/{id}', 'Tab\BoletinController@edit')->name('administracion.boletins.edit');
Route::put('/boletin/update/{id}', 'Tab\BoletinController@update')->name('administracion.boletins.update');

Route::delete('/boletin/destroy/{id}', 'Tab\BoletinController@destroy')->name('administracion.boletins.destroy');

// fix
Route::get('/boletins/fix/without/evalaucion/', 'Tab\BoletinController@index')->name('administracion.boletins.fix.without.evalaucion');

?>

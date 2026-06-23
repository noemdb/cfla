<?php

/* resource */

Route::get('/boletins/index', 'Tab\BoletinController@index')->name('profesors.boletins.index');
Route::get('/boletins/boletin', 'Tab\BoletinController@boletin')->name('profesors.boletins.boletin');
Route::get('/boletins/planilla_notas', 'Tab\BoletinController@planilla_notas')->name('profesors.boletins.planilla_notas');

// Route::get('/boletins/carga/{id}/', 'Tab\BoletinController@carga')->name('profesors.boletins.carga');
Route::get('/boletins/carga/', 'Tab\BoletinController@carga')->name('profesors.boletins.carga');

Route::get('/boletin/create', 'Tab\BoletinController@create')->name('profesors.boletins.create');
Route::post('/boletin/store', 'Tab\BoletinController@store')->name('profesors.boletins.store');

Route::get('/boletin/edit/{id}', 'Tab\BoletinController@edit')->name('profesors.boletins.edit');
Route::put('/boletin/update/{id}', 'Tab\BoletinController@update')->name('profesors.boletins.update');

Route::delete('/boletin/destroy/{id}', 'Tab\BoletinController@destroy')->name('profesors.boletins.destroy');

Route::get('/boletins/carga/xls/', 'Tab\BoletinController@cargaXls')->name('profesors.boletins.carga.xls');
Route::post('/boletins/carga/xls/', 'Tab\BoletinController@cargaXlsPost')->name('profesors.boletins.carga.xls.post');
Route::post('/boletins/store/xls', 'Tab\BoletinController@store_xls')->name('profesors.boletins.store.xls');

?>

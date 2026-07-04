<?php

/* resource */
Route::get('/administrativas/list/view', 'Tab\AdministrativaController@listview')->name('administracion.administrativas.list.view');
Route::get('/administrativas/list/view/excel', 'Tab\AdministrativaController@list_view_excel')->name('administracion.administrativas.list.view.excel');
Route::get('/administrativas/book', 'Tab\AdministrativaController@book')->name('administracion.administrativas.book');

// Route::get('/administrativas/asignar/{id}', 'Tab\AdministrativaController@asignar')->name('administracion.administrativas.asignar');
Route::get('/administrativas/asignar', 'Tab\AdministrativaController@asignar')->name('administracion.administrativas.asignar');
Route::post('/administrativas/asignarStore', 'Tab\AdministrativaController@asignarStore')->name('administracion.administrativas.asignarStore');

Route::post('/administrativas/set', 'Tab\AdministrativaController@set_plan')->name('administracion.administrativas.set.plan');
Route::post('/administrativas/inscribir/{id}', 'Tab\AdministrativaController@inscribir')->name('administracion.administrativas.inscribir');

Route::get('/administrativas/crud', 'Tab\AdministrativaController@crud')->name('administracion.administrativas.crud');

Route::get('/administrativas/retiro', 'Tab\AdministrativaController@retiro')->name('administracion.administrativas.retiro');

Route::delete('/administrativas/destroy/{id}', 'Tab\AdministrativaController@destroy')->name('administracion.administrativas.destroy');

Route::get('/administrativas/edit/{id}', 'Tab\AdministrativaController@edit')->name('administracion.administrativas.edit');
Route::put('/administrativas/update/{id}', 'Tab\AdministrativaController@update')->name('administracion.administrativas.update');

Route::get('/administrativas/unregistered', 'Tab\AdministrativaController@unregistered')->name('administracion.administrativas.unregistered');


Route::get('/administrativas/asistente', 'Tab\AdministrativaController@asistente')->name('administracion.administrativas.asistente');


?>

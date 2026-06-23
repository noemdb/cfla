<?php

// Route::get('/bmains/dashboard', 'Bot\BmainController@dashboard')->name('administracion.autoresponders.bmains.dashboard');

Route::get('/autoresponders/bmains/index', 'Bot\BmainController@index')->name('administracion.autoresponders.bmains.index');
Route::get('/autoresponders/metas/chat', 'Bot\BmainController@Metachat')->name('administracion.autoresponders.metas.chat');
Route::get('/autoresponders/metas/chat/ws', 'Bot\BmainController@MetachatWS')->name('administracion.autoresponders.metas.ws');

// Route::get('/bmains/asignar/{id}', 'Bot\BmainController@asignar')->name('administracion.autoresponders.bmains.asignar');
// Route::post('/bmains/set', 'Bot\BmainController@set_plan')->name('administracion.autoresponders.bmains.set.plan');

Route::get('/autoresponders/bmains/create', 'Bot\BmainController@create')->name('administracion.autoresponders.bmains.create');
Route::post('/autoresponders/bmains/store', 'Bot\BmainController@store')->name('administracion.autoresponders.bmains.store');

Route::get('/autoresponders/bmains/edit/{id}', 'Bot\BmainController@edit')->name('administracion.autoresponders.bmains.edit');
Route::put('/autoresponders/bmains/update/{id}', 'Bot\BmainController@update')->name('administracion.autoresponders.bmains.update');

Route::delete('/autoresponders/bmains/destroy/{id}', 'Bot\BmainController@destroy')->name('administracion.autoresponders.bmains.destroy');

<?php

/* coll_messeges */
Route::get('/coll_messeges/index', 'Tab\Collection\CollMessegeController@index')->name('administracion.collections.coll_messeges.index');
Route::get('/coll_messeges/crud/', 'Tab\Collection\CollMessegeController@crud')->name('administracion.collections.coll_messeges.crud');
Route::get('/coll_messeges/create', 'Tab\Collection\CollMessegeController@create')->name('administracion.collections.coll_messeges.create');
Route::post('/coll_messeges/store', 'Tab\Collection\CollMessegeController@store')->name('administracion.collections.coll_messeges.store');
Route::get('/coll_messeges/edit/{id}', 'Tab\Collection\CollMessegeController@edit')->name('administracion.collections.coll_messeges.edit');
Route::put('/coll_messeges/update/{id}', 'Tab\Collection\CollMessegeController@update')->name('administracion.collections.coll_messeges.update');
Route::delete('/coll_messeges/destroy/{id}', 'Tab\Collection\CollMessegeController@destroy')->name('administracion.collections.coll_messeges.destroy');
Route::get('/coll_messeges/ajax/preview/id/{id}', 'Tab\Collection\CollMessegeController@previewMsnId')->name('administracion.collections.coll_messeges.preview.id');

Route::get('/coll_messeges/send/individual', 'Tab\Collection\CollMessegeController@sendIndividual')->name('administracion.collections.coll_messeges.sendIndividual');



?>

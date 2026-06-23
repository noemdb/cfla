<?php

/* coll_nivels */
Route::get('/coll_nivels/index', 'Tab\Collection\CollNivelController@index')->name('administracion.collections.coll_nivels.index');
Route::get('/coll_nivels/crud/', 'Tab\Collection\CollNivelController@crud')->name('administracion.collections.coll_nivels.crud');
Route::get('/coll_nivels/create', 'Tab\Collection\CollNivelController@create')->name('administracion.collections.coll_nivels.create');
Route::post('/coll_nivels/store', 'Tab\Collection\CollNivelController@store')->name('administracion.collections.coll_nivels.store');
Route::get('/coll_nivels/edit/{id}', 'Tab\Collection\CollNivelController@edit')->name('administracion.collections.coll_nivels.edit');
Route::put('/coll_nivels/update/{id}', 'Tab\Collection\CollNivelController@update')->name('administracion.collections.coll_nivels.update');
Route::delete('/coll_nivels/destroy/{id}', 'Tab\Collection\CollNivelController@destroy')->name('administracion.collections.coll_nivels.destroy');
Route::get('/coll_nivels/ajax/preview/id/{id}', 'Tab\Collection\CollNivelController@previewMsnId')->name('administracion.collections.coll_nivels.preview.id');




?>

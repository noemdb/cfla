<?php

/* resource */
Route::get('/prepagos/crud', 'Tab\PrepagoController@crud')->name('representants.prepagos.crud');

Route::get('/prepagos/create', 'Tab\PrepagoController@create')->name('representants.prepagos.create');
Route::post('/prepagos/store', 'Tab\PrepagoController@store')->name('representants.prepagos.store');

Route::get('/prepagos/{id}', 'Tab\PrepagoController@edit')->name('representants.prepagos.edit');
Route::put('/prepagos/{id}', 'Tab\PrepagoController@update')->name('representants.prepagos.update');

Route::delete('/prepagos/destroy/{id}', 'Tab\PrepagoController@destroy')->name('representants.prepagos.destroy');

?>

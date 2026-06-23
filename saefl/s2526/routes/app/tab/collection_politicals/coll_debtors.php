<?php

/* coll_debtors */
Route::get('/coll_debtors/index', 'Tab\Collection\CollDebtorController@index')->name('administracion.collections.coll_debtors.index');
Route::get('/coll_debtors/crud/', 'Tab\Collection\CollDebtorController@crud')->name('administracion.collections.coll_debtors.crud');
Route::get('/coll_debtors/create', 'Tab\Collection\CollDebtorController@create')->name('administracion.collections.coll_debtors.create');
Route::post('/coll_debtors/store', 'Tab\Collection\CollDebtorController@store')->name('administracion.collections.coll_debtors.store');
Route::get('/coll_debtors/edit/{id}', 'Tab\Collection\CollDebtorController@edit')->name('administracion.collections.coll_debtors.edit');
Route::put('/coll_debtors/update/{id}', 'Tab\Collection\CollDebtorController@update')->name('administracion.collections.coll_debtors.update');
Route::delete('/coll_debtors/destroy/{id}', 'Tab\Collection\CollDebtorController@destroy')->name('administracion.collections.coll_debtors.destroy');



?>

<?php

/* resource */

Route::get('/edescriptivas/index', 'Tab\EDescriptivaController@index')->name('profesors.edescriptivas.index');
Route::get('/edescriptivas/crud', 'Tab\EDescriptivaController@crud')->name('profesors.edescriptivas.crud');

Route::get('/edescriptivas/create', 'Tab\EDescriptivaController@create')->name('profesors.edescriptivas.create');
Route::post('/edescriptivas/store', 'Tab\EDescriptivaController@store')->name('profesors.edescriptivas.store');

Route::get('/edescriptivas/edit/{id}', 'Tab\EDescriptivaController@edit')->name('profesors.edescriptivas.edit');
Route::put('/edescriptivas/update/{id}', 'Tab\EDescriptivaController@update')->name('profesors.edescriptivas.update');

Route::delete('/edescriptivas/destroy/{id}', 'Tab\EDescriptivaController@destroy')->name('profesors.edescriptivas.destroy');

?>

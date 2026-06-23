<?php

/* resource */

Route::get('/profesor_gestables/index', 'Tab\ProfesorGestableController@index')->name('profesors.profesor_gestables.index');
Route::get('/profesor_gestables/crud', 'Tab\ProfesorGestableController@crud')->name('profesors.profesor_gestables.crud');

// Route::get('/profesor_gestables/create/{pevaluacion_id}', 'Tab\ProfesorGestableController@create')->name('profesors.profesor_gestables.create');
// Route::post('/profesor_gestables/store', 'Tab\ProfesorGestableController@store')->name('profesors.profesor_gestables.store');

// Route::get('/profesor_gestables/edit/{id}', 'Tab\ProfesorGestableController@edit')->name('profesors.profesor_gestables.edit');
// Route::put('/profesor_gestables/update/{id}', 'Tab\ProfesorGestableController@update')->name('profesors.profesor_gestables.update');

// Route::delete('/profesor_gestables/destroy/{id}', 'Tab\ProfesorGestableController@destroy')->name('profesors.profesor_gestables.destroy');


?>

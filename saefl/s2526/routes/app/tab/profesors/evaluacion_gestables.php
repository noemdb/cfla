<?php

/* resource */

Route::get('/evaluacion_gestables/index', 'Tab\EvaluacionGestableController@index')->name('profesors.evaluacion_gestables.index');
Route::get('/evaluacion_gestables/crud', 'Tab\EvaluacionGestableController@crud')->name('profesors.evaluacion_gestables.crud');

Route::get('/evaluacion_gestables/create/{evaluacion_id}', 'Tab\EvaluacionGestableController@create')->name('profesors.evaluacion_gestables.create');
Route::post('/evaluacion_gestables/store', 'Tab\EvaluacionGestableController@store')->name('profesors.evaluacion_gestables.store');

Route::get('/evaluacion_gestables/edit/{id}', 'Tab\EvaluacionGestableController@edit')->name('profesors.evaluacion_gestables.edit');
Route::put('/evaluacion_gestables/update/{id}', 'Tab\EvaluacionGestableController@update')->name('profesors.evaluacion_gestables.update');

Route::delete('/evaluacion_gestables/destroy/{id}', 'Tab\EvaluacionGestableController@destroy')->name('profesors.evaluacion_gestables.destroy');


?>

<?php

/* assit_schedules */
Route::get('/asisst_controls/assit_schedules/index', 'AssistControl\AssitScheduleController@index')->name('administracion.asisst_controls.assit_schedules.index');

Route::get('/asisst_controls/assit_schedules/create', 'AssistControl\AssitScheduleController@create')->name('administracion.asisst_controls.assit_schedules.create');
Route::post('/asisst_controls/assit_schedules/store', 'AssistControl\AssitScheduleController@store')->name('administracion.asisst_controls.assit_schedules.store');
Route::get('/asisst_controls/assit_schedules/edit/{id}', 'AssistControl\AssitScheduleController@edit')->name('administracion.asisst_controls.assit_schedules.edit');
Route::put('/asisst_controls/assit_schedules/update/{id}', 'AssistControl\AssitScheduleController@update')->name('administracion.asisst_controls.assit_schedules.update');


?>

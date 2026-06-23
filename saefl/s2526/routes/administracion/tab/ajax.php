<?php
Route::get('/ajax/select/gradoByseccion/{id}', 'Ajax\FillSelectController@gradoByseccion')->name('administracion.ajax.fill.gradoByseccion');
Route::get('/ajax/select/studiantBytype/{type}', 'Ajax\FillSelectController@studiantBytype')->name('administracion.ajax.fill.studiantBytype');
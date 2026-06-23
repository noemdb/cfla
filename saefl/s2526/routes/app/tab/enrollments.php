<?php

use App\Http\Controllers\Administracion\Tab\EnrollmentController;

Route::get('/enrollments/index', 'Tab\EnrollmentController@index')->name('administracion.enrollments.index');
Route::get('/enrollments/token', 'Tab\EnrollmentController@token')->name('administracion.enrollments.token');
Route::get('/enrollments/crud', 'Tab\EnrollmentController@crud')->name('administracion.enrollments.crud');
// Route::get('/enrollments/edit/{id}', 'Tab\EnrollmentController@edit')->name('administracion.enrollments.edit');
Route::put('/enrollments/update/{id}', 'Tab\EnrollmentController@update')->name('administracion.enrollments.update');

Route::get('/enrollments/create', 'Tab\EnrollmentController@create')->name('administracion.enrollments.create');
Route::post('/enrollments/store', 'Tab\EnrollmentController@store')->name('administracion.enrollments.store');

Route::get('/enrollments/edit/{enrollment}', [EnrollmentController::class, 'edit'])->name('administracion.enrollments.edit');

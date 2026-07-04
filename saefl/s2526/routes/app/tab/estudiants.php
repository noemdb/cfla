<?php

Route::get('/estudiants/dashboard', 'Tab\EstudiantesController@dashboard')->name('administracion.estudiants.dashboard');

// Route::resource('tab/estudiants/','Tab\EstudiantesController');
// Route::resource('estudiants','Tab\EstudiantesController');
Route::get('/estudiants/index', 'Tab\EstudiantesController@index')->name('administracion.estudiants.index');

Route::get('/estudiants/create', 'Tab\EstudiantesController@create')->name('administracion.estudiants.create');
Route::post('/estudiants/store', 'Tab\EstudiantesController@store')->name('administracion.estudiants.store');

Route::get('/estudiants/edit', 'Tab\EstudiantesController@edit')->name('administracion.estudiants.edit');
Route::get('/estudiants/edit/ci/{ci}', 'Tab\EstudiantesController@edit_ci')->name('administracion.estudiants.edit_ci');
Route::put('/estudiants/update/{id}', 'Tab\EstudiantesController@update')->name('administracion.estudiants.update');

Route::delete('/estudiants/destroy/{id}', 'Tab\EstudiantesController@destroy')->name('administracion.estudiants.destroy');

Route::get('/estudiants/crud', 'Tab\EstudiantesController@crud')->name('administracion.estudiants.crud');
Route::get('/estudiants/blacklist', 'Tab\EstudiantesController@blacklist')->name('administracion.estudiants.blacklist');

Route::group(['middleware' => ['is_admon']], function () {
    // Route::get('/estudiants/historico/{id}', 'Tab\EstudiantesController@historico')->name('administracion.estudiants.historico');
    Route::get('/estudiants/historico', 'Tab\EstudiantesController@historico')->name('administracion.estudiants.historico');
    Route::get('/estudiants/saldos', 'Tab\EstudiantesController@saldos')->name('administracion.estudiants.saldos');

    Route::get('/estudiants/pagos', 'Tab\EstudiantesController@pagos')->name('administracion.estudiants.pagos');
});


Route::group(['middleware' => ['is_control']], function () {
    Route::get('/estudiants/pases', 'Tab\EstudiantesController@pases')->name('administracion.estudiants.pases');
});
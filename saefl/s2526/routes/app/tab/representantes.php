<?php

Route::get('/representants/dashboard', 'Tab\RepresentantsController@dashboard')->name('administracion.representants.dashboard');
Route::get('/representants/index', 'Tab\RepresentantsController@index')->name('administracion.representants.index');

Route::get('/representants/create','Tab\RepresentantsController@create')->name('administracion.representants.create');
Route::post('/representants/store','Tab\RepresentantsController@store')->name('administracion.representants.store');

Route::get('/representants/edit/{id}','Tab\RepresentantsController@edit')->name('administracion.representants.edit');
Route::put('/representants/update/{id}','Tab\RepresentantsController@update')->name('administracion.representants.update');

Route::get('/representants/crud', 'Tab\RepresentantsController@crud')->name('administracion.representants.crud');
Route::get('/representants/blacklist', 'Tab\RepresentantsController@blacklist')->name('administracion.representants.blacklist');

Route::get('/representants/book', 'Tab\InscripcionController@book')->name('administracion.representants.book');

Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/representants/historico/index', 'Tab\RepresentantsController@historico_index')->name('administracion.representants.historico.index');
    Route::get('/representants/historico', 'Tab\RepresentantsController@historico')->name('administracion.representants.historico');
    Route::get('/representants/saldos', 'Tab\RepresentantsController@saldos')->name('administracion.representants.saldos');
    Route::get('/representants/pagos', 'Tab\RepresentantsController@pagos')->name('administracion.representants.pagos');
    Route::get('/representants/auditorias', 'Tab\RepresentantsController@auditorias')->name('administracion.representants.auditorias');
    Route::get('/representants/solvents', 'Tab\RepresentantsController@solvents')->name('administracion.representants.solvents');
    Route::get('/representants/saldosDate', 'Tab\RepresentantsController@saldosDate')->name('administracion.representants.saldosDate');
    //Route::get('/deudas_anterior/crud', 'Tab\DeudaAnteriorController@crud')->name('administracion.deudas_anterior.crud');

    // Agregar esta ruta al archivo de rutas existente
    // Route::get('/representants/timeline/index', 'Tab\RepresentantsController@timeline_index')->name('administracion.representants.timeline.index');
    // Route::get('/representants/timeline', 'Tab\RepresentantsController@timeline')->name('administracion.representants.timeline');

    // En routes/web.php o el archivo de rutas correspondiente
Route::get('/representants/timeline', 'Tab\RepresentantsController@timeline')->name('administracion.representants.timeline');
Route::get('/representants/timeline/{id}', 'Tab\RepresentantsController@timeline_show')->name('administracion.representants.timeline.show');

});

Route::group(['middleware'=>['is_admin']], function(){
    Route::delete('/representants/destroy/{id}', 'Tab\RepresentantsController@destroy')->name('administracion.representants.destroy');
});
?>

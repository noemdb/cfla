<?php

Route::get('/registropagos/recargo/morosidad', 'Tab\AsistentController@recargo_morosidad')->name('administracion.registropagos.recargo.morosidad');

/* resource */
Route::get('/registropagos/livewire/asistent', 'Tab\AsistentController@livewire')->name('administracion.registropagos.livewire.asistent');

Route::get('/registropagos/asistent', 'Tab\AsistentController@asistent')->name('administracion.registropagos.asistent');

// Route::post('/registropagos/asistent/representant/create/{id}', 'Tab\AsistentController@asistent_representant_create')->name('administracion.registropagos.asistent.representant.create');
Route::get('/registropagos/asistent/representant/create', 'Tab\AsistentController@asistent_representant_create')->name('administracion.registropagos.asistent.representant.create');
Route::post('/registropagos/asistent/representant/store', 'Tab\AsistentController@store_representant_exchange')->name('administracion.registropagos.asistent.store.representant');

Route::get('/registropagos/asistent/estructura/create', 'Tab\AsistentController@estructura_create')->name('administracion.registropagos.asistent.estructura.create');
Route::get('/registropagos/asistent/asistent/individual', 'Tab\AsistentController@asistentIndividual')->name('administracion.registropagos.asistent.individual');


Route::get('/registropagos/dashboard', 'Tab\RegistroPagosController@dashboard')->name('administracion.registropagos.dashboard');

// Route::get('/registropagos/crud', 'Tab\RegistroPagosController@crud')->name('administracion.registropagos.crud');
Route::get('/registropagos/crud', 'Tab\RegistroPagosController@crud')->name('administracion.registropagos.crud');
Route::get('/registropagos/adelantados', 'Tab\RegistroPagosController@adelantados')->name('administracion.registropagos.adelantados');
Route::get('/registropagos/cuentaxpagars', 'Tab\RegistroPagosController@cuentaxpagars')->name('administracion.registropagos.cuentaxpagars');
Route::get('/registropagos/cuentaxpagars/estudiants', 'Tab\RegistroPagosController@cuentaxpagarsEstudiants')->name('administracion.registropagos.cuentaxpagars.estudiants');
Route::get('/registropagos/conceptopagos', 'Tab\RegistroPagosController@conceptopagos')->name('administracion.registropagos.conceptopagos');
Route::get('/registropagos/cuentaxpagars/individual', 'Tab\RegistroPagosController@cuentaxpagarsIndividual')->name('administracion.registropagos.cuentaxpagars.individual');
Route::get('/registropagos/irregulars', 'Tab\RegistroPagosController@irregulars')->name('administracion.registropagos.irregulars');

Route::get('/registropagos/irregulars', 'Tab\RegistroPagosController@irregulars')->name('administracion.registropagos.irregulars');

// Route::get('/registropagos/cruda',function(){return view('administracion.registropagos.cruda');})->name('administracion.registropagos.cruda');

Route::get('/registropagos/individual', 'Tab\RegistroPagosController@individual')->name('administracion.registropagos.individual');

Route::get('/registropagos/list/view', 'Tab\RegistroPagosController@listview')->name('administracion.registropagos.list.view');

Route::get('/registropagos/index', 'Tab\RegistroPagosController@index')->name('administracion.registropagos.index');

Route::get('/registropagos/book', 'Tab\RegistroPagosController@book')->name('administracion.registropagos.book');

Route::get('/registropagos/create/{id}/{ctaid}', 'Tab\RegistroPagosController@create')->name('administracion.registropagos.create');

Route::post('/registropagos/store', 'Tab\RegistroPagosController@store')->name('administracion.registropagos.store');

Route::get('/registropagos/representant/create/{id}', 'Tab\RegistroPagosController@representant_create')->name('administracion.registropagos.representant.create');
Route::post('/registropagos/representant/store', 'Tab\RegistroPagosController@representant_store')->name('administracion.registropagos.store.representant');

Route::get('/registropagos/create/representant/exchange/{id}', 'Tab\RegistroPagosController@create_representant_exchange')->name('administracion.registropagos.create_representant_exchange');
Route::post('/registropagos/store/representant/exchange', 'Tab\RegistroPagosController@store_representant_exchange')->name('administracion.registropagos.store.representant_exchange');

Route::get('/registropagos/parcial/create/{id}', 'Tab\RegistroPagosController@parcial_create')->name('administracion.registropagos.parcial.create');
Route::post('/registropagos/parcial/store', 'Tab\RegistroPagosController@parcial_store')->name('administracion.registropagos.parcial.store');

Route::get('/registropagos/edit/{id}', 'Tab\RegistroPagosController@edit')->name('administracion.registropagos.edit');

Route::put('/registropagos/update/{id}', 'Tab\RegistroPagosController@update')->name('administracion.registropagos.update');

Route::get('/registropagos/show/{id}', 'Tab\RegistroPagosController@show')->name('administracion.registropagos.show');

Route::get('/registropagos/batchs', 'Tab\RegistroPagosController@batchs')->name('administracion.registropagos.batchs');

Route::post('/registropagos/anular/{id}', 'Tab\RegistroPagosController@anular')->name('administracion.registropagos.anular');
Route::get('/registropagos/force-elete/{id}', 'Tab\RegistroPagosController@forceDelete')->name('administracion.registropagos.forceDelete');

//ajax
Route::get('/registropagos/create/gradoByseccion/{id}', 'Tab\RegistroPagosController@gradoByseccion');

Route::get('/registropagos/edit/gradoByseccion/{id}', 'Tab\RegistroPagosController@gradoByseccion');



Route::get('/registropagos/cancelations', 'Tab\RegistroPagosController@cancelations')->name('administracion.registropagos.cancelations');

// New routes for cancellation workflow
Route::post('/registropagos/{id}/cancel', 'Tab\RegistroPagosController@cancel')->name('administracion.registropagos.cancel');
Route::post('/registropagos/{id}/approve-cancel', 'Tab\RegistroPagosController@approveCancel')->name('administracion.registropagos.approve-cancel');
Route::get('/registropagos/{id}/cancellation-status', 'Tab\RegistroPagosController@cancellationStatus')->name('administracion.registropagos.cancellation-status');

// AJAX routes for modals
Route::get('/ajax/registropago/{id}/details', 'Tab\RegistroPagosController@getPaymentDetails')->name('administracion.ajax.registropago.details');
Route::get('/ajax/registropago/{id}/cancellation-form', 'Tab\RegistroPagosController@getCancellationForm')->name('administracion.ajax.registropago.cancellation-form');

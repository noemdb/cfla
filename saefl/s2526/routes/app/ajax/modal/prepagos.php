<?php

/*fill parials*/
Route::get('/ajax/fill/parials/modal/prepago/create/{id}', 'Ajax\FillPartialController@PrepagoCreateModal')->name('administracion.ajax.fill.modal.prepago.create');
Route::get('/ajax/fill/parials/modal/prepago/pago/{id}', 'Ajax\FillPartialController@PrepagoPagoModal')->name('administracion.ajax.fill.modal.prepago.pago');
Route::get('/ajax/fill/parials/modal/prepago/abono/{id}', 'Ajax\FillPartialController@PrepagoAbonoModal')->name('administracion.ajax.fill.modal.prepago.abono');

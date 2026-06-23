<?php

/*fill parials*/
Route::get('/ajax/fill/parials/modal/registro_pago/{id}', 'Ajax\FillPartialController@RegistroPagoModal')->name('administracion.ajax.fill.modal.registro_pago');
Route::get('/ajax/fill/parials/modal/registro_pago_combinado/{id}', 'Ajax\FillPartialController@RegistroPagoCombinadoModal')->name('administracion.ajax.fill.modal.registro_pago_combinado');
Route::get('/ajax/fill/parials/modal/resume/registro_pago_combinado/{id}', 'Ajax\FillPartialController@RegistroPagoCombinadoModalResume')->name('administracion.ajax.fill.modal.resume.registro_pago_combinado');
Route::get('/ajax/fill/parials/modal/list/registro_pago/representant/{id}', 'Ajax\FillPartialController@listRegistroRagoRepresentant')->name('administracion.ajax.fill.modal.list.registro_pago.representant');

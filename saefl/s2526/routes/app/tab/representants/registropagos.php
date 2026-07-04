<?php

/* resource */
Route::get('/registropagos/crud', 'Tab\RegistroPagoController@crud')->name('representants.registropagos.crud');


Route::get('/registropagos/payments', 'Tab\RegistroPagoController@payments')->name('representants.registropagos.payments');
Route::post('/registropagos/payments/store', 'Tab\PrepagoController@paymentsStore')->name('representants.registropagos.payments.store');


?>

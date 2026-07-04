<?php
/* modal create */

Route::get('/ajax/modal/cuentaxpagar/show/{id}', 'Ajax\Configuraciones\CuentaXPagarController@show')->name('administracion.ajax.fill.modal.cuentaxpagar');
Route::get('/ajax/modal/cuentaxpagar/edit/{id}', 'Ajax\Configuraciones\CuentaXPagarController@edit')->name('administracion.ajax.fill.modal.cuentaxpagar.edit');
Route::get('/ajax/modal/cuentaxpagar/asignar/{id}', 'Ajax\Configuraciones\CuentaXPagarController@asignar')->name('administracion.ajax.fill.modal.cuentaxpagar.asignar_concepto');

Route::get('/ajax/modal/show/conceptopago/{id}', 'Ajax\Configuraciones\CuentaXPagarController@ShowConceptoPago')->name('administracion.ajax.fill.modal.conceptopago');

?>

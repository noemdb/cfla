<?php
/* modal create */

Route::get('/ajax/modal/concepto_pagos/show/{id}', 'Ajax\Configuraciones\ConceptoPagoController@show')->name('administracion.ajax.fill.modal.concepto_pagos.show');
Route::get('/ajax/modal/concepto_pagos/edit/{id}', 'Ajax\Configuraciones\ConceptoPagoController@edit')->name('administracion.ajax.fill.modal.concepto_pagos.edit');
// Route::get('/ajax/modal/concepto_pagos/asignar/{id}', 'Ajax\Configuraciones\ConceptoPagoController@AsignarConceptoCuentaXPagar')->name('administracion.ajax.fill.modal.concepto_pagos.asignar_concepto');

// Route::get('/ajax/modal/show/conceptopago/{id}', 'Ajax\Configuraciones\ConceptoPagoController@ShowConceptoPago')->name('administracion.ajax.fill.modal.conceptopago');

?>

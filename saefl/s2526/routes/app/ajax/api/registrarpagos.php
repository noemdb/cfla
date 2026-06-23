<?php
/* modal create */

//Apis
Route::get('/ajax/list/api/registro_pagos/irregulars/{start}/{size}', 'Ajax\Api\RegistroPagoController@list_registro_pagos_irregulars')->name('administracion.ajax.api.registro_pagos.irregulars');

?>

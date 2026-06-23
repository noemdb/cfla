<?php
/* PDF */

Route::get('/registropagos/recibos/pagos/representant/{id}', 'PDF\RegistroPagoController@recibo_pago_representant')->name('representants.registropagos.recibos.pagos.representant.pdf');


?>

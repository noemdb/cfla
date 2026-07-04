<?php
/* PDF */

Route::get('/administrativas/bancos/libro/facturacion/pdf', 'PDF\BancoController@libro_facturacion')->name('administracion.configuraciones.banco.libro.facturacion');
Route::get('/administrativas/bancos/libro/facturacion/noasociados/pdf', 'PDF\BancoController@libro_facturacion_no_asociados')->name('administracion.configuraciones.banco.libro.facturacion.noasociados');



?>

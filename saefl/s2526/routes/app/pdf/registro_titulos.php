<?php
/* PDF */

Route::get('/registro_titulos/hoja_registro/pdf/{registro_titulo_id}', 'PDF\RegistroTituloController@hoja_registro')->name('administracion.registro_titulos.hoja_registro.pdf');
Route::get('/registro_titulos/constancia/promocion/pdf/{id}', 'PDF\RegistroTituloController@constanciaPromocion')->name('administracion.registro_titulos.constancia.promocion.pdf');
Route::get('/registro_titulos/constancia/promocion/pdf/lote/{id}', 'PDF\RegistroTituloController@constanciaPromocionLote')->name('administracion.registro_titulos.constancia.promocion.pdf.lote');

?>

<?php
/* PDF */

Route::get('/historico_notas/certificacion/pdf/{historico_nota_id}', 'PDF\HistoricoNotaController@certificacion')->name('administracion.historico_notas.certificacion.pdf');

?>

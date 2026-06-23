<?php
/* PDF */

Route::get('/boletin_revisions/boletin/resumen_revision/pdf/{seccion_id}', 'PDF\BoletinRevisionController@resumen_revision')->name('administracion.boletin_revisions.resumen_revision.pdf');

?>

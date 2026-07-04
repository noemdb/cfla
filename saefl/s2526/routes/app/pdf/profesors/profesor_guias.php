<?php
/* PDF */

Route::get('/profesor_guias/sabanafull/pdf/{grado_id}/{seccion_id}', 'PDF\ProfesorGuiaController@sabanafull')->name('profesors.profesor_guias.sabanafull.pdf');

?>

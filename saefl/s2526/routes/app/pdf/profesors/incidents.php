<?php
/* PDF */

Route::get('/incidents/main/pdf/{id}', 'PDF\IncidentController@ficha')->name('profesors.incidents.ficha.pdf');
Route::get('/incidents/pdf/ficha/estudiant/{estudiant_id}', 'PDF\IncidentController@fichaEstudiant')->name('profesors.incidents.pdf.ficha.estudiant');


?>

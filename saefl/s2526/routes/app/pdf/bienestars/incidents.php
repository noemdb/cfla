<?php
/* PDF */

Route::get('/incidents/pdf/ficha/{incident_id}', 'PDF\IncidentController@ficha')->name('bienestars.incidents.pdf.ficha');
Route::get('/incidents/pdf/ficha/estudiant/{estudiant_id}', 'PDF\IncidentController@fichaEstudiant')->name('bienestars.incidents.pdf.ficha.estudiant');
Route::get('/incidents/pdf/batch', 'PDF\IncidentController@batch')->name('bienestars.incidents.pdf.batch');


?>
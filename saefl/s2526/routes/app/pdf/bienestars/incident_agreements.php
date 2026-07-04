<?php
/* PDF */

Route::get('/incident_agreements/pdf/ficha/{incident_id}', 'PDF\IncidentAgreementController@ficha')->name('bienestars.incident_agreements.pdf.ficha');
Route::get('/incident_agreements/pdf/ficha/estudiant/{estudiant_id}', 'PDF\IncidentAgreementController@fichaEstudiant')->name('bienestars.incident_agreements.pdf.ficha.estudiant');
Route::get('/incident_agreements/pdf/batch', 'PDF\IncidentAgreementController@batch')->name('bienestars.incident_agreements.pdf.batch');


?>
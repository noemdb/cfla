<?php
/* PDF */

// Route::get('/estudiants/pdf/ficha/{incident_id}', 'PDF\EstudiantController@ficha')->name('bienestars.estudiants.pdf.ficha');
Route::get('/estudiants/pdf/ficha/estudiant/{estudiant_id}', 'PDF\EstudiantController@fichaEstudiant')->name('bienestars.estudiants.pdf.ficha');
Route::get('/estudiants/pdf/batch', 'PDF\EstudiantController@batch')->name('bienestars.estudiants.pdf.batch');


?>

<?php
/* PDF */

Route::get('/enrollments/pdf/ficha/{student_record_id}', 'PDF\EnrollmentController@ficha')->name('bienestars.enrollments.pdf.ficha');
Route::get('/enrollments/pdf/ficha/estudiant/{estudiant_id}', 'PDF\EnrollmentController@fichaEstudiant')->name('bienestars.enrollments.pdf.ficha.estudiant');
Route::get('/enrollments/pdf/batch', 'PDF\EnrollmentController@batch')->name('bienestars.enrollments.pdf.batch');


?>
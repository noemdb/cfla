<?php
/* PDF */
Route::get('/enrollments/pdf/payroll/{id}', 'PDF\EnrollmentController@payroll')->name('administracion.enrollments.pdf.payroll');
Route::get('/enrollments/pdf/individual/{id}', 'PDF\EnrollmentController@individual')->name('administracion.enrollments.pdf.individual');
Route::get('/enrollments/pdf/formato/simple', 'PDF\EnrollmentController@simple')->name('administracion.enrollments.pdf.simple');
Route::get('/enrollments/pdf/formatos/{grado_id}/{seccion_id}', 'PDF\EnrollmentController@formatos')->name('administracion.enrollments.pdf.formatos');



?>

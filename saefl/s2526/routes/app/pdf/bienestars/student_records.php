<?php
/* PDF */

Route::get('/student_records/pdf/ficha/{student_record_id}', 'PDF\StudentRecordController@ficha')->name('bienestars.student_records.pdf.ficha');
Route::get('/student_records/pdf/ficha/estudiant/{estudiant_id}', 'PDF\StudentRecordController@fichaEstudiant')->name('bienestars.student_records.pdf.ficha.estudiant');
Route::get('/student_records/pdf/batch', 'PDF\StudentRecordController@batch')->name('bienestars.student_records.pdf.batch');


?>
<?php
/* PDF */

Route::get('/interviews/pdf/estudiant/{estudiant_id}', 'PDF\InterviewController@forEstudiant')->name('bienestars.interviews.pdf.estudiante.resume');
Route::get('/interviews/pdf/profesor/{profesor_id}', 'PDF\InterviewController@forProfesor')->name('bienestars.interviews.pdf.profesor.resume');

?>

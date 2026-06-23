<?php

/* resource */

Route::get('/student_records', 'Tab\StudentRecordController@index')->name('bienestars.student_records.index');
Route::get('/student_records/batch', 'Tab\StudentRecordController@batch')->name('bienestars.student_records.batch');

Route::get('/student_records/summaries', 'Tab\StudentRecordController@summaries')->name('bienestars.student_records.summaries');

?>

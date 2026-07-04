<?php

/* resource */

Route::get('/enrollments', 'Tab\EnrollmentController@index')->name('bienestars.enrollments.index');
Route::get('/enrollments/batch', 'Tab\EnrollmentController@batch')->name('bienestars.enrollments.batch');

Route::get('/enrollments/summaries', 'Tab\EnrollmentController@summaries')->name('bienestars.enrollments.summaries');

?>

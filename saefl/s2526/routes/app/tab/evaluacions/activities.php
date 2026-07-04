<?php

/* resource */

Route::get('/activities/index', 'Tab\ActivityController@index')->name('evaluacions.activities.index');
Route::get('/activities/format/{id}', 'Tab\ActivityController@format')->name('evaluacions.activities.format');
Route::get('/activities/formats/grado/{grado_id}/{seccion_id}', 'Tab\ActivityController@formatForGrado')->name('evaluacions.activities.formats.grado');
Route::get('/activities/resume/{id}', 'Tab\ActivityController@resume')->name('evaluacions.activities.resume');


?>

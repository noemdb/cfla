<?php

/* resource */

Route::get('/estudiants/index', 'Tab\EstudiantController@index')->name('evaluacions.estudiants.index');
Route::get('/estudiants/format/{id}', 'Tab\EstudiantController@format')->name('evaluacions.estudiants.format');


?>

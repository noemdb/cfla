<?php

/* resource */

Route::get('/estudiants/index', 'Tab\EstudiantController@index')->name('bienestars.estudiants.index');
Route::get('/estudiants/crud', 'Tab\EstudiantController@crud')->name('bienestars.estudiants.crud');

?>

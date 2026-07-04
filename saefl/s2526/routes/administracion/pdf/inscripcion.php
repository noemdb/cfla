<?php
/* PDF */

Route::post('/inscripciones/list/pdf', 'PDF\InscripcionController@listpdf')->name('administracion.inscripciones.list.pdf');
Route::get('/inscripciones/constancia/inscripcion/pdf/{id}', 'PDF\InscripcionController@constanciapdf')->name('administracion.inscripciones.constancia.pdf');
Route::get('/inscripciones/constancia/estudio/pdf/{id}', 'PDF\InscripcionController@cestudiopdf')->name('administracion.inscripciones.constancia.estudio.pdf');
// Route::get('/inscripciones/create/{id}', 'Tab\InscripcionController@create')->name('administracion.inscripciones.create');


?>
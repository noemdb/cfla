<?php
/* PDF */

Route::post('/inscripciones/list/pdf', 'PDF\InscripcionController@listpdf')->name('administracion.inscripciones.list.pdf');

Route::get('/inscripciones/book/pdf', 'PDF\InscripcionController@book')->name('administracion.inscripciones.book.pdf');

Route::get('/inscripciones/constancia/inscripcion/pdf/{id}', 'PDF\InscripcionController@constanciapdf')->name('administracion.inscripciones.constancia.pdf');
Route::get('/inscripciones/constancia/estudio/pdf/{id}', 'PDF\InscripcionController@cestudiopdf')->name('administracion.inscripciones.constancia.estudio.pdf');
Route::get('/inscripciones/constancia/prosecucion/pdf/{id}', 'PDF\InscripcionController@prosecucion')->name('administracion.inscripciones.constancia.prosecucion.pdf');
Route::get('/inscripciones/constancia/prosecucion/pdf/lote/{grado_id}/{seccion_id}', 'PDF\InscripcionController@prosecucionLote')->name('administracion.inscripciones.constancia.prosecucion.pdf.lote');

Route::post('/inscripciones/matricula/inicial/pdf', 'PDF\InscripcionController@matricula_inicial')->name('administracion.inscripciones.matricula.pdf.inicial');





?>

<?php
/* PDF */

Route::post('/administrativas/list/pdf', 'PDF\AdministrativaController@listpdf')->name('administracion.administrativas.list.pdf');
Route::get('/administrativas/book/pdf', 'PDF\AdministrativaController@book')->name('administracion.administrativas.book.pdf');
//Route::get('/administrativas/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.administrativas.constancia.pdf');
Route::get('/administrativas/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.administrativas.constancia.estudio.pdf');
Route::get('/administrativas/solvencia/pdf/{id}', 'PDF\AdministrativaController@solvencia_pdf')->name('administracion.administrativas.solvencia.pdf');
Route::get('/administrativas/constancia/pdf/{id}', 'PDF\AdministrativaController@constancia_pdf')->name('administracion.administrativas.constancia.pdf');
// Route::get('/administrativas/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.administrativas.create');


?>

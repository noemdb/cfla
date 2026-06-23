<?php
/* PDF */

Route::get('/inscripcions/constancia/inscripcion/pdf/{id}', 'PDF\InscripcionController@constancia_inscripcions')->name('representants.constancia.inscripcions.pdf');
Route::get('/inscripcions/constancia/estudio/pdf/{id}', 'PDF\InscripcionController@constancia_estudio')->name('representants.inscripcions.constancia.estudio.pdf');
Route::get('/inscripcions/constancia/prosecucion/pdf/{id}', 'PDF\InscripcionController@constancia_prosecucion')->name('representants.inscripcions.constancia.prosecucion.pdf');
Route::get('/inscripcions/constancia/promocion/pdf/{id}', 'PDF\InscripcionController@constancia_promocion')->name('representants.inscripcions.constancia.promocion.pdf');


?>

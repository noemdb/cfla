<?php
/* PDF */
Route::get('/representants/libro/pdf', 'PDF\RepresentantController@libro')
	->name('administracion.representants.libro.pdf');
Route::get('/representants/recibo/pdf/{id}', 'PDF\RepresentantController@recibo')
	->name('administracion.representants.recibo.pdf');

?>
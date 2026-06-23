<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/inscripcion/gender', 'Chart\InscripcionController@gender')->name('administracion.inscripcion.gender.chart');
Route::get('charts/inscripcion/genderxplan', 'Chart\InscripcionController@genderxplan')->name('administracion.inscripcion.genderxplan.chart');
Route::get('charts/inscripcion/genderxgrado', 'Chart\InscripcionController@genderxgrado')->name('administracion.inscripcion.genderxgrado.chart');
Route::get('charts/inscripcion/time', 'Chart\InscripcionController@time')->name('administracion.inscripcion.time.chart');


?>
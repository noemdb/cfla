<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/administrativas/gender', 'Chart\AdministrativasController@gender')->name('administracion.administrativas.gender.chart');
Route::get('charts/administrativas/genderxplan', 'Chart\AdministrativasController@genderxplan')->name('administracion.administrativas.genderxplan.chart');
Route::get('charts/administrativas/genderxgrado', 'Chart\AdministrativasController@genderxgrado')->name('administracion.administrativas.genderxgrado.chart');
Route::get('charts/administrativas/time', 'Chart\AdministrativasController@time')->name('administracion.administrativas.time.chart');


?>

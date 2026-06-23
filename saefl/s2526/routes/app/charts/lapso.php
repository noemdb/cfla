<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/lapso/census/institution', 'Chart\LapsoController@census_institution')->name('administracion.lapso.census.chart.institution');
Route::get('charts/lapso/census/grado', 'Chart\LapsoController@census_grado')->name('administracion.lapso.census.chart.grado');
Route::get('charts/lapso/census/municipio', 'Chart\LapsoController@census_municipio')->name('administracion.lapso.census.chart.municipio');


?>
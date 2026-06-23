<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/estudiants/municipio', 'Chart\EstudiantController@estudiants_municipios')->name('administracion.estudiants.municipio.chart');
Route::get('charts/estudiants/municipio/pestudio', 'Chart\EstudiantController@estudiants_municipios_pestudio')->name('administracion.estudiants.municipio.pestudio.chart');

<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
Route::get('charts/control/estudiants/estudiants_municipios', 'Chart\EstudiantController@estudiants_municipios_extend')->name('bienestars.charts.control.estudiants.estudiants_municipios');
Route::get('charts/control/estudiants/estudiants_municipios_pestudio', 'Chart\EstudiantController@estudiants_municipios_pestudio_extend')->name('bienestars.charts.control.estudiants.estudiants_municipios_pestudio');
Route::get('charts/control/evaluacions/actividades', 'Chart\EvaluacionController@actividades_extend')->name('bienestars.charts.control.evaluacions.actividades');
Route::get('charts/control/inscripcions/gender', 'Chart\InscripcionController@gender_extend')->name('bienestars.charts.control.inscripcions.gender');
Route::get('charts/control/area_conocimientos/promedio_x_area', 'Chart\AreaConocimientoController@promedio_x_area_extend')->name('bienestars.charts.control.area_conocimientos.promedio_x_area');

?>

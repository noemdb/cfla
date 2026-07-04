<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
/*control.evaluacions*/
Route::get('representants/charts/control/evaluacions/actividades', 'Chart\EvaluacionController@actividades')->name('representants.charts.control.evaluacions.actividades');

// /*admon.bancos*/
// Route::get('charts/admon/bancos/ingresoxmonth', 'Chart\BancosController@ingreso_month_extend')->name('representants.charts.admon.bancos.all_ingresoxmonth');
// Route::get('charts/admon/bancos/deuda_representate_concepto', 'Chart\BancosController@deuda_representate_concepto_extend')->name('representants.charts.admon.bancos.deuda_representate_concepto');

// /*control.estudiants*/
// Route::get('charts/control/estudiants/estudiants_municipios', 'Chart\EstudiantController@estudiants_municipios_extend')->name('representants.charts.control.estudiants.estudiants_municipios');
// Route::get('charts/control/estudiants/estudiants_municipios_pestudio', 'Chart\EstudiantController@estudiants_municipios_pestudio_extend')->name('representants.charts.control.estudiants.estudiants_municipios_pestudio');


// /*control.inscripcions*/
// Route::get('charts/control/inscripcions/gender', 'Chart\InscripcionController@gender_extend')->name('representants.charts.control.inscripcions.gender');
// Route::get('charts/control/inscripcions/genderxplan', 'Chart\InscripcionController@genderxplan_extend')->name('representants.charts.control.inscripcions.genderxplan');
// Route::get('charts/control/inscripcions/genderxgrado', 'Chart\InscripcionController@genderxgrado_extend')->name('representants.charts.control.inscripcions.genderxgrado');

// /*control.area_conocimientos*/
// Route::get('charts/control/area_conocimientos/promedio_x_area', 'Chart\AreaConocimientoController@promedio_x_area_extend')->name('representants.charts.control.area_conocimientos.promedio_x_area');

?>

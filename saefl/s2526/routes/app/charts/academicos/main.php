<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/*control.estudiants*/
Route::get('charts/control/estudiants/estudiants_municipios', 'Chart\EstudiantController@estudiants_municipios_extend')->name('academicos.charts.control.estudiants.estudiants_municipios');
Route::get('charts/control/estudiants/estudiants_municipios_pestudio', 'Chart\EstudiantController@estudiants_municipios_pestudio_extend')->name('academicos.charts.control.estudiants.estudiants_municipios_pestudio');

/*control.evaluacions*/
Route::get('charts/control/evaluacions/actividades', 'Chart\EvaluacionController@actividades_extend')->name('academicos.charts.control.evaluacions.actividades');

/*control.inscripcions*/
Route::get('charts/control/inscripcions/gender', 'Chart\InscripcionController@gender_extend')->name('academicos.charts.control.inscripcions.gender');
Route::get('charts/control/inscripcions/genderxplan', 'Chart\InscripcionController@genderxplan_extend')->name('academicos.charts.control.inscripcions.genderxplan');
Route::get('charts/control/inscripcions/genderxgrado', 'Chart\InscripcionController@genderxgrado_extend')->name('academicos.charts.control.inscripcions.genderxgrado');

/*control.area_conocimientos*/
Route::get('charts/control/area_conocimientos/promedio_x_area', 'Chart\AreaConocimientoController@promedio_x_area_extend')->name('academicos.charts.control.area_conocimientos.promedio_x_area');

/*audits*/
Route::get('charts/audits/usages/usersxaccess', 'Chart\LoginoutsController@loginouts_users_extend')->name('academicos.charts.audits.usages.usersxaccess');
Route::get('charts/audits/usages/loginouts_months', 'Chart\LoginoutsController@loginouts_months_extend')->name('academicos.charts.audits.usages.loginoutsmonths');
Route::get('charts/audits/usages/loginoutsrols', 'Chart\LoginoutsController@loginouts_rols_extend')->name('academicos.charts.audits.usages.loginoutsrols');

Route::get('charts/audits/usages/logdbsusers', 'Chart\LogdbsController@logdbs_users_extend')->name('academicos.charts.audits.usages.logdbsusers');
Route::get('charts/audits/usages/logdbsmonths', 'Chart\LogdbsController@logdbs_months_extend')->name('academicos.charts.audits.usages.logdbsmonths');
Route::get('charts/audits/usages/logdbsrols', 'Chart\LogdbsController@logdbs_rols_extend')->name('academicos.charts.audits.usages.logdbsrols');

Route::get('charts/control/timeline', 'Chart\PollController@timeline')->name('academicos.polls.timeline.chart');
Route::get('charts/control/general', 'Chart\PollController@general')->name('academicos.polls.general.chart');
Route::get('charts/control/question', 'Chart\PollController@question')->name('academicos.polls.question.chart');

?>

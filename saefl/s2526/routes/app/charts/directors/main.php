<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/*admon.bancos*/
Route::get('charts/admon/bancos/ingresoxmonth', 'Chart\BancosController@ingreso_month_extend')->name('directors.charts.admon.bancos.all_ingresoxmonth');
Route::get('charts/admon/bancos/deuda_representate_concepto', 'Chart\BancosController@deuda_representate_concepto_extend')->name('directors.charts.admon.bancos.deuda_representante_concepto');
Route::get('charts/admon/bancos/ingresoxmetodo', 'Chart\BancosController@ingreso_metodo_extend')->name('directors.charts.admon.bancos.ingresoxmetodo');

/*Registro de Pago*/
Route::get('charts/admon/registropagos/actividades', 'Chart\RegistroPagosController@actividades_extend')->name('directors.charts.admon.bancos.actividades.chart');

/*admon.ExchangeRate*/
Route::get('charts/admon/exchangerates/fluctuations', 'Chart\ExchangeRateController@fluctuations_extend')->name('directors.charts.admon.exchangerates.fluctuations');

/*admon.payments*/
Route::get('charts/admon/payments/countxday', 'Chart\PaymentController@countxday_extend')->name('directors.charts.admon.payments.countxday');

/*control.estudiants*/
Route::get('charts/control/estudiants/estudiants_municipios', 'Chart\EstudiantController@estudiants_municipios_extend')->name('directors.charts.control.estudiants.estudiants_municipios');
Route::get('charts/control/estudiants/estudiants_municipios_pestudio', 'Chart\EstudiantController@estudiants_municipios_pestudio_extend')->name('directors.charts.control.estudiants.estudiants_municipios_pestudio');

/*control.evaluacions*/
Route::get('charts/control/evaluacions/actividades', 'Chart\EvaluacionController@actividades_extend')->name('directors.charts.control.evaluacions.actividades');

/*control.inscripcions*/
Route::get('charts/control/inscripcions/gender', 'Chart\InscripcionController@gender_extend')->name('directors.charts.control.inscripcions.gender');
Route::get('charts/control/inscripcions/genderxplan', 'Chart\InscripcionController@genderxplan_extend')->name('directors.charts.control.inscripcions.genderxplan');
Route::get('charts/control/inscripcions/genderxgrado', 'Chart\InscripcionController@genderxgrado_extend')->name('directors.charts.control.inscripcions.genderxgrado');

/*control.area_conocimientos*/
Route::get('charts/control/area_conocimientos/promedio_x_area', 'Chart\AreaConocimientoController@promedio_x_area_extend')->name('directors.charts.control.area_conocimientos.promedio_x_area');

/*audits*/
Route::get('charts/audits/usages/usersxaccess', 'Chart\LoginoutsController@loginouts_users_extend')->name('directors.charts.audits.usages.usersxaccess');
Route::get('charts/audits/usages/loginouts_months', 'Chart\LoginoutsController@loginouts_months_extend')->name('directors.charts.audits.usages.loginoutsmonths');
Route::get('charts/audits/usages/loginoutsrols', 'Chart\LoginoutsController@loginouts_rols_extend')->name('directors.charts.audits.usages.loginoutsrols');

Route::get('charts/audits/usages/logdbsusers', 'Chart\LogdbsController@logdbs_users_extend')->name('directors.charts.audits.usages.logdbsusers');
Route::get('charts/audits/usages/logdbsmonths', 'Chart\LogdbsController@logdbs_months_extend')->name('directors.charts.audits.usages.logdbsmonths');
Route::get('charts/audits/usages/logdbsrols', 'Chart\LogdbsController@logdbs_rols_extend')->name('directors.charts.audits.usages.logdbsrols');
Route::get('charts/audits/usages/logdbsrols', 'Chart\LogdbsController@logdbs_rols_extend')->name('directors.charts.audits.usages.logdbsrols');

?>

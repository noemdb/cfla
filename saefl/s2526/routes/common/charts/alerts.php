<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/alerts', 'Chart\AlertsController@index')->name('alerts.chart');
Route::get('models/charts/alertsmonths', 'Chart\AlertsController@AlertsMonth')->name('alerts.months.chart');
Route::get('models/charts/alertsusers', 'Chart\AlertsController@AlertsUsers')->name('alerts.users.chart');
Route::get('models/charts/alertstypes', 'Chart\AlertsController@AlertsTypes')->name('alerts.types.chart');

?>
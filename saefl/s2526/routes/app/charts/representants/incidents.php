<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/representants/incidents', 'Chart\IncidentController@month')->name('representants.incidents.month.chart');
?>

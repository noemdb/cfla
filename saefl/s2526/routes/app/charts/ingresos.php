<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/ingresos/ingreso/day', 'Chart\IngresosController@IngresosXDay')->name('administracion.ingresos.ingresoxday.chart');
Route::get('charts/ingresos/ingreso/day/date', 'Chart\IngresosController@IngresosXDayDate')->name('administracion.ingresos.ingreso.day.date.chart');

?>

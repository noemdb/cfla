<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/area_conocimientos/promedio_x_area', 'Chart\AreaConocimientoController@promedio_x_area')->name('administracion.area_conocimientos.promedio_x_area.chart');


?>

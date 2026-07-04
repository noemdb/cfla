<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/bmessenges/options', 'Chart\BmessengeController@options')->name('administracion.bmessenges.charts.options');

?>

<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/representants/ingresoxmonth', 'Chart\BancosController@IngresoXMonth')->name('representants.bancos.ingresoxmonth.chart');
?>

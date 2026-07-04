<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/exchange_rates/movimientocambiario', 'Chart\ExchangeRateController@movimientocambiario')->name('administracion.configuraciones.exchange_rates.movimientocambiario.chart');
?>

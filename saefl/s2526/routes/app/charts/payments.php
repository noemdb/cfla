<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/payments/countxday', 'Chart\PaymentController@countxday')->name('administracion.payments.charts.countxday');

?>

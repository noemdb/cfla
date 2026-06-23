<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas tab
|
*/

Route::get('/configuraciones/dashboard', 'Tab\Configuracion\HomeController@dashboard')->name('administracion.configuraciones.dashboard');

//portal web
require __DIR__ . '/tab/blog.php';

//portal debate
require __DIR__ . '/tab/debate.php';

//diagnostico
require __DIR__ . '/tab/diagnostics.php';

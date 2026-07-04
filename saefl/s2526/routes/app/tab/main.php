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


Route::get('/pagos_adelantados/index', 'Tab\PagosAdelantadosController@index')->name('administracion.pagos_adelantados.index');
Route::get('/registro_pagos/index', 'Tab\RegistroPagosController@index')->name('administracion.registro_pagos.index');
Route::get('/cuentas_cobrar/index', 'Tab\CuentasCobrarController@index')->name('administracion.cuentas_cobrar.index');
Route::get('/operaciones_bancos/index', 'Tab\OperacionesBancosController@index')->name('administracion.operaciones_bancos.index');

//institucion
require (__DIR__ . '/institucion.php');

//autoridad
require (__DIR__ . '/autoridad.php');

//banco
require (__DIR__ . '/banco.php');

//estudiantes
// require (__DIR__ . '/estudiantes.php');

//representantes
// require (__DIR__ . '/representantes.php');

//inscripciones
require (__DIR__ . '/inscripcion.php');

//planpagos
require (__DIR__ . '/planpagos.php');

//concepto_pagos
require (__DIR__ . '/cuentaxpagars.php');

//concepto_pagos
require (__DIR__ . '/concepto_pagos.php');

//registrarpagos
require (__DIR__ . '/registrarpagos.php');

//Programas Educativo - peducativo
require (__DIR__ . '/peducativo.php');

//Plan de Estudio pestudio
require (__DIR__ . '/pestudio.php');

//Grados grado
require (__DIR__ . '/grado.php');

//Secciones seccion
require (__DIR__ . '/seccion.php');

//Secciones mailers
require (__DIR__ . '/mailers.php');

//ajax
require (__DIR__ . '/ajax.php');


 ?>

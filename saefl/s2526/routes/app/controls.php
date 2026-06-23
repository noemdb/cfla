<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas tab
|
*/

//ruta temporal esta faltando definir los permisos de los roles

//administracions
// require (__DIR__ . '/tab/controls/administrations.php');
// require (__DIR__ . '/indicators/controls/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//controls
require (__DIR__ . '/tab/controls/controls.php');
// require (__DIR__ . '/indicators/controls/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/evaluacions.php');//generacion de pdf's

require (__DIR__ . '/tab/controls/coll_politicals.php');

//audits
require (__DIR__ . '/tab/controls/audits.php');

require (__DIR__ . '/charts/controls/main.php');//generacion de gráficas

require (__DIR__ . '/tab/controls/users.php');


?>

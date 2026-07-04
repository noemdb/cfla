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
require (__DIR__ . '/tab/directors/administrations.php');
// require (__DIR__ . '/indicators/directors/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//controls
require (__DIR__ . '/tab/directors/controls.php');
// require (__DIR__ . '/indicators/directors/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/evaluacions.php');//generacion de pdf's

require (__DIR__ . '/tab/directors/coll_politicals.php');

//audits
require (__DIR__ . '/tab/directors/audits.php');

require (__DIR__ . '/charts/directors/main.php');//generacion de gráficas

require (__DIR__ . '/tab/directors/users.php');

require (__DIR__ . '/tab/directors/polls.php');


?>

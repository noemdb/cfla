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
// require (__DIR__ . '/tab/evaluacions/administrations.php');
// require (__DIR__ . '/indicators/evaluacions/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//learnings
require (__DIR__ . '/tab/inicials/home.php');
require (__DIR__ . '/tab/inicials/eiplanningwks.php');
require (__DIR__ . '/tab/inicials/eiplanningbwks.php');
require (__DIR__ . '/tab/inicials/eiprojectks.php');
require (__DIR__ . '/tab/inicials/eispecialks.php');
require (__DIR__ . '/tab/inicials/eievaluationks.php');
require (__DIR__ . '/tab/inicials/eifinalks.php');

//audits
// require (__DIR__ . '/tab/evaluacions/audits.php');
// require (__DIR__ . '/charts/evaluacions/main.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

//Enviador de correos masivos
// require (__DIR__ . '/tab/evaluacions/mailers.php');
// require (__DIR__ . '/tab/evaluacions/polls.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

?>

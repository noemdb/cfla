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
require (__DIR__ . '/tab/leaders/learnings.php');
require (__DIR__ . '/charts/leaders/learnings.php');

//activities
require (__DIR__ . '/tab/leaders/activities.php');

//activities
require (__DIR__ . '/tab/leaders/profesors.php');

//audits
// require (__DIR__ . '/tab/evaluacions/audits.php');
// require (__DIR__ . '/charts/evaluacions/main.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

//Enviador de correos masivos
// require (__DIR__ . '/tab/evaluacions/mailers.php');
// require (__DIR__ . '/tab/evaluacions/polls.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

//competitions
require (__DIR__ . '/tab/leaders/competitions.php');
require (__DIR__ . '/pdf/leaders/competitions.php');

?>

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

//controls
require (__DIR__ . '/tab/evaluacions/controls.php');

//permissions
require (__DIR__ . '/tab/evaluacions/permissions.php');

//assistcontrols
require (__DIR__ . '/tab/evaluacions/assistcontrols.php');

//pevaluacions
require (__DIR__ . '/tab/evaluacions/pevaluacions.php');

//profesors
require (__DIR__ . '/tab/evaluacions/profesors.php');

//activities
require (__DIR__ . '/tab/evaluacions/activities.php');
//require (__DIR__ . '/charts/profesors/activities.php');//generacion de graficas

//estudiants
require (__DIR__ . '/tab/evaluacions/estudiants.php');

//leaders
require (__DIR__ . '/tab/evaluacions/leaders.php');

//charts
require (__DIR__ . '/charts/evaluacions/main.php');

require (__DIR__ . '/tab/evaluacions/debates.php');

require (__DIR__ . '/tab/evaluacions/inicials.php');

require (__DIR__ . '/tab/evaluacions/peducativos.php');

require (__DIR__ . '/tab/evaluacions/competitions.php');
require (__DIR__ . '/pdf/evaluacions/competitions.php');


require (__DIR__ . '/tab/evaluacions/diagnostics.php');


// require (__DIR__ . '/tab/evaluacions/audits.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

//Enviador de correos masivos
// require (__DIR__ . '/tab/evaluacions/mailers.php');
// require (__DIR__ . '/tab/evaluacions/polls.php');
// require (__DIR__ . '/tab/evaluacions/users.php');

//home/nuser/code/s2526/routes/app/tab/evaluacions/diagnostics.php

?>

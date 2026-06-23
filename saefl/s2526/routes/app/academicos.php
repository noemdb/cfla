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
// require (__DIR__ . '/tab/academicos/administrations.php');
// require (__DIR__ . '/indicators/academicos/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//academicos
require (__DIR__ . '/tab/academicos/controls.php');

//audits
require (__DIR__ . '/tab/academicos/audits.php');

require (__DIR__ . '/charts/academicos/main.php'); //generacion de gráficas

require (__DIR__ . '/tab/academicos/users.php');

//Enviador de correos masivos
require (__DIR__ . '/tab/academicos/mailers.php');

//Gestión de polls
require (__DIR__ . '/tab/academicos/polls.php');


//Gestión de polls
require (__DIR__ . '/tab/academicos/lessons.php');

//Gestión de inicials
require (__DIR__ . '/tab/academicos/inicials.php');

//Gestión de users
require (__DIR__ . '/tab/academicos/users.php');

//manager_registers
require (__DIR__ . '/tab/academicos/manager_registers.php');


//Gestión de activities
require (__DIR__ . '/tab/academicos/activities.php');

//Gestión de diagnostics
require (__DIR__ . '/tab/academicos/diagnostics.php');
?>

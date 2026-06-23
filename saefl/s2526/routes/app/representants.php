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
require (__DIR__ . '/tab/representants/administrations.php');
require (__DIR__ . '/charts/representants/bancos.php');//generacion de graficas
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//controls
require (__DIR__ . '/tab/representants/controls.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/evaluacions.php');//generacion de pdf's

//inscripcions
require (__DIR__ . '/tab/representants/inscripcions.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
require (__DIR__ . '/pdf/representants/inscripcions.php');//generacion de pdf's

//preinscripcions
require (__DIR__ . '/tab/representants/preinscripcions.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/evaluacions.php');//generacion de pdf's

//prepagos
require (__DIR__ . '/tab/representants/prepagos.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/evaluacions.php');//generacion de pdf's

//registropagos
require (__DIR__ . '/tab/representants/registropagos.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
require (__DIR__ . '/pdf/representants/registropagos.php');//generacion de pdf's

//boletins
require (__DIR__ . '/tab/representants/boletins.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
require (__DIR__ . '/pdf/representants/boletins.php');//generacion de pdf's

//boletins
require (__DIR__ . '/tab/representants/ayudas.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/representants/ayudas.php');//generacion de pdf's

//User
require (__DIR__ . '/tab/representants/users.php');
// require (__DIR__ . '/indicators/representants/administracions.php');//generacion de indicadores
// require (__DIR__ . '/pdf/representants/ayudas.php');//generacion de pdf's

//Incident
require (__DIR__ . '/charts/representants/incidents.php');

?>

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

//pevaluacions
require (__DIR__ . '/tab/profesors/pevaluacions.php');

//profesor_gestables
require (__DIR__ . '/tab/profesors/profesor_gestables.php');

//evaluacion_gestables
require (__DIR__ . '/tab/profesors/evaluacion_gestables.php');

//evaluacions
require (__DIR__ . '/tab/profesors/evaluacions.php');
require (__DIR__ . '/charts/profesors/evaluacions.php');//generacion de graficas

//activities
require (__DIR__ . '/tab/profesors/activities.php');
//require (__DIR__ . '/charts/profesors/activities.php');//generacion de graficas

//profesor_guias
require (__DIR__ . '/tab/profesors/profesor_guias.php');
require (__DIR__ . '/pdf/profesors/profesor_guias.php');//generacion de pdf's

//estudiants
require (__DIR__ . '/tab/profesors/estudiants.php');
require (__DIR__ . '/pdf/profesors/estudiants.php');//generacion de pdf's

//profesor_guias
require (__DIR__ . '/tab/profesors/representants.php');
//require (__DIR__ . '/pdf/profesors/representants.php');//generacion de pdf's

//census
require (__DIR__ . '/tab/profesors/census.php');
//require (__DIR__ . '/pdf/profesors/census.php');//generacion de pdf's

//edescriptivas
require (__DIR__ . '/tab/profesors/edescriptivas.php');
require (__DIR__ . '/pdf/profesors/edescriptivas.php');//generacion de pdf's
require (__DIR__ . '/ajax/profesors/modal/edescriptivas.php'); //generacion de ajax

//boletins
require (__DIR__ . '/tab/profesors/boletins.php');
require (__DIR__ . '/pdf/profesors/boletins.php');//generacion de pdf's
// require (__DIR__ . '/xls/profesors/boletins.php');//generacion de xls
require (__DIR__ . '/ajax/profesors/modal/boletins.php'); //generacion de ajax

//competitions
require (__DIR__ . '/tab/profesors/competitions.php');

//debates
require (__DIR__ . '/tab/profesors/debates.php');


//diagnostics
require (__DIR__ . '/tab/profesors/diagnostics.php');

//boletins
require (__DIR__ . '/tab/profesors/incidents.php');
require (__DIR__ . '/pdf/profesors/incidents.php');//generacion de pdf's

//boletins
require (__DIR__ . '/tab/profesors/social_actions.php');
require (__DIR__ . '/pdf/profesors/social_actions.php');//generacion de pdf's

//users
require (__DIR__ . '/tab/profesors/users.php');

?>

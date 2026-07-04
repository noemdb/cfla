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

//institucion
require (__DIR__ . '/tab/institucion.php');

//autoridad
require (__DIR__ . '/tab/autoridad.php');

//estudiantes
require (__DIR__ . '/tab/estudiants.php');
require (__DIR__ . '/xls/estudiants.php'); //generacion xls
require (__DIR__ . '/charts/estudiants.php'); // charts
require (__DIR__ . '/pdf/estudiants.php'); //generacion de pdf's
require (__DIR__ . '/ajax/modal/estudiants.php'); //generacion de ajax

//representantes
require (__DIR__ . '/tab/representantes.php');
require (__DIR__ . '/xls/representantes.php');//generacion xls
require (__DIR__ . '/pdf/representantes.php');//generacion de pdf's
require (__DIR__ . '/ajax/modal/representantes.php');//generacion de xls's

//collection_polices - Políticas de Cobranza
require (__DIR__ . '/tab/collection_polices.php');

//sync_datas
require (__DIR__ . '/tab/sync_datas.php');

//retiros seccion
require (__DIR__ . '/tab/retiro.php');

//ajax
require (__DIR__ . '/tab/ajax.php');

//retiros seccion
require (__DIR__ . '/tab/retiro.php');


//bmains
require (__DIR__ . '/tab/bmains.php');
//require (__DIR__ . '/charts/bmains.php');

require (__DIR__ . '/tab/boptions.php');
//require (__DIR__ . '/charts/boptions.php');

require (__DIR__ . '/tab/bmesseges.php');
require (__DIR__ . '/charts/bmesseges.php');

//Secciones mailers
require (__DIR__ . '/tab/mailers.php');

//Secciones polls
require (__DIR__ . '/tab/polls.php');
require (__DIR__ . '/charts/polls.php');

require (__DIR__ . '/tab/enrollments.php');
require (__DIR__ . '/pdf/enrollments.php');

require (__DIR__ . '/tab/iterrogations.php');

//Secciones Matriculación
require (__DIR__ . '/tab/catchments.php');
require (__DIR__ . '/pdf/catchments.php');//generacion de pdf's

//Secciones baremos.php
require (__DIR__ . '/tab/baremos.php');

?>

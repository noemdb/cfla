<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas tab
|
*/
//inscripciones (tab,pdf's,xls's,chart's)
require __DIR__ . '/tab/inscripcion.php';
require __DIR__ . '/xls/inscripcion.php';
require __DIR__ . '/charts/inscripcion.php';     //generacion de graficas
require __DIR__ . '/pdf/inscripcion.php';        //generacion de pdf's
require __DIR__ . '/ajax/api/inscripcion.php';   //generacion de ajax
require __DIR__ . '/ajax/modal/inscripcion.php'; //fill modal

//inscripciones (tab,pdf's,xls's,chart's)
// require (__DIR__ . '/tab/enrollments.php');
// require (__DIR__ . '/pdf/enrollments.php');

//materia_pendientes (tab,pdf's,xls's,chart's)
require __DIR__ . '/tab/materia_pendientes.php';

//Plan de Estudio pestudio
require __DIR__ . '/tab/pestudio.php';

//Programas Educativo - peducativo
require __DIR__ . '/tab/peducativo.php';

//Grados grado
require __DIR__ . '/tab/grado.php';

//Secciones seccion
require __DIR__ . '/tab/seccion.php';

//Lapsos lapsos
require __DIR__ . '/tab/lapso.php';
require __DIR__ . '/charts/lapso.php'; //generacion de graficas

//retiros asignaturas
require __DIR__ . '/tab/asignaturas.php';

//retiros grupo_estables
require __DIR__ . '/tab/grupo_estables.php';

//retiros profesors
require __DIR__ . '/tab/profesors.php';
require __DIR__ . '/xls/profesors.php';
require __DIR__ . '/charts/evaluacions.php'; //generacion de graficas

//retiros pensums
require __DIR__ . '/tab/pensums.php';
require __DIR__ . '/pdf/pensums.php'; //generacion de pdf's

//retiros boletins
require __DIR__ . '/tab/boletins.php';
require __DIR__ . '/pdf/boletins.php'; //generacion de pdf's
require __DIR__ . '/xls/boletins.php';
require __DIR__ . '/xls/boletin_ajustes.php';

//retiros boletin_revisions
require __DIR__ . '/tab/boletin_revisions.php';
require __DIR__ . '/pdf/boletin_revisions.php'; //generacion de pdf's

//edescriptivas
require __DIR__ . '/tab/edescriptivas.php';
require __DIR__ . '/pdf/edescriptivas.php';        //generacion de pdf's
require __DIR__ . '/ajax/modal/edescriptivas.php'; //generacion de ajax

//retiros pevaluacions
require __DIR__ . '/tab/pevaluacions.php';
require __DIR__ . '/xls/pevaluacions.php';
// require (__DIR__ . '/pdf/pevaluacions.php');//generacion de pdf's

//profesor_gestables
require __DIR__ . '/tab/profesor_gestables.php';

//evaluacion_gestables
require __DIR__ . '/tab/evaluacion_gestables.php';

//retiros evalaucions
require __DIR__ . '/tab/evaluacions.php';
require __DIR__ . '/xls/evaluacions.php';
// require (__DIR__ . '/pdf/evalaucions.php');//generacion de pdf's

//historico notas
require __DIR__ . '/tab/historico_notas.php';
require __DIR__ . '/pdf/historico_notas.php'; //generacion de pdf's

//registro_titulos
require __DIR__ . '/tab/registro_titulos.php';
require __DIR__ . '/pdf/registro_titulos.php'; //generacion de pdf's
require __DIR__ . '/tab/titulos.php';
require __DIR__ . '/pdf/titulos.php'; //generacion de pdf's

//retiros grupo_estables
require __DIR__ . '/tab/oinstitucions.php';

//retiros profesor_guias
require __DIR__ . '/tab/profesor_guias.php';
// require (__DIR__ . '/pdf/profesor_guias.php');//generacion de pdf's

//Areas de Conocimientos area_conocimientos
require __DIR__ . '/tab/area_conocimientos.php';
require __DIR__ . '/charts/area_conocimientos.php'; //generacion de graficas

//Campos de Conocimientos campo_conocimientos
require __DIR__ . '/tab/campo_conocimientos.php';

//retiros preinscripcions
require __DIR__ . '/tab/preinscripcions.php';
require __DIR__ . '/pdf/preinscripcions.php';    //generacion de pdf's
require __DIR__ . '/charts/preinscripcions.php'; //generacion de graficas

//Secciones mailers
// require (__DIR__ . '/tab/mailers.php');

//Secciones bienstars
require __DIR__ . '/tab/bienestars.php';
require __DIR__ . '/pdf/bienestars.php'; //generacion de pdf's

//Secciones bienstars
require __DIR__ . '/tab/social_actions.php';
require __DIR__ . '/pdf/social_actions.php'; //generacion de pdf's

//reservadas

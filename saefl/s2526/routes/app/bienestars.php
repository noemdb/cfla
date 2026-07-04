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

//enrollments
require(__DIR__ . '/tab/bienestars/catchments.php');
// require (__DIR__ . '/pdf/bienestars/catchments.php');

//enrollments
require(__DIR__ . '/tab/bienestars/enrollments.php');
require(__DIR__ . '/pdf/bienestars/enrollments.php');

//enrollments
require(__DIR__ . '/tab/bienestars/incident_descriptions.php');
// require (__DIR__ . '/pdf/bienestars/incident_descriptions.php');

//student_records
require(__DIR__ . '/tab/bienestars/student_records.php');
require(__DIR__ . '/pdf/bienestars/student_records.php');
// require (__DIR__ . '/tab/bienestars/student_records.php');//generacion de indicadores


//incident_records
require(__DIR__ . '/tab/bienestars/incidents.php');
require(__DIR__ . '/pdf/bienestars/incidents.php');
// require (__DIR__ . '/tab/bienestars/incident_records.php');//generacion de indicadores

//incident_records
require(__DIR__ . '/tab/bienestars/incident_agreements.php');
require(__DIR__ . '/pdf/bienestars/incident_agreements.php');
// require (__DIR__ . '/tab/bienestars/incident_records.php');//generacion de indicadores

//estudiants
require(__DIR__ . '/tab/bienestars/estudiants.php');
require(__DIR__ . '/pdf/bienestars/estudiants.php');


//estudiants
require(__DIR__ . '/tab/bienestars/interviews.php');
require(__DIR__ . '/pdf/bienestars/interviews.php');

//helps
require(__DIR__ . '/tab/bienestars/helps.php');



require(__DIR__ . '/charts/bienestars/main.php'); //generacion de gráficas


require(__DIR__ . '/tab/bienestars/resend.php');

require(__DIR__ . '/tab/bienestars/activities.php');

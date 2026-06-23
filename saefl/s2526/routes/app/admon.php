<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas admon
|
*/
//banco (tab,pdf's,xls's,chart's)
require(__DIR__ . '/tab/banco.php');
require(__DIR__ . '/charts/banco.php'); //generacion de graficas
require(__DIR__ . '/pdf/banco.php'); //generacion de pdf's

//planpagos
require(__DIR__ . '/tab/planpagos.php');



//exchange_rates
require(__DIR__ . '/tab/exchange_rates.php');
require(__DIR__ . '/charts/exchange_rates.php');

//cuentaxpagars
require(__DIR__ . '/tab/cuentaxpagars.php');
require(__DIR__ . '/ajax/cuentaxpagars.php');
require(__DIR__ . '/xls/cuentaxpagars.php'); //generacion de xls's

//concepto_pagos
require(__DIR__ . '/tab/concepto_pagos.php'); //tap
require(__DIR__ . '/ajax/concepto_pagos.php');

//registrarpagos
require(__DIR__ . '/tab/registrarpagos.php');
require(__DIR__ . '/pdf/registrarpagos.php'); //generacion de pdf's
require(__DIR__ . '/xls/registrarpagos.php'); //generacion de xls's
require(__DIR__ . '/ajax/api/registrarpagos.php'); //generacion de ajax
require(__DIR__ . '/ajax/modal/registrarpagos.php'); //generacion de fill modal
require(__DIR__ . '/charts/registropagos.php');
require(__DIR__ . '/email/registropago/ticket.php'); //app/email/registropago/ticket

//prepagos
require(__DIR__ . '/tab/prepagos.php');
require(__DIR__ . '/tab/mbancarios.php');
require(__DIR__ . '/ajax/modal/prepagos.php'); //generacion de fill modal

//payments
require(__DIR__ . '/tab/payments.php');
require(__DIR__ . '/charts/payments.php');
// require (__DIR__ . '/tab/mbancarios.php');
// require (__DIR__ . '/ajax/modal/payments.php');//generacion de fill modal

//abonos
require(__DIR__ . '/tab/abonos.php');
require(__DIR__ . '/xls/abonos.php'); //generacion de xls's

//credito_a_favors
require(__DIR__ . '/tab/credito_a_favors.php');
require(__DIR__ . '/pdf/credito_a_favors.php'); //generacion de pdf's
require(__DIR__ . '/xls/credito_a_favors.php'); //generacion de xls's
require(__DIR__ . '/ajax/modal/credito_a_favors.php'); //generacion de xls's

//ingresos
require(__DIR__ . '/tab/ingresos.php');
require(__DIR__ . '/pdf/ingresos.php'); //generacion de pdf's
require(__DIR__ . '/xls/ingresos.php'); //generacion de xls's
require(__DIR__ . '/charts/ingresos.php');
require(__DIR__ . '/ajax/fillPartials/ingresos.php'); //generacion de xls's

//deudas_anterior
require(__DIR__ . '/tab/deudas_anterior.php');

//plan_beneficos
require(__DIR__ . '/tab/plan_beneficos.php');

//descuentos
require(__DIR__ . '/tab/descuentos.php');

//refunds
require(__DIR__ . '/tab/refunds.php');

//libros
require(__DIR__ . '/tab/libros.php');

//administrativas (tab,pdf's,xls's,chart's)
require(__DIR__ . '/tab/administrativas.php');
require(__DIR__ . '/charts/administrativas.php'); //generacion de graficas
require(__DIR__ . '/pdf/administrativas.php'); //generacion de pdf's
require(__DIR__ . '/xls/administrativas.php'); //generacion de xls's
require(__DIR__ . '/ajax/api/administrativas.php'); //generacion de ajax

//isrl
require(__DIR__ . '/tab/isrl.php');

//collection_politicals
require(__DIR__ . '/pdf/collection_politicals.php');
require(__DIR__ . '/tab/collection_politicals/coll_politicals.php');
require(__DIR__ . '/email/collection_politicals/coll_politicals.php');

//coll_nivels
require(__DIR__ . '/tab/collection_politicals/coll_nivels.php');
require(__DIR__ . '/email/collection_politicals/coll_nivels.php');

//coll_activities
require(__DIR__ . '/tab/collection_politicals/coll_activities.php');

//coll_debtors
require(__DIR__ . '/tab/collection_politicals/coll_debtors.php');

//coll_promises
require(__DIR__ . '/tab/collection_politicals/coll_promises.php');

//coll_messeges
require(__DIR__ . '/tab/collection_politicals/coll_messeges.php');
require(__DIR__ . '/email/collection_politicals/coll_messeges.php');


//isrl
require(__DIR__ . '/tab/receibts/main.php');
require(__DIR__ . '/pdf/receibts/main.php');


//assit_attendances
require(__DIR__ . '/tab/asisst_controls/assit_attendances.php');
require(__DIR__ . '/pdf/asisst_controls/assit_attendances.php');

//assit_schedules
require(__DIR__ . '/tab/asisst_controls/assit_schedules.php');
// require (__DIR__ . '/pdf/asisst_controls/assit_schedules.php');
//require (__DIR__ . '/pdf/receibts/main.php');
require(__DIR__ . '/pdf/asisst_controls/assit_attendances.php');

//calendar_events
require(__DIR__ . '/tab/calendar_events.php');
// require (__DIR__ . '/pdf/asisst_controls/assit_schedules.php');
//require (__DIR__ . '/pdf/receibts/main.php');


//payments
require(__DIR__ . '/tab/transactions.php');
// require (__DIR__ . '/charts/transactions.php');
// require (__DIR__ . '/tab/mbancarios.php');
// require (__DIR__ . '/ajax/modal/payments.php');//generacion de fill modal

//mailList (resend y sendpulse)
require(__DIR__ . '/tab/resend.php');

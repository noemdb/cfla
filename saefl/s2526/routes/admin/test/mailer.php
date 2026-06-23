<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas iniciales
|
*/

use App\Http\Controllers\Administracion\Email\Catchment\CatchmentSendNotificationsController;
use App\Http\Controllers\Administracion\Email\Collection\sendCongratulationController;
use App\Http\Controllers\Administracion\Email\RegistroPago\sendTicketPaymentController;

Route::get('/congratulations/send', [sendCongratulationController::class, 'sendCongratulations']);
Route::get('/congratulations/view', [sendCongratulationController::class, 'view']);

//registro de pago
Route::get('/ticket/send/{pago_combinado_id}', [sendTicketPaymentController::class, 'sendMail']);
Route::get('/ticket/view', [sendTicketPaymentController::class, 'view']);

Route::get('/catchments/reprogrammer', [CatchmentSendNotificationsController::class, 'sendMailInterviewReprogrammer']);

Route::get('/catchments/reprogrammer/fase/1', [CatchmentSendNotificationsController::class, 'sendMailInterviewReprogrammerFaseOne']);
Route::get('/catchments/accepteds', [CatchmentSendNotificationsController::class, 'sendMailCatchmentAccepted']);


?>
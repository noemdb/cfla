<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas iniciales
|
*/

use App\Http\Controllers\Administracion\Email\CollectionScheduleController;

Route::get('/coll_political/load/job', [CollectionScheduleController::class, 'bacthCollectionSendScheduleTest']);
Route::get('/coll_political/load/job/calendar', [CollectionScheduleController::class, 'bacthCollectionSendScheduleTestCalendar']);


?>
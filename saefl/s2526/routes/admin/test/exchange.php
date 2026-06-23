<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas iniciales
|
*/

// use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\Email\SetExchangeRateController;
use App\Http\Controllers\Restapi\Exchange\KurokuroController;
use App\Http\Controllers\Restapi\Exchange\CapriceController;

Route::get('/exchange/get/rate/kuro', [KurokuroController::class, 'setExchangeRate']);
// Route::get('/exchange/get/rate/caprice', [CapriceController::class, 'setExchangeRate']);
Route::get('/exchange/get/rate/caprice', [CapriceController::class, 'getExchanRateToday']);
Route::get('/exchange/set/rate/caprice', [CapriceController::class, 'setExchangeRateTodate']);
Route::get('/exchange/set/rate/caprice/send', [SetExchangeRateController::class, 'testMessegesSend']);

Route::get('/exchange/set/rate/caprice/cfla', [CapriceController::class, 'setExchangeRateTodateCFLA']);


?>

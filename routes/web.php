<?php

use App\Http\Controllers\Educational\CompetitionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () { return view('home'); });

Route::get('/studia', [HomeController::class, 'studia'])->name('studia');


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/reporte', [HomeController::class, 'payment'])->name('payment');
Route::get('/matricula', [HomeController::class, 'enrollment'])->name('enrollment');
Route::get('/pago', [HomeController::class, 'credicard'])->name('credicard');
Route::get('/post/{id}', [HomeController::class, 'post'])->name('post');

// Route::get('/env', [HomeController::class, 'env'])->name('env');

// Livewire::setScriptRoute(function ($handle) {
//     return Route::get('/cfla/livewire/livewire.js', $handle);
// });

// Livewire::setUpdateRoute(function ($handle) {
//     return Route::post('/cfla/livewire/update', $handle);
// });

// Livewire::setScriptRoute(function ($handle) {
//     return Route::get(env('APP_URL_PRE','null').'/livewire/livewire.js', $handle);
// });

// Livewire::setUpdateRoute(function ($handle) {
//     return Route::post(env('APP_URL_PRE','null').'/livewire/update', $handle);
// });

Route::group(['prefix' => 'general', 'namespace' => 'General'], function () {
    Route::get('/educational/competition/moderator/{token}', [CompetitionController::class,'moderator'])->name('general.educational.competition.moderator');
    Route::get('/educational/competition/board/{token}', [CompetitionController::class,'board'])->name('general.educational.competition.board');
    Route::get('/educational/competition/scoreboard/{token}', [CompetitionController::class,'scoreboard'])->name('general.educational.competition.scoreboard');
});


use App\Http\Controllers\OrderController;

// Route::put('/competitions/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
Route::get('/competitions/{orderId}/status/{status}', [OrderController::class, 'updateOrderStatus']);

<?php

use App\Http\Controllers\Census\CatchmentPDFController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\Educational\CompetitionController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\HomeController;
use App\Livewire\EnrollmentWizard;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Http\Controllers\OrderController;

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

Route::get('/censo', [CensusController::class, 'index'])->name('census');
Route::get('/catchment/download-pdf/{token}', [CatchmentPDFController::class, 'downloadPDF'])->name('catchment.download.pdf');

// Ruta para la prosecución
Route::get('/prosecucion', [HomeController::class, 'prosecucion'])->name('prosecucion');
Route::get('/prosecucion/download/{id}', [HomeController::class, 'downloadProsecucionPDF'])->name('prosecucion.download.pdf');

Route::group(['prefix' => 'general', 'namespace' => 'General'], function () {
    Route::get('/educational/competition/moderator/{token}', [CompetitionController::class, 'moderator'])->name('general.educational.competition.moderator');
    Route::get('/educational/competition/board/{token}', [CompetitionController::class, 'board'])->name('general.educational.competition.board');
    Route::get('/educational/competition/scoreboard/{token}', [CompetitionController::class, 'scoreboard'])->name('general.educational.competition.scoreboard');
});

// Route::put('/competitions/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
Route::get('/competitions/{orderId}/status/{status}', [OrderController::class, 'updateOrderStatus']);


//Api Gmail
Route::get('/auth/google', [GmailController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/oauth2callback', [GmailController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/send-email', [GmailController::class, 'sendEmail']);



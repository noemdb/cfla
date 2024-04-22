<?php

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


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/env', [HomeController::class, 'env'])->name('env');

Livewire::setScriptRoute(function ($handle) {
    return Route::get(env('APP_URL').'/livewire/livewire.js', $handle);
});
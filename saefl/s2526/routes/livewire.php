<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Livewire Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Livewire routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group but WITHOUT the default controller namespace.
|
*/

Route::group(['prefix' => 'app', 'middleware' => ['auth', 'is_admin']], function () {
    Route::get('/prompt-contexts/index', \App\Http\Livewire\Administracion\Agentia\PromptContextIndex::class)->name('administracion.prompt-contexts.index');
});

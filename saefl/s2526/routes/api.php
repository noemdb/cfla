<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|Route::middleware('auth:api')->get('/users', function (Request $request) {

| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


use App\Http\Controllers\GeminiController;

Route::prefix('gemini')->middleware('gemini.rate')->group(function () {
    Route::post('/generate', [GeminiController::class, 'generate']);
    Route::post('/analyze-image', [GeminiController::class, 'analyzeImage']);
    Route::post('/chat', [GeminiController::class, 'chat']);
    Route::post('/count-tokens', [GeminiController::class, 'countTokens']);
    Route::get('/models', [GeminiController::class, 'listModels']);
    Route::post('/stream', [GeminiController::class, 'stream']);
});

//Route::post('register', 'Auth\RegisterController@registerapi');
Route::post('bot/represntant/info', 'Api\Bot\RepresentantController@info');
Route::post('bot/exchangerate/info', 'Api\Bot\ExchangeRateController@info');

Route::post('bot/autoresponder/main', 'Api\Bot\AutoresponderController@main');
Route::post('bot/autoresponder/info/debs', 'Api\Bot\AutoresponderController@sendInfoDebs');
Route::post('bot/autoresponder/exchange/rate', 'Api\Bot\AutoresponderController@sendExchangeRate');

Route::post('bot/autoresponder/control/main', 'Api\Bot\AutoresponderControlController@main');
// Route::post('bot/autoresponder/control/request/reset/password', 'Api\Bot\AutoresponderControlController@resquestResetPassword');
Route::post('bot/autoresponder/control/request/reset/password/name', 'Api\Bot\AutoresponderControlController@resquestResetPasswordName');

Route::post('/resend/webhook', [App\Http\Controllers\Api\ResendWebhookController::class, 'handle']);
Route::post('/sendpulse/webhook', [App\Http\Controllers\Api\SendPulseWebhookController::class, 'handle']);



<?php

use App\Services\Lms\BroadcastAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ACK de eventos broadcast (Opción 10): el firmware del cliente lo envía al
// recibir un evento. Idempotente (el flag `delivered` solo se marca una vez)
// y rate-limited para evitar abuso.
Route::post('/broadcast/ack', function (Request $request) {
    $request->validate([
        'event_id' => ['required', 'integer', 'exists:broadcast_events,id'],
    ]);

    app(BroadcastAudit::class)->ack((int) $request->input('event_id'));

    return response()->json(['ok' => true]);
})->middleware(['auth:sanctum', 'throttle:30,1']);

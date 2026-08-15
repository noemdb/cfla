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

// Timeline de actividad de un usuario (Spec BINNACLE-001, §10). Requiere
// token Sanctum; el panel Livewire lo consume en el cliente del admin.
Route::middleware('auth:sanctum')->get('/binnacle/user/{userId}/timeline', function (Request $request, int $userId) {
    $timeline = \App\Services\Binnacle::getUserActivityTimeline(
        $userId,
        $request->query('from'),
        $request->query('to'),
    );

    return response()->json([
        'user_id' => $userId,
        'total' => $timeline->count(),
        'entries' => $timeline->map(fn ($e) => [
            'id' => $e->id,
            'event_type' => $e->event_type,
            'category' => $e->event_category,
            'severity' => $e->event_severity,
            'title' => $e->title,
            'description' => $e->description,
            'created_at' => $e->created_at?->toISOString(),
            'ip_address' => $e->ip_address,
            'request_url' => $e->request_url,
        ]),
    ]);
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

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Inicial\EiplanningwkApiController;
use App\Http\Controllers\API\Inicial\EiplanningwstrategyApiController;
use App\Http\Controllers\API\Inicial\EiplanningwsummaryApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Educación Inicial
|--------------------------------------------------------------------------
|
| Rutas API REST para el módulo de Educación Inicial.
| Todas las rutas están protegidas por autenticación Sanctum.
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Grupo para planificaciones semanales
    Route::prefix('inicial/eiplanningwks')->group(function () {
        Route::get('/', [EiplanningwkApiController::class, 'index'])
            ->name('api.inicial.eiplanningwks.index')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::post('/', [EiplanningwkApiController::class, 'store'])
            ->name('api.inicial.eiplanningwks.store')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::get('/statistics', [EiplanningwkApiController::class, 'statistics'])
            ->name('api.inicial.eiplanningwks.statistics')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::get('/{id}', [EiplanningwkApiController::class, 'show'])
            ->name('api.inicial.eiplanningwks.show')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::put('/{id}', [EiplanningwkApiController::class, 'update'])
            ->name('api.inicial.eiplanningwks.update')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::delete('/{id}', [EiplanningwkApiController::class, 'destroy'])
            ->name('api.inicial.eiplanningwks.destroy')
            ->middleware('rate_limit_inicial:20,1');
        
        Route::post('/{id}/clone', [EiplanningwkApiController::class, 'clone'])
            ->name('api.inicial.eiplanningwks.clone')
            ->middleware('rate_limit_inicial:10,1');
        
        Route::get('/{id}/export-pdf', [EiplanningwkApiController::class, 'exportPdf'])
            ->name('api.inicial.eiplanningwks.export-pdf')
            ->middleware('rate_limit_inicial:20,1');
        
        Route::get('/{id}/download-pdf', [EiplanningwkApiController::class, 'exportPdf'])
            ->name('api.inicial.eiplanningwks.download-pdf')
            ->middleware('rate_limit_inicial:20,1');
    });

    // Grupo para estrategias de planificación
    Route::prefix('inicial/eiplanningwstrategies')->group(function () {
        Route::get('/', [EiplanningwstrategyApiController::class, 'index'])
            ->name('api.inicial.eiplanningwstrategies.index')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::post('/', [EiplanningwstrategyApiController::class, 'store'])
            ->name('api.inicial.eiplanningwstrategies.store')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::get('/{id}', [EiplanningwstrategyApiController::class, 'show'])
            ->name('api.inicial.eiplanningwstrategies.show')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::put('/{id}', [EiplanningwstrategyApiController::class, 'update'])
            ->name('api.inicial.eiplanningwstrategies.update')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::delete('/{id}', [EiplanningwstrategyApiController::class, 'destroy'])
            ->name('api.inicial.eiplanningwstrategies.destroy')
            ->middleware('rate_limit_inicial:20,1');
        
        Route::post('/batch', [EiplanningwstrategyApiController::class, 'storeBatch'])
            ->name('api.inicial.eiplanningwstrategies.store-batch')
            ->middleware('rate_limit_inicial:10,1');
        
        Route::put('/{id}/order', [EiplanningwstrategyApiController::class, 'updateOrder'])
            ->name('api.inicial.eiplanningwstrategies.update-order')
            ->middleware('rate_limit_inicial:30,1');
    });

    // Grupo para resúmenes de planificación
    Route::prefix('inicial/eiplanningwsummaries')->group(function () {
        Route::get('/', [EiplanningwsummaryApiController::class, 'index'])
            ->name('api.inicial.eiplanningwsummaries.index')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::post('/', [EiplanningwsummaryApiController::class, 'store'])
            ->name('api.inicial.eiplanningwsummaries.store')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::get('/{id}', [EiplanningwsummaryApiController::class, 'show'])
            ->name('api.inicial.eiplanningwsummaries.show')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::put('/{id}', [EiplanningwsummaryApiController::class, 'update'])
            ->name('api.inicial.eiplanningwsummaries.update')
            ->middleware('rate_limit_inicial:30,1');
        
        Route::delete('/{id}', [EiplanningwsummaryApiController::class, 'destroy'])
            ->name('api.inicial.eiplanningwsummaries.destroy')
            ->middleware('rate_limit_inicial:20,1');
        
        Route::get('/plan/{planId}', [EiplanningwsummaryApiController::class, 'byPlan'])
            ->name('api.inicial.eiplanningwsummaries.by-plan')
            ->middleware('rate_limit_inicial:60,1');
        
        Route::post('/validate', [EiplanningwsummaryApiController::class, 'validateCoverage'])
            ->name('api.inicial.eiplanningwsummaries.validate-coverage')
            ->middleware('rate_limit_inicial:30,1');
    });

    // Webhooks para integraciones externas
    Route::prefix('webhooks/inicial')->group(function () {
        Route::post('/plan-created', [EiplanningwkApiController::class, 'webhookPlanCreated'])
            ->name('api.webhooks.inicial.plan-created')
            ->withoutMiddleware(['auth:sanctum'])
            ->middleware('webhook-auth');
        
        Route::post('/plan-updated', [EiplanningwkApiController::class, 'webhookPlanUpdated'])
            ->name('api.webhooks.inicial.plan-updated')
            ->withoutMiddleware(['auth:sanctum'])
            ->middleware('webhook-auth');
        
        Route::post('/plan-deleted', [EiplanningwkApiController::class, 'webhookPlanDeleted'])
            ->name('api.webhooks.inicial.plan-deleted')
            ->withoutMiddleware(['auth:sanctum'])
            ->middleware('webhook-auth');
    });

});
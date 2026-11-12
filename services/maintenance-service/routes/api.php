<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\MaintenancePlanController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::get('/interventions/stats', [InterventionController::class, 'stats']);
    Route::post('/interventions', [InterventionController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE,AGENT');
    Route::get('/interventions/{id}', [InterventionController::class, 'show'])->whereUuid('id');
    Route::patch('/interventions/{id}/start', [InterventionController::class, 'start'])->middleware('role:ADMIN,GESTIONNAIRE')->whereUuid('id');
    Route::patch('/interventions/{id}/close', [InterventionController::class, 'close'])->middleware('role:ADMIN,GESTIONNAIRE')->whereUuid('id');
    Route::patch('/interventions/{id}/cancel', [InterventionController::class, 'cancel'])->middleware('role:ADMIN,GESTIONNAIRE')->whereUuid('id');

    Route::get('/maintenance-plans', [MaintenancePlanController::class, 'index']);
    Route::post('/maintenance-plans', [MaintenancePlanController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/maintenance-plans/{id}', [MaintenancePlanController::class, 'show'])->whereUuid('id');
    Route::patch('/maintenance-plans/{id}', [MaintenancePlanController::class, 'update'])->middleware('role:ADMIN,GESTIONNAIRE')->whereUuid('id');
    Route::delete('/maintenance-plans/{id}', [MaintenancePlanController::class, 'destroy'])->middleware('role:ADMIN,GESTIONNAIRE')->whereUuid('id');
});

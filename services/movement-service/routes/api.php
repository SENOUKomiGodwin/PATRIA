<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\MovementController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/movements', [MovementController::class, 'index']);
    Route::post('/movements', [MovementController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/movements/{id}', [MovementController::class, 'show']);
});

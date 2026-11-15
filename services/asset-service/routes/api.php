<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;

Route::get('/health', HealthController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/assets/stats', [AssetController::class, 'stats']);

    Route::get('/assets', [AssetController::class, 'index']);
    Route::post('/assets', [AssetController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/assets/{id}', [AssetController::class, 'show']);
    Route::put('/assets/{id}', [AssetController::class, 'update'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/assets/{id}/qr', [AssetController::class, 'qr']);
    Route::get('/assets/{id}/depreciation', [AssetController::class, 'depreciation']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('role:ADMIN,GESTIONNAIRE');

    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations', [LocationController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/locations/{id}', [LocationController::class, 'show']);
    Route::put('/locations/{id}', [LocationController::class, 'update'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->middleware('role:ADMIN,GESTIONNAIRE');
});

<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('/reports/depreciation', [ReportController::class, 'depreciation']);
    Route::get('/reports/exports/{type}', [ReportController::class, 'export'])
        ->middleware('role:ADMIN,GESTIONNAIRE,AUDITEUR');
});

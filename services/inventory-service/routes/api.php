<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/campaigns/{id}', [CampaignController::class, 'show']);
    Route::post('/campaigns/{id}/lines', [CampaignController::class, 'addLine'])->middleware('role:AGENT,GESTIONNAIRE');
    Route::post('/campaigns/{id}/close', [CampaignController::class, 'close'])->middleware('role:ADMIN,GESTIONNAIRE');
    Route::get('/campaigns/{id}/discrepancies', [CampaignController::class, 'discrepancies']);
});

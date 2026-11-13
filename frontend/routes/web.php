<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

// Style guide (dev only)
Route::get('/styleguide', function () {
    return view('styleguide.index');
})->name('styleguide');

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth.session'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Assets
    Route::prefix('biens')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AssetController::class, 'update'])->name('update');
    });

    // Movements
    Route::prefix('mouvements')->name('movements.')->group(function () {
        Route::get('/', [MovementController::class, 'index'])->name('index');
    });

    // Inventories
    Route::prefix('inventaires')->name('inventories.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/{id}', [InventoryController::class, 'show'])->name('show');
    });

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/interventions/{id}', [MaintenanceController::class, 'showIntervention'])->name('intervention');
        Route::get('/plans', [MaintenanceController::class, 'plans'])->name('plans');
    });

    // Reports
    Route::prefix('rapports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/amortissement', [ReportController::class, 'depreciation'])->name('depreciation');
        Route::get('/exports', [ReportController::class, 'exports'])->name('exports');
    });

    // Users
    Route::prefix('utilisateurs')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
    });
});

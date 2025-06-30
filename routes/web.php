<?php

use Illuminate\Support\Facades\Route;
use Hongdev\MasterAdmin\Http\Controllers\MasterAdminController;
use Hongdev\MasterAdmin\Http\Controllers\CommandController;
use Hongdev\MasterAdmin\Http\Controllers\LogController;
use Hongdev\MasterAdmin\Http\Controllers\SettingsController;
use Hongdev\MasterAdmin\Http\Controllers\DatabaseController;

Route::middleware('master-admin')->prefix('master-admin')->group(function () {
    // Dashboard
    Route::get('/', [MasterAdminController::class, 'index']);
    
    // Artisan Commands
    Route::get('/commands/{command}', [CommandController::class, 'executeCommand']);
    
    // Logs
    Route::get('/logs/view', [LogController::class, 'viewLogs']);
    Route::get('/logs/clear', [LogController::class, 'clearLogs']);
    
    // Settings
    Route::get('/settings/environment/{env}', [SettingsController::class, 'changeEnvironment']);
    Route::get('/settings/debug/{mode}', [SettingsController::class, 'toggleDebugMode']);
    
    // Database configuration
    Route::get('database/test-connection', [DatabaseController::class, 'testConnection']);
    Route::get('database/config', [DatabaseController::class, 'showConfig'])->name('master-admin.database.config');
    Route::post('database/config', [DatabaseController::class, 'updateConfig']);
});

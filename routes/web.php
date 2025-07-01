<?php

use Illuminate\Support\Facades\Route;
use Hongdev\MasterAdmin\Http\Controllers\MasterAdminController;
use Hongdev\MasterAdmin\Http\Controllers\CommandController;
use Hongdev\MasterAdmin\Http\Controllers\LogController;
use Hongdev\MasterAdmin\Http\Controllers\SettingsController;
use Hongdev\MasterAdmin\Http\Controllers\Settings\DatabaseController;
use Hongdev\MasterAdmin\Http\Controllers\Settings\EnvironmentController;
use Hongdev\MasterAdmin\Http\Controllers\Settings\MailController;
use Hongdev\MasterAdmin\Http\Controllers\Settings\DriveController;
use Hongdev\MasterAdmin\Http\Controllers\SystemController;
use Hongdev\MasterAdmin\Http\Controllers\BackupController;

Route::middleware('master-admin')->prefix('master-admin')->group(function () {
    // Dashboard
    Route::get('/', [MasterAdminController::class, 'index'])->name('master-admin.dashboard');

    // Artisan Commands
    Route::get('/commands/{command}', [CommandController::class, 'executeCommand'])->name('master-admin.commands.execute');

    // Logs
    Route::prefix('logs')->name('master-admin.logs.')->group(function () {
        Route::get('/view', [LogController::class, 'viewLogs'])->name('view');
        Route::get('/clear', [LogController::class, 'clearLogs'])->name('clear');
    });

    // Settings
    Route::prefix('settings')->name('master-admin.settings.')->group(function () {
        // Main settings page
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        
        // Environment settings
        Route::prefix('environment')->name('environment.')->group(function () {
            Route::get('/', [EnvironmentController::class, 'index'])->name('index');
            Route::get('/change/{env}', [EnvironmentController::class, 'change'])->name('change');
            Route::get('/debug/{mode}', [EnvironmentController::class, 'debug'])->name('debug');
        });

        // Database settings
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/', [DatabaseController::class, 'index'])->name('index');
            Route::post('/', [DatabaseController::class, 'update'])->name('update');
            Route::get('/test', [DatabaseController::class, 'test'])->name('test');
        });

        // Mail settings
        Route::prefix('mail')->name('mail.')->group(function () {
            Route::get('/', [MailController::class, 'index'])->name('index');
            Route::post('/', [MailController::class, 'update'])->name('update');
            Route::get('/test', [MailController::class, 'test'])->name('test');
        });

        // Google Drive settings
        Route::prefix('drive')->name('drive.')->group(function () {
            Route::get('/', [DriveController::class, 'index'])->name('index');
            Route::post('/', [DriveController::class, 'update'])->name('update');
            Route::get('/test', [DriveController::class, 'test'])->name('test');
        });
    });

    // Backup
    Route::prefix('backup')->group(function () {
        // Trang backup, hiển thị lựa chọn backup
        Route::get('/', [BackupController::class, 'index'])->name('master-admin.backup.index');
        // Thực hiện backup database (chỉ data)
        Route::post('/database', [BackupController::class, 'backupDatabase'])->name('master-admin.backup.database');
        // Thực hiện backup storage
        Route::post('/storage', [BackupController::class, 'backupStorage'])->name('master-admin.backup.storage');
        // Thực hiện backup toàn bộ (trừ vendor, node_modules)
        Route::post('/full', [BackupController::class, 'backupFull'])->name('master-admin.backup.full');
        // Thực hiện backup tất cả (bao gồm cả vendor, node_modules)
        Route::post('/all', [BackupController::class, 'backupAll'])->name('master-admin.backup.all');
        // Lưu file backup lên Google Drive
        Route::post('/upload', [BackupController::class, 'uploadToDrive'])->name('master-admin.backup.upload');
    });

    // System metrics
    Route::get('system/metrics', [SystemController::class, 'getPerformanceMetrics']);
});

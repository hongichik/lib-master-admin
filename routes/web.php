<?php

use Illuminate\Support\Facades\Route;
use Hongdev\MasterAdmin\Http\Controllers\MasterAdminController;
use Hongdev\MasterAdmin\Http\Controllers\CommandController;
use Hongdev\MasterAdmin\Http\Controllers\LogController;
use Hongdev\MasterAdmin\Http\Controllers\SettingsController;
use Hongdev\MasterAdmin\Http\Controllers\DatabaseController;
use Hongdev\MasterAdmin\Http\Controllers\SystemController;
use Hongdev\MasterAdmin\Http\Controllers\MailController;
use Hongdev\MasterAdmin\Http\Controllers\DriveController;
use Hongdev\MasterAdmin\Http\Controllers\BackupController;

Route::middleware('master-admin')->prefix('master-admin')->group(function () {
    // Dashboard
    Route::get('/', [MasterAdminController::class, 'index']);

    // Artisan Commands
    Route::get('/commands/{command}', [CommandController::class, 'executeCommand']);

    // Logs
    Route::get('/logs/view', [LogController::class, 'viewLogs']);
    Route::get('/logs/clear', [LogController::class, 'clearLogs']);

    Route::prefix('settings')->group(function () {
        Route::get('environment/{env}', [SettingsController::class, 'changeEnvironment']);
        Route::get('debug/{mode}', [SettingsController::class, 'toggleDebugMode']);

        // Database configuration
        Route::get('database/test-connection', [DatabaseController::class, 'testConnection']);
        Route::get('database/config', [DatabaseController::class, 'showConfig'])->name('master-admin.database.config');
        Route::post('database/config', [DatabaseController::class, 'updateConfig']);

        // Mail Configuration
        Route::get('mail/config', [MailController::class, 'showConfig'])->name('master-admin.mail.config');
        Route::post('mail/config', [MailController::class, 'updateConfig']);
        Route::get('mail/test', [MailController::class, 'testMail']);

        // Google Drive Configuration
        Route::get('drive/config', [DriveController::class, 'showConfig'])->name('master-admin.drive.config');
        Route::post('drive/config', [DriveController::class, 'updateConfig']);
        Route::get('drive/test', [DriveController::class, 'testConnection']);
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

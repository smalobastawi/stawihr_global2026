<?php

use App\Http\Controllers\Annalytics\AnnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogsController;
use App\Http\Controllers\DownloadLogsController;
use App\Http\Controllers\ReportsController;


Route::group(['module' => 'Annalytics', 'middleware' => ['auth', 'permission'], 'prefix' => 'reports'], function () {
    
    Route::group(['section' => 'activity', 'sub_section' => 'logs', 'prefix' => 'annalytics'], function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.test');
        Route::get('/activity_logs', [ActivityLogsController::class, 'index'])->name('reports.activity_logs');
        Route::get('/activity_logs_view/{id}', [ActivityLogsController::class, 'view'])->name('reports.activity_logs.view');
        Route::get('/download_logs', [DownloadLogsController::class, 'index'])->name('reports.download_logs');
        Route::get('/error_log', [ReportsController::class, 'errorLog'])->name('reports.errorLog');
    });

    Route::group(['section' => 'annalytics', 'sub_section' => 'annalytics', 'prefix' => 'annalytics'], function () {
        Route::get('/', [AnnalyticsController::class, 'index'])->name('reports.annalytics.view');
    });
    
});

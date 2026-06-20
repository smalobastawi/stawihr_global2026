<?php

use App\Http\Controllers\Api\Internal\StatsController;
use App\Http\Controllers\Api\Internal\SuspensionController;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\InternalApiAuth::class])->group(function () {
    Route::post('stats', [StatsController::class, 'getStats'])->name('internal.stats');
    Route::get('summary', [StatsController::class, 'getSummary'])->name('internal.summary');
    Route::get('health', fn () => response()->json([
        'success' => true,
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]))->name('internal.health');

    Route::get('suspension', [SuspensionController::class, 'show'])->name('internal.suspension.show');
    Route::post('suspension', [SuspensionController::class, 'store'])->name('internal.suspension.store');
    Route::delete('suspension', [SuspensionController::class, 'destroy'])->name('internal.suspension.destroy');
});

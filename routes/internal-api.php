<?php

use App\Http\Controllers\Api\Internal\EmployeeSyncController;
use App\Http\Controllers\Api\Internal\LeaveSyncController;
use App\Http\Controllers\Api\Internal\StatsController;
use App\Http\Controllers\Api\Internal\SuspensionController;
use App\Http\Controllers\Api\Internal\VehicleSyncController;
use App\Http\Middleware\InternalApiAuth;
use App\Http\Middleware\SchoolMisSyncAuth;
use App\Services\Integrations\VehicleSyncAccess;
use Illuminate\Support\Facades\Route;

Route::middleware([InternalApiAuth::class])->group(function () {
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

// School MIS (StawiSMS) outbound pull — gated by Settings → School MIS Integration
Route::prefix('sync')->name('internal.sync.')->middleware([SchoolMisSyncAuth::class])->group(function () {
    Route::get('health', function (VehicleSyncAccess $vehicles) {
        $capability = $vehicles->localCapability();

        return response()->json([
            'success' => true,
            'status' => 'ok',
            'integration' => 'school_mis',
            'timestamp' => now()->toIso8601String(),
            'data' => $capability,
            'vehicles_module_enabled' => $capability['vehicles_module_enabled'],
            'sync_vehicles' => $capability['sync_vehicles'],
            'can_write_vehicles' => $capability['can_write_vehicles'],
        ]);
    })->name('health');
    Route::get('employees', [EmployeeSyncController::class, 'index'])->name('employees');
    Route::get('leave/on-leave', [LeaveSyncController::class, 'onLeave'])->name('leave.on-leave');
    Route::get('leave', [LeaveSyncController::class, 'index'])->name('leave');
    Route::get('vehicles', [VehicleSyncController::class, 'index'])->name('vehicles');
    Route::post('vehicles/upsert', [VehicleSyncController::class, 'upsert'])->name('vehicles.upsert');
});

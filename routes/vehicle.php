<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Vehicle\VehicleAssignmentController;

Route::group(['module' => 'Vehicle Management', 'prefix' => 'vehicle', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    // Assignment History Routes — MUST be before /{id} catch-all
    Route::group(['section' => 'reports', 'sub_section' => 'assignments'], function () {
        Route::get('/assignments', [VehicleAssignmentController::class, 'index'])->name('vehicle.assignment.index');
        Route::get('/assignments/download', [VehicleAssignmentController::class, 'download'])->name('vehicle.assignment.download');
        Route::get('/assignments/vehicle/{vehicleId}', [VehicleAssignmentController::class, 'vehicleHistory'])->name('vehicle.assignment.vehicle_history');
        Route::get('/assignments/employee/{employeeId}', [VehicleAssignmentController::class, 'employeeHistory'])->name('vehicle.assignment.employee_history');
    });

    // Vehicle Routes
    Route::group(['section' => 'setup', 'sub_section' => 'vehicles'], function () {
        Route::get('/', [VehicleController::class, 'index'])->name('vehicle.index');
        Route::get('/create', [VehicleController::class, 'create'])->name('vehicle.create');
        Route::post('/store', [VehicleController::class, 'store'])->name('vehicle.store');

        // AJAX Routes - MUST be before /{id} catch-all
        Route::get('/get-drivers', [VehicleController::class, 'getDrivers'])->name('vehicle.get_drivers');

        // Import/Export Routes
        Route::post('/import', [VehicleController::class, 'import'])->name('vehicle.import');
        Route::get('/download-template', [VehicleController::class, 'downloadTemplate'])->name('vehicle.download_template');

        Route::get('/{id}/edit', [VehicleController::class, 'edit'])->name('vehicle.edit');
        Route::get('/{id}', [VehicleController::class, 'show'])->name('vehicle.show');
        Route::put('/{id}', [VehicleController::class, 'update'])->name('vehicle.update');
        Route::delete('/{id}/delete', [VehicleController::class, 'destroy'])->name('vehicle.delete');

        // Driver Assignment Routes
        Route::post('/{id}/assign-driver', [VehicleController::class, 'assignDriver'])->name('vehicle.assign_driver');
        Route::post('/{id}/unassign-driver', [VehicleController::class, 'unassignDriver'])->name('vehicle.unassign_driver');
    });
});

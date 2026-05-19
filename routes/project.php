<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\ProjectController;
use App\Http\Controllers\Payroll\ProjectAllocationController;
use App\Http\Controllers\Project\ProjectAllocationReportController;
use App\Http\Controllers\Payroll\ProjectAllocationBulkUploadController;
use App\Http\Controllers\DataImportController;

Route::group(['module' => 'project', 'prefix' => 'project', 'as' => 'project.', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    // Project allocation routes (keeping these as they're different from employee project management)
    Route::post('employee/{employee}/project-allocation', [ProjectAllocationController::class, 'store'])->name('project-allocation.store');
    Route::get('employee/{employee}/project-allocation/create', [ProjectAllocationController::class, 'create'])->name('project-allocation.create');
    Route::post('employee/{employee}/project-allocation', [ProjectAllocationController::class, 'store'])->name('project-allocation.store');
    Route::get('project-allocation/{id}/edit', [ProjectAllocationController::class, 'edit'])->name('project-allocation.edit');
    Route::put('project-allocation/{id}', [ProjectAllocationController::class, 'update'])->name('project-allocation.update');
    Route::delete('/project-allocation/{id}', [ProjectAllocationController::class, 'destroy'])->name('project-allocation.delete');

    // Project Allocations List
    Route::get('/project-allocations', [ProjectAllocationController::class, 'index'])->name('project-allocations.index');
    Route::post('/project-allocations', [ProjectAllocationController::class, 'storeAllocation'])->name('project-allocations.store');

    // Project Allocation Report
    Route::get('/project-allocation-report', [ProjectAllocationReportController::class, 'index'])->name('project-allocation-report.index');
    Route::post('/project-allocation-report/export', [ProjectAllocationReportController::class, 'export'])->name('project-allocation-report.export');

    // Project Allocation Bulk Upload
    Route::get('/project-allocations/bulk-upload', [ProjectAllocationBulkUploadController::class, 'index'])->name('project-allocations.bulk-upload.index');
    Route::get('/project-allocations/bulk-upload/download-template', [ProjectAllocationBulkUploadController::class, 'downloadTemplate'])->name('project-allocations.bulk-upload.download-template');
    Route::post('/project-allocations/bulk-upload', [DataImportController::class, 'projectAllocationsImport'])->name('project-allocations.bulk-upload.import');
});

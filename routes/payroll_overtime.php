<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payroll\EmployeeOvertimeController;

Route::group(['module' => 'Payroll', 'section' => 'overtime', 'prefix' => 'payroll/overtime', 'middleware' => ['auth']], function () {
    // Bulk Upload Routes
    Route::get('/bulk-upload', ['as' => 'payroll.overtime.bulk_upload.index', 'uses' => 'App\Http\Controllers\DataImportController@overtimeIndex']);
    Route::post('/bulk-upload', ['as' => 'payroll.overtime.bulk_upload', 'uses' => 'App\Http\Controllers\DataImportController@overtimeImport']);
    Route::get('/bulk-upload/download-template', ['as' => 'payroll.overtime.bulk_upload.download_template', 'uses' => 'App\Http\Controllers\DataImportController@downloadOvertimeTemplate']);

    Route::get('/', [EmployeeOvertimeController::class, 'index'])->name('payroll.overtime.index');
    Route::get('/create', [EmployeeOvertimeController::class, 'create'])->name('payroll.overtime.create');
    Route::post('/store', [EmployeeOvertimeController::class, 'store'])->name('payroll.overtime.store');
    Route::get('/{id}/show/', [EmployeeOvertimeController::class, 'show'])->name('payroll.overtime.show');
    Route::get('/{id}/edit', [EmployeeOvertimeController::class, 'edit'])->name('payroll.overtime.edit');
    Route::put('/{id}/update/', [EmployeeOvertimeController::class, 'update'])->name('payroll.overtime.update');
    Route::delete('/{id}/delete/', [EmployeeOvertimeController::class, 'destroy'])->name('payroll.overtime.delete');
    
    // Import/Export routes
    Route::get('/template/download', [EmployeeOvertimeController::class, 'downloadTemplate'])->name('payroll.overtime.template.download');
    Route::get('/upload_payroll_overtimes', [EmployeeOvertimeController::class, 'showImportForm'])->name('payroll.overtime.import.form');
    Route::post('/import', [EmployeeOvertimeController::class, 'import'])->name('payroll.overtime.import');

    // AJAX routes
    Route::get('/employee/{employee_id}/overtime-rate', [EmployeeOvertimeController::class, 'getEmployeeOvertimeRate'])->name('payroll.overtime.getEmployeeOvertimeRate');
    //Update the overtimes into the Payroll side. 
});
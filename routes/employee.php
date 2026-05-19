<?php

use App\Http\Controllers\Employee\RegionController;
use App\Models\StaffContract;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\StaffContractController;
use App\Http\Controllers\Employee\LocationController;
use App\Http\Controllers\User\UserImportController;
use App\Http\Controllers\Employee\ProgramController;
use App\Http\Controllers\Employee\WarningController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\DepartmentController;
use App\Http\Controllers\Employee\DesignationController;
use App\Http\Controllers\Employee\TerminationController;
use App\Http\Controllers\Employee\TerminationChecklistController;

use App\Http\Controllers\Employee\EmployeeGroupController;
use App\Http\Controllers\Employee\EmployeeReportsController;
use App\Http\Controllers\Employee\EmployeeSectionController;
use App\Http\Controllers\Employee\EmployeeMovementController;
use App\Http\Controllers\Employee\EmployeePermanentController;
use App\Http\Controllers\Employee\EmployeeEducationQualificationController;
use App\Http\Controllers\EthnicityController;

Route::group(['module' => 'Employee Management', 'section' => 'General', 'prefix' => 'employeeManagement', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['sub_section' => 'department', 'prefix' => 'department'], function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('department.create');
        Route::post('/store', [DepartmentController::class, 'store'])->name('department.store');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('department.update');
        Route::delete('/{department}/delete', [DepartmentController::class, 'destroy'])->name('department.delete');
    });

    Route::group(['sub_section' => 'designation', 'prefix' => 'designation'], function () {
        Route::get('/', [DesignationController::class, 'index'])->name('designation.index');
        Route::get('/create', [DesignationController::class, 'create'])->name('designation.create');
        Route::post('/store', [DesignationController::class, 'store'])->name('designation.store');
        Route::get('/{designation}/edit', [DesignationController::class, 'edit'])->name('designation.edit');
        Route::put('/{designation}', [DesignationController::class, 'update'])->name('designation.update');
        Route::delete('/{designation}/delete', [DesignationController::class, 'destroy'])->name('designation.delete');
    });


    Route::group(['sub_section' => 'location', 'prefix' => 'location'], function () {
        Route::get('/', [LocationController::class, 'index'])->name('location.index');
        Route::get('/create', [LocationController::class, 'create'])->name('location.create');
        Route::post('/store', [LocationController::class, 'store'])->name('location.store');
        Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('location.edit');
        Route::put('/{location}', [LocationController::class, 'update'])->name('location.update');
        Route::delete('/{location}/delete', [LocationController::class, 'destroy'])->name('location.delete');
    });

    Route::group(['sub_section' => 'region', 'prefix' => 'region'], function () {
        Route::get('/', [RegionController::class, 'index'])->name('region.index');
        Route::get('/create', [RegionController::class, 'create'])->name('region.create');
        Route::post('/store', [RegionController::class, 'store'])->name('region.store');
        Route::get('/{region}/edit', [RegionController::class, 'edit'])->name('region.edit');
        Route::put('/{region}', [RegionController::class, 'update'])->name('region.update');
        Route::delete('/{region}/delete', [RegionController::class, 'destroy'])->name('region.delete');
    });

    Route::group(['sub_section' => 'employee', 'prefix' => 'employee'], function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employee.index');
        Route::get('/inactive', [EmployeeController::class, 'inactive'])->name('employee.inactive.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employee.create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('employee.store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('employee.show');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
        Route::delete('/{employee}/delete', [EmployeeController::class, 'destroy'])->name('employee.delete');
        Route::get('/{employee}/disable', [EmployeeController::class, 'disable'])->name('employee.disable');
        Route::get('/{employee}/enable', [EmployeeController::class, 'enable'])->name('employee.enable');
        Route::get('/{employee}/updateBiometricCaptureStatus', [EmployeeController::class, 'updateBiometricCaptureStatus'])->name('employee.updateBiometricCaptureStatus');
        Route::put('/earnings/{id}', [EmployeeController::class, 'updateEarning'])->name('employee.updateEarning');
        Route::delete('/earnings/{id}', [EmployeeController::class, 'deleteEarning'])->name('employee.deleteEarning');
        Route::post('/{employee}/deductions', [EmployeeController::class, 'addDeduction'])->name('employee.addDeduction');
        Route::put('/{employee}/deductions/{deduction}', [EmployeeController::class, 'updateDeduction'])->name('employee.updateDeduction');
        Route::delete('/{employee}/deductions/{deduction}', [EmployeeController::class, 'deleteDeduction'])->name('employee.deleteDeduction');
    });

    Route::group(['sub_section' => 'warning', 'prefix' => 'warning'], function () {
        Route::get('/', [WarningController::class, 'index'])->name('warning.index');
        Route::get('/create', [WarningController::class, 'create'])->name('warning.create');
        Route::post('/store', [WarningController::class, 'store'])->name('warning.store');
        Route::get('/{warning}/edit', [WarningController::class, 'edit'])->name('warning.edit');
        Route::get('/{warning}', [WarningController::class, 'show'])->name('warning.show');
        //        Route::get('/{warning}',[WarningController::class, 'show'])->name('warning.show');
        Route::put('/{warning}', [WarningController::class, 'update'])->name('warning.update');
        Route::delete('/{warning}/delete', [WarningController::class, 'destroy'])->name('warning.delete');
    });

    Route::group(['sub_section' => 'termination', 'prefix' => 'termination'], function () {
        Route::get('/', [TerminationController::class, 'index'])->name('termination.index');
        Route::get('/create', [TerminationController::class, 'create'])->name('termination.create');
        Route::post('/store', [TerminationController::class, 'store'])->name('termination.store');
        //termination imports here - must be before wildcard routes
        Route::get('importTermination', [TerminationController::class, 'import'])->name('termination.import');
        Route::post('importTerminationSave', [DataImportController::class, 'importTerminations'])->name('termination.importSave');
        Route::post('delete_termination_doc', [TerminationController::class, 'deleteTerminationDoc'])->name('termination.doc.delete');
        Route::get('/report/{id}', [TerminationController::class, 'report'])->name('termination.report');
        Route::get('/{termination}/reinstate', [TerminationController::class, 'reinstate'])->name('termination.reinstate');
        Route::get('/{termination}/edit', [TerminationController::class, 'edit'])->name('termination.edit');
        Route::get('/{termination}', [TerminationController::class, 'show'])->name('termination.show');
        Route::put('/{termination}', [TerminationController::class, 'update'])->name('termination.update');
        Route::delete('/{termination}/delete', [TerminationController::class, 'destroy'])->name('termination.delete');
    });


    Route::group(['sub_section' => 'termination-checklist', 'prefix' => 'termination-checklist'], function () {
        Route::get('/', [TerminationChecklistController::class, 'index'])->name('termination-checklist.index');
        Route::get('/create', [TerminationChecklistController::class, 'create'])->name('termination-checklist.create');
        Route::post('/store', [TerminationChecklistController::class, 'store'])->name('termination-checklist.store');
        //termination imports here - must be before wildcard routes
        Route::get('importTermination', [TerminationChecklistController::class, 'import'])->name('termination-checklist.import');
        Route::post('importTerminationSave', [DataImportController::class, 'importTerminations'])->name('termination-checklist.importSave');
        Route::post('/update-termination-checklist-action/{checklist}', [TerminationChecklistController::class, 'updateTerminationChecklistAction'])->name('termination-checklist-action.update');
        Route::get('/{termination}/edit', [TerminationChecklistController::class, 'edit'])->name('termination-checklist.edit');
        Route::get('/{termination}', [TerminationChecklistController::class, 'show'])->name('termination-checklist.show');
        Route::put('/{termination}', [TerminationChecklistController::class, 'update'])->name('termination-checklist.update');
        Route::delete('/{termination}/delete', [TerminationChecklistController::class, 'destroy'])->name('termination-checklist.delete');
    });

    Route::group(['prefix' => 'permanent'], function () {
        Route::get('/', [EmployeePermanentController::class, 'index'])->name('permanent.index');
        Route::get('/updatePermanent', [EmployeePermanentController::class, 'updatePermanent'])->name('permanent.updatePermanent');
    });

    Route::group(['sub_section' => 'reports'], function () {
        Route::get('export', [UserImportController::class, 'export'])->name('export');
        Route::get('import_employees', [DataImportController::class, 'index'])->name('employee.importView');
        Route::post('importUsers/import', [DataImportController::class, 'userImport'])->name('importUsers');
        // Redirect GET requests to the import form (handles accidental page refresh after submission)
        Route::get('importUsers/import', function () {
            return redirect()->route('employee.importView');
        });
        Route::post('importUsers/supervisors', [DataImportController::class, 'importSupervisors'])->name('importSupervisors');
        // Redirect GET requests for supervisors
        Route::get('importUsers/supervisors', function () {
            return redirect()->route('employee.importView');
        });
        Route::post('importUsers/contracts', [DataImportController::class, 'contractsImport'])->name('contractsImport');
        // Redirect GET requests for contracts
        Route::get('importUsers/contracts', function () {
            return redirect()->route('employee.importView');
        });
        Route::get('downloadSampleSupervisorFile', [DataImportController::class, 'downloadSupervisorSample'])->name('downloadSampleSupervisorFile');
        Route::get('downloadSampleContractsFile', [DataImportController::class, 'downloadSampleContractsFile'])->name('downloadSampleContractsFile');
        Route::get('downloadSampleEmployeeFile', [DataImportController::class, 'downloadSampleEmployeeFile'])->name('downloadSampleEmployeeFile');

        Route::get('/userReport', [EmployeeController::class, 'getEmployeeList'])->name('employee.active');

        Route::get('/joinersReport', [EmployeeReportsController::class, 'joinersReport'])->name('employee.joinersReport');
        Route::get('/leaversReport', [EmployeeReportsController::class, 'leaversReport'])->name('employee.leaversReport');
        Route::get('/movementReport', [EmployeeReportsController::class, 'movementReport'])->name('employee.movementReport');
        Route::get('/userReport1', [EmployeeController::class, 'userReportDownload'])->name('employee.downloadReport');
        Route::get('/masterRoll', [EmployeeController::class, 'masterRoll'])->name('employee.masterRoll');
    });
    //employeeSection and Group
    Route::group(['sub_section' => 'employee_section'], function () {
        Route::resource('employeeSection', EmployeeSectionController::class);
        //    Route::resource('employeeSectionDelete/{id}', [EmployeeSectionController::class, 'destroy']);
        Route::resource('employeeGroup', EmployeeGroupController::class);
        Route::get('workshiftshere', [EmployeeController::class, 'getShifts'])->name('workshift.share');
        Route::get('chart', [EmployeeController::class, 'charts'])->name('workshift.chart');
        Route::get('chart-line-ajax', [EmployeeController::class, 'chartLineAjax'])->name('workshift.chart_line');
    });


    //employee movement here
    Route::group(['sub_section' => 'employee_movement'], function () {
        Route::resource('employeeMovement', EmployeeMovementController::class);
        Route::get('employeeMovementImport', [EmployeeMovementController::class, 'bulkImport'])->name('employeeMovementImport');
        Route::post('employeeMovementImport', [DataImportController::class, 'importMovements'])->name('employeeMovementImportSave');
        Route::delete('employeeMovement/{id}/delete', [EmployeeMovementController::class, 'destroy'])->name('employeeMovement.delete');
        Route::get('employeeMovement/{id}/undoChanges', [EmployeeMovementController::class, 'undoChanges'])->name('employeeMovement.undoChanges');
        Route::post('employeeMovement/findEmployeeInfo', [EmployeeMovementController::class, 'findEmployeeInfo'])->name('employeeMovement.findEmployeeInfo');

    });
    //active user report


    Route::group(['sub_section' => 'contracts', 'prefix' => 'contracts'], function () {
        Route::get('/', [StaffContractController::class, 'index'])->name('contract.index');
        Route::get('/create/{id?}', [StaffContractController::class, 'create'])->name('contract.create');
        Route::post('/store', [StaffContractController::class, 'store'])->name('contract.store');
        Route::get('/{contract}/edit', [StaffContractController::class, 'edit'])->name('contract.edit');
        Route::get('/{contract}', [StaffContractController::class, 'show'])->name('contract.show');
        Route::put('/{contract}', [StaffContractController::class, 'update'])->name('contract.update');
        Route::delete('/{contract}/delete', [StaffContractController::class, 'delete'])->name('contract.delete');
        Route::delete('/{contract}/destroy', [StaffContractController::class, 'destroy'])->name('contract.destroy');
    });

    Route::group(['sub_section' => 'programs', 'prefix' => 'employee_programs', 'as' => 'employee.'], function () {
        Route::resource('/program', ProgramController::class);
    });

    Route::group(['sub_section' => 'projects', 'prefix' => 'projects', 'as' => 'employee.'], function () {
        Route::get('/', [\App\Http\Controllers\Employee\ProjectController::class, 'index'])->name('project.index');
        Route::get('/create', [\App\Http\Controllers\Employee\ProjectController::class, 'create'])->name('project.create');
        Route::post('/store', [\App\Http\Controllers\Employee\ProjectController::class, 'store'])->name('project.store');
        Route::get('/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'show'])->name('project.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Employee\ProjectController::class, 'edit'])->name('project.edit');
        Route::put('/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'update'])->name('project.update');
        Route::delete('/{id}', [\App\Http\Controllers\Employee\ProjectController::class, 'destroy'])->name('project.destroy');
    });
    Route::group(['sub_section' => 'ethnicity', 'prefix' => 'ethnicity', 'as' => 'ethnicities.'], function () {
        Route::get('ethnicities', [EthnicityController::class, 'index'])->name('index');
        Route::get('ethnicities/create', [EthnicityController::class, 'create'])->name('create');
        Route::post('ethnicities/store', [EthnicityController::class, 'store'])->name('store');
        Route::get('ethnicities/{ethnicity}', [EthnicityController::class, 'show'])->name('show');
        Route::get('ethnicities/{ethnicity}/edit', [EthnicityController::class, 'edit'])->name('edit');
        Route::put('ethnicities/{ethnicity}/update', [EthnicityController::class, 'update'])->name('update');
        Route::delete('ethnicities/{ethnicity}/delete', [EthnicityController::class, 'destroy'])->name('destroy');
    });
});

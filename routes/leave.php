<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Leave\HolidayController;
use App\Http\Controllers\Leave\PublicHolidayController;
use App\Http\Controllers\Leave\WeeklyHolidayController;
use App\Http\Controllers\Leave\LeaveTypeController;
use App\Http\Controllers\Leave\ApplyForLeaveController;
use App\Http\Controllers\Leave\RequestedApplicationController;
use App\Http\Controllers\Leave\ReportController;
use App\Http\Controllers\Leave\LeaveScheduleController;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\LeaveGroupController;

Route::group(['module' => 'Leave Management', 'prefix' => 'leaveManagement', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {
    //Route::get('/', [RequestedApplicationController::class, 'index'])->name('requestedApplication.index');
    Route::group(['section' => 'setup', 'sub_section' => 'holiday', 'prefix' => 'manageHoliday'], function () {
        Route::get('/', [HolidayController::class, 'index'])->name('holiday.index');
        Route::get('/create', [HolidayController::class, 'create'])->name('holiday.create');
        Route::post('/store', [HolidayController::class, 'store'])->name('holiday.store');
        Route::get('/{manageHoliday}/edit', [HolidayController::class, 'edit'])->name('holiday.edit');
        Route::put('/{manageHoliday}', [HolidayController::class, 'update'])->name('holiday.update');
        Route::delete('/{manageHoliday}/delete', [HolidayController::class, 'destroy'])->name('holiday.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'holiday', 'prefix' => 'publicHoliday'], function () {
        Route::get('/', [PublicHolidayController::class, 'index'])->name('publicHoliday.index');
        Route::get('/create', [PublicHolidayController::class, 'create'])->name('publicHoliday.create');
        Route::post('/store', [PublicHolidayController::class, 'store'])->name('publicHoliday.store');
        Route::get('/{publicHoliday}/edit', [PublicHolidayController::class, 'edit'])->name('publicHoliday.edit');
        Route::put('/{publicHoliday}', [PublicHolidayController::class, 'update'])->name('publicHoliday.update');
        Route::delete('/{publicHoliday}/delete', [PublicHolidayController::class, 'destroy'])->name('publicHoliday.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'weekly_holiday', 'prefix' => 'weeklyHoliday'], function () {
        Route::get('/', [WeeklyHolidayController::class, 'index'])->name('weeklyHoliday.index');
        Route::get('/create', [WeeklyHolidayController::class, 'create'])->name('weeklyHoliday.create');
        Route::post('/store', [WeeklyHolidayController::class, 'store'])->name('weeklyHoliday.store');
        Route::get('/{weeklyHoliday}/edit', [WeeklyHolidayController::class, 'edit'])->name('weeklyHoliday.edit');
        Route::put('/{weeklyHoliday}', [WeeklyHolidayController::class, 'update'])->name('weeklyHoliday.update');
        Route::delete('/{weeklyHoliday}/delete', [WeeklyHolidayController::class, 'destroy'])->name('weeklyHoliday.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'leave_type', 'prefix' => 'leaveType'], function () {
        Route::get('/', [LeaveTypeController::class, 'index'])->name('leaveType.index');
        Route::get('/create', [LeaveTypeController::class, 'create'])->name('leaveType.create');
        Route::post('/store', [LeaveTypeController::class, 'store'])->name('leaveType.store');
        Route::get('/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('leaveType.edit');
        Route::put('/{leaveType}', [LeaveTypeController::class, 'update'])->name('leaveType.update');
        Route::delete('/{leaveType}/delete', [LeaveTypeController::class, 'destroy'])->name('leaveType.delete');
    });


    Route::group(['section' => 'setup', 'sub_section' => 'leave_group', 'prefix' => 'leaveGroup'], function () {
        Route::get('/', [LeaveGroupController::class, 'index'])->name('leaveGroup.index');
        Route::get('/create', [LeaveGroupController::class, 'create'])->name('leaveGroup.create');
        Route::post('/store', [LeaveGroupController::class, 'store'])->name('leaveGroup.store');
        Route::get('/edit/{leaveGroup}', [LeaveGroupController::class, 'edit'])->name('leaveGroup.edit');
        Route::put('/update/{leaveGroup}', [LeaveGroupController::class, 'update'])->name('leaveGroup.update');
        Route::delete('/delete/{leaveGroup}', [LeaveGroupController::class, 'destroy'])->name('leaveGroup.delete');
        Route::post('/storeSetting/{leaveGroup}/', [LeaveGroupController::class, 'storeSetting'])->name('leaveGroup.addSetting');
        Route::delete('/deleteEmployee/{leaveGroup}/{employee}/', [LeaveGroupController::class, 'deleteEmployee'])->name('leaveGroup.deleteEmployee');
        Route::post('/addEmployee/{leaveGroup}/{employee}/', [LeaveGroupController::class, 'addEmployee'])->name('leaveGroup.addEmployee');
        Route::delete('/deleteEmployees/{leaveGroup}/', [LeaveGroupController::class, 'deleteEmployees'])->name('leaveGroup.deleteEmployees.bulk');
        Route::post('/addEmployees/{leaveGroup}/', [LeaveGroupController::class, 'addEmployees'])->name('leaveGroup.addEmployees.bulk');
        Route::get('/listEmployees/{leaveGroup}/', [LeaveGroupController::class, 'listEmployees'])->name('leaveGroup.listEmployees');
        Route::get('/show/{leaveGroup}/', [LeaveGroupController::class, 'show'])->name('leaveGroup.show');
    });

    Route::group(['section' => 'leaves', 'sub_section' => 'apply_for_leave', 'prefix' => 'applyForLeave'], function () {
        Route::get('/', [ApplyForLeaveController::class, 'index'])->name('applyForLeave.index');
        Route::get('/create', [ApplyForLeaveController::class, 'create'])->name('applyForLeave.create');
        Route::post('/store', [ApplyForLeaveController::class, 'store'])->name('applyForLeave.store');
        Route::post('getEmployeeLeaveBalance', [ApplyForLeaveController::class, 'getEmployeeLeaveBalance'])->name('leave.employee.balance');
        Route::get('/{applyForLeave}', [RequestedApplicationController::class, 'viewDetails'])->name('applyForLeave.show');

        // Apply on behalf routes - properly grouped under Leave Management
        Route::get('applyOnBehalf/create', [ApplyForLeaveController::class, 'applyOnBehalfCreate'])->name('applyOnBehalf.create');
        Route::post('applyOnBehalf/store', [ApplyForLeaveController::class, 'applyOnBehalfStore'])->name('applyOnBehalf.store');
        Route::post('applyOnBehalf/balance', [ApplyForLeaveController::class, 'getEmployeeLeaveBalance'])->name('applyOnBehalf.balance');
        Route::post('applyOnBehalf/totalDays', [ApplyForLeaveController::class, 'applyForTotalNumberOfDays'])->name('applyOnBehalf.totalDays');
        Route::get('applyOnBehalf/employeeDetails/{employeeId}', [ApplyForLeaveController::class, 'getEmployeeDetails'])->name('applyOnBehalf.employeeDetails');
        Route::get('applyOnBehalf/employeeLeaveTypes/{employeeId}', [ApplyForLeaveController::class, 'getEmployeeLeaveTypes'])->name('applyOnBehalf.employeeLeaveTypes');
    });

    Route::group(['section' => 'leaves', 'sub_section' => 'configure_leave', 'prefix' => 'requestedApplication'], function () {
        Route::get('/', [RequestedApplicationController::class, 'index'])->name('requestedApplication.index');
        Route::get('/{requestedApplication}/viewDetails', [RequestedApplicationController::class, 'viewDetails'])->name('requestedApplication.viewDetails');
        Route::put('/{requestedApplication}', [RequestedApplicationController::class, 'update'])->name('requestedApplication.update');
    });

    Route::group(['section' => 'leaves', 'sub_section' => 'admin_reports'], function () {
         Route::get('leaveReport', [ReportController::class, 'employeeLeaveReport'])->name('leaveReport.leaveReport.form');
         Route::post('leaveReport', [ReportController::class, 'employeeLeaveReport'])->name('leaveReport.leaveReport.download');
        Route::get('downloadLeaveReport', [ReportController::class, 'downloadLeaveReport'])->name('leave.admin.report.download');;

        Route::get('summaryReport', [ReportController::class, 'summaryReport'])->name('summaryReport.summaryReport.form');
        Route::post('summaryReport', [ReportController::class, 'summaryReport'])->name('summaryReport.summaryReport.download');
        Route::get('downloadSummaryReport', [ReportController::class, 'downloadSummaryReport'])->name('leave.summaryReport.download');
        Route::get('leaveReport/balances', [ReportController::class, 'leaveBalances'])->name('leave.report.balances.form');
        Route::post('leaveReport/balances/download', [ReportController::class, 'leaveBalances'])->name('leave.report.balances.download');

        //Full leave report routes
        Route::get('fullOrganizationReport', [ReportController::class, 'fullOrganizationReport'])->name('leaveReport.fullOrganizationReport');
        Route::post('fullOrganizationReport', [ReportController::class, 'fullOrganizationReport'])->name('leaveReport.fullOrganizationReport.filter');
        Route::get('generateReport', [ReportController::class, 'generateReport'])->name('generateReport.generateReport');
        Route::get('pendingLeaveRequests', [RequestedApplicationController::class, 'hrPending'])->name('pendingLeaveRequests.pendingLeaveRequests');
        Route::get('allLeaveApplications', [RequestedApplicationController::class, 'allLeaveApplications'])->name('allLeaveApplications.allLeaveApplications');
        Route::post('/{application}/recall', [RequestedApplicationController::class, 'recall'])->name('leaveApplication.recall');
        Route::delete('/{application}/delete', [RequestedApplicationController::class, 'destroy'])->name('leaveApplication.delete');
        Route::get('ceoPendingLeaveRequests', [RequestedApplicationController::class, 'ceoPending'])->name('ceoPendingLeaveRequests.ceoPendingLeaveRequests');
        Route::get('downloadStaffReport', [ReportController::class, 'reportPerStaff'])->name('downloadStaffReport.downloadStaffReport');

        Route::get('onLeaveToday', [ReportController::class, 'onLeaveToday'])->name('leave.report.onLeaveToday');

        Route::get('/monthly-leave-consumption', [ReportController::class, 'monthlyLeaveConsumption'])->name('leaveReport.monthlyLeaveConsumption');
        Route::get('/download-monthly-leave-consumption', [ReportController::class, 'downloadMonthlyLeaveConsumption'])->name('downloadleaveReport.monthlyLeaveConsumption');
        Route::get('/export-monthly-leave-consumption', [ReportController::class, 'exportMonthlyLeaveConsumption'])->name('exportleaveReport.monthlyLeaveConsumption');

        // Leave History Report
        Route::get('leaveHistory', [ReportController::class, 'leaveHistory'])->name('leave.report.history');
        Route::get('leaveHistory/{employee_id}', [ReportController::class, 'leaveHistoryDetail'])->name('leave.report.history.detail');

        // Leave Encashment Report
        Route::get('leaveEncashment', [ReportController::class, 'leaveEncashmentReport'])->name('leave.report.encashment');
        Route::get('leaveEncashment/download', [ReportController::class, 'downloadLeaveEncashmentReport'])->name('leave.report.encashment.download');
    });

    Route::group(['section' => 'leaves', 'sub_section' => 'my_reports'], function () {
        Route::get('myLeaveReport', [ReportController::class, 'myLeaveReport'])->name('myLeaveReport.myLeaveReport.view');
        Route::post('myLeaveReport', [ReportController::class, 'myLeaveReport'])->name('myLeaveReport.myLeaveReport.download');
        Route::get('downloadMyLeaveReport', [ReportController::class, 'downloadMyLeaveReport'])->name('leave.myreport.download');
    });

    //Rollover leaves
    Route::group(['section' => 'manage_leaves', 'sub_section' => 'rollover_leaves'], function () {
        Route::get('rolloverLeaves', [LeaveTypeController::class, 'rolloverLeavesIndex'])->name('rolloverLeaves');
        Route::get('rolloverLeave/{id}/edit', [LeaveTypeController::class, 'editRolloverLeave'])->name('rolloverLeaveEdit.view');
        Route::post('rolloverLeave/{id}/edit', [LeaveTypeController::class, 'updateRolloverLeave'])->name('rolloverLeaveEdit.save');
        Route::get('addRolloverLeave1', [LeaveTypeController::class, 'addRolloverLeave'])->name('addRolloverLeave1');
        Route::post('storeRolloverLeave', [LeaveTypeController::class, 'storeRolloverLeave'])->name('storeRolloverLeave');
        Route::delete('rolloverLeave/{id}/delete', [LeaveTypeController::class, 'destroyRollover'])->name('rolloverLeave.delete');
        Route::get('updateDefaultRollovers', [LeaveTypeController::class, 'automaticLeaveRollover'])->name('updateDefaultRollovers');
    });

    //Leave Adjustments
    Route::group(['section' => 'manage_leaves', 'sub_section' => 'leave_adjustments'], function () {
        Route::get('adjustments', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'index'])->name('leave.adjustments.index');
        Route::get('adjustments/create', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'create'])->name('leave.adjustments.create');
        Route::post('adjustments', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'store'])->name('leave.adjustments.store');
        Route::get('adjustments/{id}', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'show'])->name('leave.adjustments.show');
        Route::get('adjustments/{id}/edit', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'edit'])->name('leave.adjustments.edit');
        Route::put('adjustments/{id}', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'update'])->name('leave.adjustments.update');
        Route::delete('adjustments/bulk-destroy', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'bulkDestroy'])->name('leave.adjustments.bulkDestroy');
        Route::delete('adjustments/{id}', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'destroy'])->name('leave.adjustments.destroy');
        Route::get('adjustments/balance/fetch', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'getEmployeeBalance'])->name('leave.adjustments.balance');

        // Bulk upload routes
        Route::get('adjustments/template/download', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'downloadTemplate'])->name('leave.adjustments.template.download');
        Route::get('adjustments/import/form', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'showImportForm'])->name('leave.adjustments.import.form');
        Route::post('adjustments/import', [\App\Http\Controllers\Leave\LeaveAdjustmentController::class, 'import'])->name('leave.adjustments.import');
    });

    Route::group(['section' => 'manage_leaves', 'sub_section' => 'uploads'], function () {
        Route::get('manualUpload', [ApplyForLeaveController::class, 'manualUpload'])->name('leaveManagement.manualUpload');
        Route::get('manualUploadView', [ApplyForLeaveController::class, 'manualUploadView'])->name('leaveManagement.manualUploadView');
        Route::post('manualUploadSave', [DataImportController::class, 'importLeaves'])->name('leaveManagement.manualUploadSave');
    });

    Route::group(['section' => 'manage_leaves', 'sub_section' => 'approve_reject'], function () {
        Route::post('approveOrRejectLeaveApplication', [RequestedApplicationController::class, 'approveOrRejectLeaveApplication'])->name('leave.manage.approve_reject');
    });

    // Leave Schedule Routes
    Route::group(['section' => 'manage_leaves', 'sub_section' => 'leave_schedule', 'prefix' => 'leaveSchedule'], function () {
        Route::get('/', [LeaveScheduleController::class, 'index'])->name('leave.schedule.index');
        Route::get('/create', [LeaveScheduleController::class, 'create'])->name('leave.schedule.create');
        Route::post('/store', [LeaveScheduleController::class, 'store'])->name('leave.schedule.store');
        Route::get('/bulk-upload', [LeaveScheduleController::class, 'bulkUpload'])->name('leave.schedule.bulkUpload');
        Route::post('/bulk-upload', [LeaveScheduleController::class, 'bulkUploadStore'])->name('leave.schedule.bulkUpload.store');
        Route::get('/download-sample', [LeaveScheduleController::class, 'downloadSample'])->name('leave.schedule.sample.download');
        Route::get('/{id}/edit', [LeaveScheduleController::class, 'edit'])->name('leave.schedule.edit');
        Route::put('/{id}', [LeaveScheduleController::class, 'update'])->name('leave.schedule.update');
        Route::delete('/{id}/delete', [LeaveScheduleController::class, 'destroy'])->name('leave.schedule.delete');
        Route::post('/send-reminders', [LeaveScheduleController::class, 'sendReminders'])->name('leave.schedule.reminders');
    });
});

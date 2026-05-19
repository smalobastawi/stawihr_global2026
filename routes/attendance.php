<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Attendance\WorkShiftController;
use App\Http\Controllers\Attendance\AttendanceReportController;
use App\Http\Controllers\Attendance\ManualAttendanceController;
use \App\Http\Controllers\Attendance\AttendanceController;
use \App\Http\Controllers\SystemUpgradeController;
use \App\Http\Controllers\MorphoDeviceController;
use \App\Http\Controllers\MorphoDeviceLogController;
use \App\Http\Controllers\BiometricDeviceController;
use \App\Http\Controllers\Attendance\AnomaliesController;
use \App\Http\Controllers\DataImportController;
use \App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Attendance\OvertimeApprovalController;


Route::group(['module'=>'Attendance','prefix' => 'attendance','middleware' => ['auth', 'permission']], function(){

    Route::group(['section'=>'setup','sub_section'=>'work_shift','prefix' => 'workShift'], function () {
        Route::get('/',[WorkShiftController::class, 'index'])->name('workShift.index');
        Route::get('/create',[WorkShiftController::class, 'create'])->name('workShift.create');
        Route::post('/store',[WorkShiftController::class, 'store'])->name('workShift.store');
        Route::get('/{workShift}/edit',[WorkShiftController::class, 'edit'])->name('workShift.edit');
        Route::put('/{workShift}',[WorkShiftController::class, 'update'])->name('workShift.update');
        Route::delete('/{workShift}/delete',[WorkShiftController::class, 'destroy'])->name('workShift.delete');
    });
 Route::group(['section'=>'reports','sub_section'=>'generate_view' ], function () {
    Route::get('dailyAttendance',[AttendanceReportController::class, 'dailyAttendanceTable'])->name('dailyAttendance.dailyAttendance');
    Route::post('dailyAttendance',[AttendanceReportController::class, 'dailyAttendanceTable'])->name('dailyAttendance.dailyAttendanceFilter');

    Route::get('weeklyAttendance', [AttendanceReportController::class, 'weeklyAttendance'])->name('weeklyAttendance.weeklyAttendance');
    Route::post('weeklyAttendance', [AttendanceReportController::class, 'weeklyAttendance'])->name('weeklyAttendance.weeklyAttendanceFilter');

    Route::get('monthlyAttendance', [AttendanceReportController::class, 'monthlyAttendance'])->name('monthlyAttendance.monthlyAttendance');
    Route::post('monthlyAttendance', [AttendanceReportController::class, 'monthlyAttendance'])->name('monthlyAttendance.monthlyAttendanceFilter');
    Route::post('newMonthlyAttendance', [AttendanceReportController::class, 'newMonthlyAttendance'])->name('newMonthlyAttendance.monthlyAttendance');

    Route::get('myAttendanceReport', [AttendanceReportController::class, 'myAttendanceReport'])->name('myAttendanceReport.myAttendanceReport');
    Route::post('myAttendanceReport', [AttendanceReportController::class, 'myAttendanceReport'])->name('myAttendanceReport.myAttendanceReportFilter');

    Route::get('attendanceSummaryReport', [AttendanceReportController::class, 'attendanceSummaryReport'])->name('attendanceSummaryReport.attendanceSummaryReport');
    Route::post('attendanceSummaryReport', [AttendanceReportController::class, 'attendanceSummaryReport'])->name('attendanceSummaryReport.attendanceSummaryReportFilter');

    Route::get('manualAttendance', [ManualAttendanceController::class, 'manualAttendance'])->name('manualAttendance.manualAttendance');
    Route::get('manualAttendance/filter', [ManualAttendanceController::class, 'filterData'])->name('manualAttendance.filter');
    Route::post('manualAttendanceStore', [ManualAttendanceController::class, 'store'])->name('manualAttendance.store');

    Route::get('downloadDailyAttendance/{id}',[AttendanceReportController::class, 'downloadDailyAttendance'])->name('attendande.daily.download');
    Route::get('exportDailyAttendance/{id}',[AttendanceReportController::class, 'exportDailyAttendance'])->name('attendande.daily.export');
    Route::get('downloadWeeklyAttendance/{id}',[AttendanceReportController::class, 'downloadWeeklyAttendance'])->name('attendande.weekly.download');
    Route::get('downloadWeeklyAttendanceExcel/{id}',[AttendanceReportController::class, 'exportWeeklyAttendance'])->name('attendande.daily.download.excel');
    Route::get('downloadMonthlyAttendance',[AttendanceReportController::class, 'downloadMonthlyAttendance'])->name('attendande.monthly.download');
    Route::get('downloadMyAttendance',[AttendanceReportController::class, 'downloadMyAttendance'])->name('attendance.my.download');
    Route::get('downloadAttendanceSummaryReport/{date}',[AttendanceReportController::class, 'downloadAttendanceSummaryReport'])->name('attendande.summary.download');
    Route::get('mealReport',[AttendanceReportController::class, 'mealReport'])->name('attendance.mealReport')->name('attendande.meal.report');
    Route::post('mealReport',[AttendanceReportController::class, 'mealReport'])->name('attendance.mealReportFilter')->name('attendande.meal.report.filter');;
    //load anomalies report
    Route::get('anomalyReport',[AttendanceReportController::class, 'anomalyReport'])->name('attendance.anomalyReport');
    Route::post('anomalyReport',[AttendanceReportController::class, 'anomalyReport'])->name('attendance.anomalyReportFilter');
   //store the corrected anomalies
    Route::get('attendanceAnomalies',[AnomaliesController::class, 'attendanceAnomalies'])->name('attendance.anomalies');
    Route::post('anomaliesStore',[AnomaliesController::class, 'storeAnomalies'])->name('attendance.anomaliesStore');

    Route::get('correctFromExcel',[DataImportController::class, 'anomaliesCorrections'])->name('attendance.correctFromExcel');
    Route::post('storeFromExcel',[DataImportController::class, 'importAnomalyCorrections'])->name('attendance.storeFromExcel');

    //overtimes here
    Route::get('overtimes', [OvertimeApprovalController::class, 'ovetimes'])->name('attendance.approveOvertimes');
    Route::post('overtimesApprove', [OvertimeApprovalController::class, 'approveOvertime'])->name('attendance.overtimeApproval');
    Route::get('filterOvertime', [OvertimeApprovalController::class, 'filterOvertime'])->name('attendance.filterOvertime');
    Route::get('/update-overtimes-to-payroll', [OvertimeApprovalController::class, 'updateOvertimesToPayroll'])->name('attendance.overtime.update_payroll');

    //raw attendance logs here
    Route::get('view_raw_logs', [AttendanceReportController::class, 'rowAttendanceLogs'])->name('attendance.view_raw_logs');
    Route::post('view_raw_logs', [AttendanceReportController::class, 'rowAttendanceLogs'])->name('attendance.view_raw_logs.filter');
});

    // get attendance by ip

   

    // setup ip  attendance
    Route::group(['section'=>'General','sub_section'=>'manual_attendance'] , function () {
    Route::get('setup-employee-attendance', [ManualAttendanceController::class, 'setupDashboardAttendance'])->name( 'attendance.dashboard');

    Route::post('setup-employee-attendance-post', [ManualAttendanceController::class, 'postDashboardAttendance'])->name('attendance.dashboard.post');

    Route::get('deletedups',  [ManualAttendanceController::class, 'deletedups2022'])->name('duplictes.remove');

    Route::get('newAttendance',  [AttendanceController::class, 'index'])->name('newAttendanceIndex');
    Route::get('newAttendance1/filter', [AttendanceController::class, 'filterData'])->name('newAttendance.filter');
    Route::post('newAttendance1Store', [AttendanceController::class, 'store'])->name('newAttendance.store');

    //Route for upgrading system
    Route::get('/migrateAttendanceData', [SystemUpgradeController::class, 'index'])->name('migrateAttendanceData');
    Route::get('/saveMigrateAttendanceData', [SystemUpgradeController::class, 'migrateData3'])->name('saveMigrateAttendanceData');
    
 
    
});
Route::group(['section'=>'General','sub_section'=>'ip_attendance'] , function () {
Route::post('ip-attendance',[AttendanceController::class, 'ipAttendance'])->name('ip.attendance');
});
 Route::group(['section'=>'devices','sub_section'=>'devices'], function () {
    Route::get('/biometric',[AttendanceController::class, 'biometricAttendance'])->name('biometricGet.index');
    Route::get('/bioDevices',[BiometricDeviceController::class, 'devices'])->name('biometricDevices');
    Route::get('/recordUpdate',[BiometricDeviceController::class, 'getRecords'])->name('biometricUpdate');
    Route::get('/createDevice',[BiometricDeviceController::class, 'createDevices'])->name('createDevice');
    Route::post('/createDevice1',[BiometricDeviceController::class, 'store'])->name('storeDevice');
    Route::get('/editBioDevice{id}',[BiometricDeviceController::class, 'edit'])->name('editBioDevice');
    Route::post('/editBioDevice1',[BiometricDeviceController::class, 'update'])->name('posteditBioDevice');
    Route::delete('/deleteBioDevice{id}',[BiometricDeviceController::class, 'destroy'])->name('deleteBioDevice');
    Route::get('/updateStatus{id}',[BiometricDeviceController::class, 'updateDeviceStatus'])->name('updateStatus');
    Route::get('/biometricAttendance',[AttendanceController::class, 'biometricAttendance'])->name('zkbiometricGet.index');
    Route::get('/devices', [MorphoDeviceController::class, 'index'])->name('devices');
});
   //meal report here
  

});




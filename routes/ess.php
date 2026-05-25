<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ess\EssIndexController;
use App\Http\Controllers\Leave\ReportController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Payroll\GenerateSalarySheet;
use App\Http\Controllers\Surveys\GoogleFormController;
use App\Http\Controllers\Leave\ApplyForLeaveController;
use \App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Feedback\EmployeeFeedbackController;
use App\Http\Controllers\Feedback\AnonymousFeedbackController;
use App\Http\Controllers\Leave\RequestedApplicationController;
use App\Http\Controllers\Leave\LeaveScheduleController;
use App\Http\Controllers\Attendance\AttendanceReportController;
use App\Http\Controllers\Ess\EssLoanController;
use App\Http\Controllers\Employee\EmployeeEducationQualificationController;
use App\Http\Controllers\Vehicle\VehicleAssignmentController;

Route::group(['module' => 'Self Service', 'prefix' => 'ess', 'as' => 'ess.', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {


    Route::group(['section' => 'leaves', 'sub_section' => 'apply_for_leave', 'prefix' => 'leave', 'as' => 'leave.'], function () {
        Route::get('/', [EssIndexController::class, 'leave'])->name('index');
        Route::get('/create', [EssIndexController::class, 'leaveApplyForm'])->name('form');
        Route::get('/edit/{id}', [EssIndexController::class, 'editLeave'])->name('edit');
        Route::post('/update', [EssIndexController::class, 'updateLeave'])->name('update');
        Route::post('/store', [EssIndexController::class, 'leaveStore'])->name('apply.store');
        Route::post('/balance', [EssIndexController::class, 'leaveBalance'])->name('balance');
        Route::post('applyForTotalNumberOfDays', [ApplyForLeaveController::class, 'applyForTotalNumberOfDays'])->name('leave.employee.apply.totaldays');
        Route::get('/{applyForLeave}', [EssIndexController::class, 'viewLeaveDetails'])->name('applyForLeave.show');
        Route::post('/approveOrReject', [EssIndexController::class, 'approveOrRejectLeave'])->name('leave.approveOrReject');
        Route::post('/delete_justification', [EssIndexController::class, 'deleteLeaveJustification'])->name('justification.delete');
        Route::delete('/recall/{id}', [EssIndexController::class, 'recall'])->name('recall');
    });

    Route::group(['section' => 'leaves', 'sub_section' => 'my_reports', 'prefix' => 'leave/report', 'as' => 'leave.report.'], function () {
        Route::get('view', [EssIndexController::class, 'leave'])->name('view');
        Route::post('download', [ReportController::class, 'myLeaveReport'])->name('download');
        Route::get('download2', [ReportController::class, 'downloadMyLeaveReport'])->name('download2');
    });

    // Leave Schedule Routes for ESS
    Route::group(['section' => 'leaves', 'sub_section' => 'scheduled_leaves', 'prefix' => 'leave/scheduled', 'as' => 'leave.scheduled.'], function () {
        Route::get('leaves/', [EssIndexController::class, 'employeeScheduledLeaves'])->name('index');
    });

    Route::group(['section' => 'notifications', 'sub_section' => 'notifications', 'prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('/', [EssIndexController::class, 'notifications'])->name('index');
        Route::get('/mark-all-read', [EssIndexController::class, 'markAllNotificationsRead'])->name('markAllRead');
        Route::get('/{id}/mark-read', [EssIndexController::class, 'markNotificationRead'])->name('markRead');
        Route::delete('/{id}', [EssIndexController::class, 'destroyNotification'])->name('delete');
    });

    Route::group(['section' => 'payrol', 'sub_section' => 'self_payroll', 'prefix' => 'payroll', 'as' => 'payroll.'], function () {
        // Route::get('/', [GenerateSalarySheet::class, 'myPayroll'])->name('index');
        // Route::get('myPayroll/generatePayslip/{id}', [GenerateSalarySheet::class, 'generatePayslip'])->name('payslip.generate'); 

        Route::get('/', [EssIndexController::class, 'myPayroll'])->name('index');
        Route::get('myPayroll/generatePayslip/{id}', [EssIndexController::class, 'generatePayslip'])->name('payslip.generate');
    });

    Route::group(['section' => 'attendance', 'sub_section' => 'attendance', 'as' => 'attendance.', 'prefix' => 'attendance'], function () {
        Route::get('downloadMyAttendance', [AttendanceReportController::class, 'downloadMyAttendance'])->name('download');
        Route::post('checkin', [AttendanceController::class, 'ipAttendance'])->name('create');
    });

    Route::group(['section' => 'approvals', 'sub_section' => 'approval', 'as' => 'approval.', 'prefix' => 'approval'], function () {
        Route::get('/', [EssIndexController::class, 'approval'])->name('index');
        Route::get('/show/{modelType}/{modelId}', [EssIndexController::class, 'approvalShow'])->name('show');
    
      // Approval delegation management
      Route::get('approval-delegations', [EssIndexController::class, 'approvalDelegations'])->name('delegations.index');
      Route::post('approval-delegations1', [EssIndexController::class, 'storeApprovalDelegation'])->name('delegations.store');
      Route::get('approval-delegations/{id}/edit', [EssIndexController::class, 'editApprovalDelegation'])->name('delegations.edit');
      Route::put('approval-delegations/update/{id}', [EssIndexController::class, 'updateApprovalDelegation'])->name('delegations.update');
      Route::delete('approval-delegations/delete/{id}', [EssIndexController::class, 'deleteApprovalDelegation'])->name('delegations.destroy');
      Route::post('approval-delegations/{id}/toggle-status', [EssIndexController::class, 'toggleDelegationStatus'])->name('delegations.toggle-status');
      Route::post('approval-delegations/{id}/deactivate', [EssIndexController::class, 'deactivateDelegation'])->name('delegations.deactivate');
     
    
    });

    Route::group(['section' => 'awards', 'sub_section' => 'awards', 'as' => 'awards.', 'prefix' => 'awards'], function () {
        Route::get('/', [EssIndexController::class, 'awards'])->name('index');
    });

    Route::group(['section' => 'diciplinary', 'sub_section' => 'diciplinary', 'as' => 'diciplinary.', 'prefix' => 'diciplinary'], function () {
        Route::get('/', [EssIndexController::class, 'diciplinary'])->name('index');
        Route::get('/details/{disciplinary}', [EssIndexController::class, 'diciplinaryDetails'])->name('show');
    });

    Route::group(['section' => 'contacts', 'sub_section' => 'contacts', 'as' => 'contacts.', 'prefix' => 'contacts'], function () {
        Route::get('/', [EssIndexController::class, 'contacts'])->name('index');
    });

    Route::group(['section' => 'trainings', 'sub_section' => 'trainings', 'as' => 'trainings.', 'prefix' => 'trainings'], function () {
        Route::get('/', [EssIndexController::class, 'trainings'])->name('index');
        Route::get('/{training}/show', [EssIndexController::class, 'showTraining'])->name('show');
        Route::match(['get', 'post'], '/{training}/{employee?}/{status}/invitation', [EssIndexController::class, 'handleInvitationResponse'])->name('invitation.response')->defaults('employee', auth()->user()->employee->employee_id ?? null);
        Route::get('/attendance/{training}/{employee}/confirm', [EssIndexController::class, 'showTrainingAttendanceConfirmation'])->name('attendance.confirm');
        Route::post('/attendance/{training}/{employee}/confirm', [EssIndexController::class, 'handleAttendanceResponse']);
    });

    Route::group(['section' => 'recruitment', 'sub_section' => 'recruitment', 'as' => 'recruitment.', 'prefix' => 'recruitment'], function () {
        Route::get('/job/posts', [EssIndexController::class, 'jobPosts'])->name('job.posts');
        Route::get('/{id}/job/details', [EssIndexController::class, 'jobPostDetails'])->name('job.details');
        Route::post('/{id}/apply/job', [EssIndexController::class, 'jobApply'])->name('apply.job');
    });

    Route::group(['section' => 'shifts', 'sub_section' => 'shifts', 'as' => 'shifts.', 'prefix' => 'shifts'], function () {
        Route::get('/', [EssIndexController::class, 'shifts'])->name('index');
    });

    Route::group(['section' => 'documents', 'sub_section' => 'documents', 'as' => 'documents.', 'prefix' => 'documents'], function () {

        Route::get('/', [EssIndexController::class, 'documents'])->name('index');
        // Document acknowledgment route
        Route::post('/{document}/acknowledge', [EssIndexController::class, 'acknowledgeDocument'])->name('acknowledge');
        // Serve document file route
        Route::get('/{document}/serve', [EssIndexController::class, 'serveDocument'])->name('serve');
        // upload documents
        Route::post('/{employee}/docs/upload', [EmployeeController::class, 'storeEmployeeDocument'])->name('docs.upload');
    });


    Route::group(['section' => 'employee', 'sub_section' => 'employee', 'as' => 'employee.', 'prefix' => 'employee'], function () {

        Route::get('/edit/profile', [EmployeeController::class, 'editProfile'])->name('edit.profile');
        // Update employee profile
        Route::put('/{employee}/update/profile', [EmployeeController::class, 'updateProfile'])->name('update.profile');

        // employee qualifications 
        Route::post('/{employee}/qualification/store', [EmployeeEducationQualificationController::class, 'store'])->name('qualification.store');

        // employee proffessional experience
        Route::post('/{employee}/experience/store', [EmployeeController::class, 'storeProffessionalExperience'])->name('experience.store');
    });

    Route::group(['section' => 'Feedback', 'sub_section' => 'ess_feedback'], function () {
        Route::name('feedback.')->prefix('feedback')->group(function () {
            Route::get('/', [EmployeeFeedbackController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeFeedbackController::class, 'create'])->name('create');
            Route::post('/store', [EmployeeFeedbackController::class, 'store'])->name('store');
            Route::get('/{id}/view', [EmployeeFeedbackController::class, 'view'])->name('view');
            Route::get('/show/{id}', [EmployeeFeedbackController::class, 'show'])->name('show');
            Route::put('/{id}/update', [EmployeeFeedbackController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [EmployeeFeedbackController::class, 'destroy'])->name('delete');
            Route::get('/create-anonymous', [AnonymousFeedbackController::class, 'createAnonymous'])->name('anonymous.create');
            Route::post('/store-anonymous', [AnonymousFeedbackController::class, 'storeAnonymous'])->name('anonymous.store');
        });
    });

    // survey form 
    Route::group(['section' => 'survey', 'sub_section' => 'survey', 'as' => 'survey.', 'prefix' => 'survey'], function () {
        // Example route to view forms (requires authentication first)
        Route::get('/', [EssIndexController::class, 'survey'])->name('index');
        // Example route to get responses (requires authentication first)
        // Route::get('/forms/{formId}/responses', [GoogleFormController::class, 'getFormResponses'])->name('forms.responses')->middleware('ensure.google.token');
    });

    Route::group(['section' => 'subordinates', 'sub_section' => 'subordinates', 'prefix' => 'subordinates', 'as' => 'subordinates.'], function () {
        Route::get('/', [EssIndexController::class, 'subordinates'])->name('index');
    });

    Route::group(['section' => 'loans', 'sub_section' => 'my_loans', 'prefix' => 'loans', 'as' => 'loans.'], function () {
        Route::get('/', [EssLoanController::class, 'index'])->name('index');
        Route::get('/create', [EssLoanController::class, 'create'])->name('create');
        Route::post('/store', [EssLoanController::class, 'store'])->name('store');
        Route::get('/show/{id}', [EssLoanController::class, 'show'])->name('show');
    });

    // Performance Self Evaluation
    Route::group(['section' => 'performance', 'sub_section' => 'self_evaluation', 'prefix' => 'performance', 'as' => 'performance.'], function () {
        Route::get('/my-appraisals', [EssIndexController::class, 'myAppraisals'])->name('myAppraisals');
        Route::get('/self-evaluation', [EssIndexController::class, 'goToSelfEvaluation'])->name('selfEvaluation');
        Route::get('/{appraisal}/selfReview', [EssIndexController::class, 'selfReview'])->name('selfReview');
        Route::post('/{appraisal}/saveSelfReview', [EssIndexController::class, 'saveSelfReview'])->name('saveSelfReview');
        Route::post('/{appraisal}/submitSelfReview', [EssIndexController::class, 'submitSelfReview'])->name('submitSelfReview');
        Route::get('/{appraisal}/show', [EssIndexController::class, 'showAppraisal'])->name('show');
    });

    // My PIP Plans
    Route::group(['section' => 'performance', 'sub_section' => 'my_pip', 'prefix' => 'pip', 'as' => 'pip.'], function () {
        Route::get('/my-plans', [EssIndexController::class, 'myPipPlans'])->name('myPlans');
        Route::get('/{plan}/show', [EssIndexController::class, 'showPipPlan'])->name('show');
    });

    // My Vehicle & Assignment History
    Route::group(['section' => 'vehicles', 'sub_section' => 'my_vehicle', 'prefix' => 'vehicle', 'as' => 'vehicle.'], function () {
        Route::get('/my-vehicle', [EssIndexController::class, 'myVehicle'])->name('myVehicle');
    });
});

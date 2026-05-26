<?php

use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MorphoDeviceLogController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\RemoteLogController;
use App\Http\Controllers\Api\BiometricDevicesController;
use App\Http\Controllers\Api\DataSynchronizationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\ApiApprovalController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ApprovalsController;
use App\Http\Controllers\Api\ApprovalSettingsController;
use App\Http\Controllers\Api\LeaveApprovalController;
use App\Http\Controllers\Api\EmployeeDocumentsController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\Api\LeaveApiController;
use App\Http\Controllers\Api\Leave\LeaveApplicationApiController;
use App\Http\Controllers\Api\DisciplinaryController;
use App\Http\Controllers\Api\PipController;
use App\Http\Controllers\Api\NotificationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('checkloginOptions', [AuthController::class, 'checkloginOptions']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::get('password-change-options', [AuthController::class, 'passwordChangeOptions']);
        Route::post('send-password-change-otp', [AuthController::class, 'sendPasswordChangeOtp']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('savelogs', [MorphoDeviceLogController::class, 'store'])->name('morphologs.store');
    Route::post('updateDeviceList', [BiometricDevicesController::class, 'updateDeviceList'])->name('updateDeviceList');
});

// Public Routes
Route::post('attendance', [ApiAttendanceController::class, 'store']);

Route::post('updateAttendanceTable', [RemoteLogController::class, 'updateAttendanceTable'])->name('morphologs.update');

Route::get('syncEmployeeData', [DataSynchronizationController::class, 'syncEmployeeData'])->name('api.syncEmployeeData');
Route::post('license_check', [LicenseController::class, 'checkLicense'])->name('check_license');
Route::get('attendance/update-password', [AttendanceController::class, 'updatePassword']);

// Password Reset Routes
Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::post('validate-token', [ForgotPasswordController::class, 'validateToken']);
Route::post('send-password-change-otp', [LoginController::class, 'sendPasswordChangeOtp'])->name('send-password-change-otp');

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/dashboard', function () {
        // Dashboard logic here
    });

    // Employee Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });
    Route::get('employee/profile', [EmployeeController::class, 'profile']);

    // Department Routes
    Route::get('/department/profile', [DepartmentController::class, 'profile']);

    // Attendance Routes
    Route::get('/attendance/clock-status', [AttendanceController::class, 'getClockStatus']);
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin']);



    // Leave Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/leave-types', [LeaveController::class, 'getLeaveTypes']);

        Route::prefix('leave')->group(function () {
            // Leave Applications
            Route::get('applications', [LeaveController::class, 'getLeaveApplications']);
            Route::post('apply', [LeaveController::class, 'applyLeave']);
            Route::get('balance', [LeaveController::class, 'getLeaveBalance']);
            Route::post('calculate-days', [LeaveController::class, 'calculateTotalLeaveDays']);
            Route::get('requested-applications', [LeaveController::class, 'requestedApplications']);

            //         // Leave Management
            Route::get('/pending', [LeaveApprovalController::class, 'getPendingLeaves']);
            Route::post('/approve-all', [LeaveApprovalController::class, 'approveAllLeaves']);
            Route::post('/reject-all', [LeaveApprovalController::class, 'rejectAllLeaves']);
        });
    });

    // Approval Routes
    Route::prefix('approvals')->group(function () {
        Route::get('/pending', [ApprovalsController::class, 'getPendingApprovals']);
        Route::get('/history', [ApprovalsController::class, 'getApprovalHistory']);
        Route::get('/requests', [ApprovalsController::class, 'getApprovalRequests']);
        Route::post('/take-action', [ApprovalsController::class, 'takeAction']);
        Route::post('/create', [ApprovalsController::class, 'createApprovalRequest']);
    });
    Route::get('/pending/all', [ApprovalsController::class, 'getAllPendingApprovals']);
    Route::get('/history/all', [ApprovalsController::class, 'getAllApprovalHistory']);
    Route::get('/requests/all', [ApprovalsController::class, 'getAllApprovalRequests']);
    Route::get('/my-approvals', [ApprovalsController::class, 'getUserApprovalDetails']);

    // Approval Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/approval', [ApprovalSettingsController::class, 'getApprovalSettings']);
        Route::post('/approval', [ApprovalSettingsController::class, 'createApprovalSettings']);
    });

    // Documents Routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [EmployeeDocumentsController::class, 'index']);
        Route::post('/', [EmployeeDocumentsController::class, 'store']);
        Route::get('/{uuid}', [EmployeeDocumentsController::class, 'show']);
        Route::put('/{uuid}', [EmployeeDocumentsController::class, 'update']);
        Route::delete('/{uuid}', [EmployeeDocumentsController::class, 'destroy']);
    });

    // Approval Request Routes
    Route::prefix('approval-requests')->group(function () {
        Route::get('/', [ApiApprovalController::class, 'getApprovalRequests']);
        Route::get('/{id}', [ApiApprovalController::class, 'getApprovalRequestDetails']);
        Route::post('/{id}/status', [ApiApprovalController::class, 'updateApprovalStatus']);
        Route::get('/records', [ApiApprovalController::class, 'getApprovalRecords']);
        Route::post('/', [ApiApprovalController::class, 'submitApprovalRequest']);
    });
});

// Payroll Routes
Route::prefix('payroll')->group(function () {
    Route::post('test-tax-setup', [PayrollController::class, 'testTaxSetup'])->name('api.payroll.testTaxSetup');
    Route::post('test-allowance', [PayrollController::class, 'testAllowance'])->name('api.payroll.testAllowance');
    Route::post('test-deduction', [PayrollController::class, 'testDeduction'])->name('api.payroll.testDeduction');
    Route::post('test-payroll-generation', [PayrollController::class, 'testPayrollGeneration'])->name('api.payroll.testPayrollGeneration');
    Route::get('salary/bonus-types', [PayrollController::class, 'getSalaryBonusTypes'])->name('api.payroll.getSalaryBonusTypes');
    Route::post('salary/bonus', [PayrollController::class, 'storeSalaryBonus'])->name('api.payroll.storeSalaryBonus');
    Route::post('salary/calculate-final-salary', [PayrollController::class, 'calculateFinalSalary']);
    Route::get('salary/deduction-rules', [PayrollController::class, 'getSalaryDeductionRules'])->name('api.payroll.getSalaryDeductionRules');
    Route::put('salary/deduction-rules', [PayrollController::class, 'updateSalaryDeductionRules'])->name('api.payroll.updateSalaryDeductionRules');
    Route::post('calculate-yearly-salary', [PayrollController::class, 'calculateYearlySalary'])->name('api.payroll.calculateYearlySalary');
    // These routes with {id} parameter must come AFTER the routes without parameter
    Route::get('salary-details/{id}', [PayrollController::class, 'getAuthUserSalaryDetails'])->name('api.payroll.salaryDetails');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('payroll/salary-details', [PayrollController::class, 'getAuthUserSalaryDetails']);
    Route::get('payroll/yearly-salary', [PayrollController::class, 'calculateAuthUserYearlySalary']);
    Route::get('payroll/recent-payslips', [PayrollController::class, 'getRecentPayslips'])->name('api.payroll.recentPayslips');
    Route::get('payroll/payslip/{id}', [PayrollController::class, 'getPayslipDetail']);
    Route::get('payroll/payslip/{id}/url', [PayrollController::class, 'getPayslipUrl']);
    Route::get('payroll/payslip/{id}/view', [PayrollController::class, 'viewPayslip']);
});
Route::get('/approvals/pending/all', [ApprovalsController::class, 'getAllPendingApprovals']);
Route::get('/approvals/requests/all', [ApprovalsController::class, 'getAllApprovalRequests']);

Route::middleware('auth:sanctum')->group(function () {
    // Employee Leave Routes
    Route::prefix('leave')->group(function () {
        Route::get('/applications', [LeaveController::class, 'getLeaveApplications']);
        Route::post('/apply', [LeaveController::class, 'applyLeave']);
        Route::get('/balance', [LeaveController::class, 'getLeaveBalance']);
        Route::get('/calculate-days', [LeaveController::class, 'calculateTotalLeaveDays']);
        Route::get('/requested', [LeaveController::class, 'requestedApplications']);
        Route::get('/all-requested', [LeaveController::class, 'allRequestedApplications']);
    });

    // Leave Approval Routes
    Route::prefix('leave-approval')->group(function () {
        Route::get('/pending', [LeaveApprovalController::class, 'getPendingApprovals']);
        Route::get('/history', [LeaveApprovalController::class, 'getApprovalHistory']);
    });
});
Route::post('/leave/approval', [LeaveApprovalController::class, 'processApproval']); // HR & CEO Approval/Rejection
Route::get('/leave-approvals/pending', [LeaveApprovalController::class, 'getPendingApprovals']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attendance/clock-status', [AttendanceController::class, 'getClockStatus']);
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin']);
    Route::get('/attendance/get', [AttendanceController::class, 'getAttendance']);
});
// Daily attendance report
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/supervisor-data/{type}', 'AttendanceController@getSupervisorData');

    Route::get('/attendance/daily', [AttendanceController::class, 'dailyAttendance']);
    // Weekly attendance report
    Route::get('/attendance/weekly', [AttendanceController::class, 'weeklyAttendance']);

    // Monthly attendance report
    Route::get('/attendance/monthly', [AttendanceController::class, 'monthlyAttendance']);

    // My attendance summary report
    Route::get('/attendance/my-summary', [AttendanceController::class, 'myAttendanceReport']);

    Route::get('/supervisor-data/{type}', [AttendanceController::class, 'getSupervisorData'])
        ->where('type', 'departments|employee_types|work_shifts|supervised_employees');
});

Route::get('/supervised-employee-attendance', [AttendanceController::class, 'getSupervisedEmployeeAttendance']);


// Helper routes for supervisors
Route::prefix('attendance/supervisor')->middleware('auth:sanctum')->group(function () {
    Route::get('/departments', [AttendanceController::class, 'getDepartments']);
    Route::get('/employee-types', [AttendanceController::class, 'getEmployeeTypes']);
    Route::get('/work-shifts', [AttendanceController::class, 'getWorkShifts']);
    Route::get('/employees', [AttendanceController::class, 'getSupervisedEmployees']);
    Route::get('/supervised-employee-daily-attendance', [AttendanceController::class, 'getSupervisedEmployeeDailyAttendance']);
    Route::get('/count-supervised-employees-attendance', [AttendanceController::class, 'countSupervisedEmployeesWithAttendance']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Check leave balance
    Route::get('leave/balance', [LeaveController::class, 'getEmployeeLeaveBalance']);

    // Calculate leave days 
    Route::post('leave/calculate-days', [LeaveController::class, 'calculateLeaveDays']);

    // Apply for leave
        Route::post('leave/apply', [LeaveController::class, 'applyLeave']);

    // Get all leave applications for the authenticated employee
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');

    // Get leave application form data (leave types, employee info)
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');

    // Store a new leave application
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

    // Apply alias
    Route::post('/apply', [LeaveController::class, 'applyLeave']);

    // Get a specific leave application details
    Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leaves.show');

    // Update a leave application
    Route::put('/leaves/{id}', [LeaveController::class, 'update'])->name('leaves.update');

    // Delete a leave application
    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

    // Get leave application for editing
    Route::get('/leaves/{id}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');

    // Recall a leave application
    Route::post('/leaves/{id}/recall', [LeaveController::class, 'recall'])->name('leaves.recall');

    // Delete justification document
    Route::delete('/leaves/justification', [LeaveController::class, 'deleteJustification'])->name('leaves.delete-justification');

    // Supervisor: View pending leave applications of supervised employees
    Route::get('/leaves/supervisor/pending', [LeaveController::class, 'supervisorPendingLeaves'])->name('leaves.supervisor.pending');

    

    // Supervisor: Approve or reject a leave application
    Route::post('/leaves/approve-reject', [LeaveController::class, 'approveOrReject'])->name('leaves.approve-reject');

    // Supervisor: Personal leave report
    Route::get('/leaves/reports/personal', [LeaveController::class, 'personalLeaveReport'])->name('leaves.reports.personal');

    // Supervisor: Report of approved leaves for supervised employees
    Route::get('/leaves/reports/approved', [LeaveController::class, 'approvedLeavesReport'])->name('leaves.reports.approved');
});


Route::post('/auth/google-login', [AuthController::class, 'loginWithGoogle']);
Route::post('/auth/azure-login', [AuthController::class, 'loginWithAzure']);

Route::middleware('auth:sanctum')->group(function () {




    Route::get('/pending-approvals', [ApprovalsController::class, 'getPendingApprovals']);

    // Get user's approval details
    Route::get('/my-approvals', [ApprovalsController::class, 'getUserApprovalDetails']);

    // Get user's approval history
    Route::get('/approval-history', [ApprovalsController::class, 'getApprovalHistory']);

    // Get all approval requests for the authenticated user
    Route::get('/approval-requests', [ApprovalsController::class, 'getApprovalRequests']);

    // Take action on an approval request (approve/reject)
    Route::post('/approval-action', [ApprovalsController::class, 'takeAction']);

    // Create a new approval request
    Route::post('/create-approval', [ApprovalsController::class, 'createApprovalRequest']);

    // Get pending leave approvals for supervised employees
    Route::get('/pending-leave-approvals', [ApprovalsController::class, 'getAllPendingApprovals']);

    // Get all approval history (admin access, if applicable)
    Route::get('/all-approval-history', [ApprovalsController::class, 'getAllApprovalHistory']);

    // Get all approval requests (admin access, if applicable)
    Route::get('/all-approval-requests', [ApprovalsController::class, 'getAllApprovalRequests']);
});


// Leave approval/rejection routes (auth:api - for legacy API)
Route::middleware('auth:api')->group(function () {
    Route::post('/leave/approve', [LeaveController::class, 'approveLeave']);
    Route::post('/leave/reject', [LeaveController::class, 'rejectLeave']);
});

// Leave approval/rejection routes (auth:sanctum - for mobile app)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/leave/approve', [LeaveController::class, 'approveLeave']);
    Route::post('/leave/reject', [LeaveController::class, 'rejectLeave']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('/supervisor/approve-leave', [LeaveController::class, 'approveLeave']);
    Route::post('/supervisor/reject-leave', [LeaveController::class, 'rejectLeave']);
});
Route::middleware('auth:api')->get('/user/leaves', [LeaveController::class, 'getUserLeaves']);

Route::get('/employee/supervisor', [LeaveController::class, 'getSupervisor']);
Route::middleware('auth:sanctum')->get('/employee/supervisor', [LeaveController::class, 'getSupervisor']);




Route::middleware('auth:api')->group(function () {
    Route::get('/leave/regional-applications', [LeaveController::class, 'getRegionalLeaveApplications']);
    Route::get('/leave/regional-attendance', [LeaveController::class, 'getRegionalAttendance']);
});
Route::get('/supervisor', [LeaveController::class, 'getSupervisor'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get(
        'attendance/supervised-employees/today',
        [App\Http\Controllers\Api\AttendanceController::class, 'getSupervisedEmployeesTodayAttendance']
    );

    Route::get(
        'attendance/supervised-employees/today/count',
        [App\Http\Controllers\Api\AttendanceController::class, 'getSupervisedEmployeesTodayAttendanceCount']
    );
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get(
        'leaves/supervisor/today',
        [App\Http\Controllers\Api\LeaveController::class, 'supervisorTodayLeaves']
    );

    Route::get(
        'leaves/supervisor/today/count',
        [App\Http\Controllers\Api\LeaveController::class, 'supervisorTodayLeavesCount']
    );
});
// Route::get('/supervisor', [LeaveController::class, 'getSupervisor']);
Route::middleware(['auth:sanctum', 'api'])->get('/supervisor', [LeaveController::class, 'getSupervisor']);
// Add this to your routes/api.php file
Route::middleware('auth:sanctum')->get('/supervisor', [App\Http\Controllers\Api\AttendanceController::class, 'getSupervisorDetails']);
// In routes/api.php
Route::post('verify-email', [App\Http\Controllers\Api\AuthController::class, 'verifyEmailAndGenerateToken']);
Route::middleware('auth:sanctum')->get('/supervisor/reports/rejected-leaves', [App\Http\Controllers\Api\LeaveController::class, 'rejectedLeavesReport']);
Route::get('/supervisor/employees-on-leave-today', [LeaveController::class, 'supervisorEmployeesOnLeaveToday'])->middleware('auth:api');
Route::middleware('auth:sanctum')->get('leave/is-supervisor', [LeaveController::class, 'isSupervisor']);
Route::middleware('auth:sanctum')->get('/my-work-shift', [App\Http\Controllers\Api\AttendanceController::class, 'getMyWorkShift']);

Route::middleware('auth:sanctum')->prefix('disciplinary')->group(function () {
    Route::get('/cases', [DisciplinaryController::class, 'index']);
    Route::get('/cases/{id}', [DisciplinaryController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('pip')->group(function () {
    Route::get('/plans', [PipController::class, 'index']);
    Route::get('/plans/{id}', [PipController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

// Feedback API Routes
// Public routes for anonymous feedback
Route::prefix('feedback')->group(function () {
    Route::get('/categories', [App\Http\Controllers\Api\FeedbackController::class, 'getCategories']);
    Route::post('/anonymous', [App\Http\Controllers\Api\FeedbackController::class, 'submitAnonymousFeedback']);
});

// Protected routes for authenticated employees (general feedback)
Route::middleware('auth:sanctum')->prefix('feedback')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\FeedbackController::class, 'getEmployeeFeedback']);
    Route::get('/{id}', [App\Http\Controllers\Api\FeedbackController::class, 'getEmployeeFeedbackDetails']);
    Route::post('/', [App\Http\Controllers\Api\FeedbackController::class, 'submitEmployeeFeedback']);
    Route::delete('/{id}', [App\Http\Controllers\Api\FeedbackController::class, 'deleteEmployeeFeedback']);
});

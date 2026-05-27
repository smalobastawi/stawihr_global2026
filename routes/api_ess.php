<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DisciplinaryController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\PipController;
use App\Http\Controllers\Api\EssBootstrapController;

/*
|--------------------------------------------------------------------------
| Employee Self-Service (ESS) Mobile API Routes
|--------------------------------------------------------------------------
| Base URL: /api/ess
| All routes require Sanctum authentication unless noted otherwise.
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('bootstrap', [EssBootstrapController::class, 'bootstrap']);

    Route::get('employee/profile', [EmployeeController::class, 'profile']);
    Route::get('employee/supervisor', [LeaveController::class, 'getSupervisor']);

    Route::get('leave-types', [LeaveController::class, 'getLeaveTypes']);

    Route::prefix('leave')->group(function () {
        Route::get('balance', [LeaveController::class, 'getLeaveBalance']);
        Route::get('balances', [LeaveController::class, 'getAllLeaveBalances']);
        Route::post('calculate-days', [LeaveController::class, 'calculateLeaveDays']);
        Route::post('apply', [LeaveController::class, 'applyLeave']);
    });

    Route::get('leaves', [LeaveController::class, 'index'])->name('ess.api.leaves.index');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('ess.api.leaves.create');
    Route::get('leaves/reports/personal', [LeaveController::class, 'personalLeaveReport']);
    Route::get('leaves/{id}', [LeaveController::class, 'show'])->name('ess.api.leaves.show');
    Route::put('leaves/{id}', [LeaveController::class, 'update'])->name('ess.api.leaves.update');
    Route::delete('leaves/{id}', [LeaveController::class, 'destroy'])->name('ess.api.leaves.destroy');
    Route::post('leaves/{id}/recall', [LeaveController::class, 'recall'])->name('ess.api.leaves.recall');

    Route::prefix('payroll')->group(function () {
        Route::get('recent-payslips', [PayrollController::class, 'getRecentPayslips']);
        Route::get('payslip/{id}', [PayrollController::class, 'getPayslipDetail']);
        Route::get('payslip/{id}/url', [PayrollController::class, 'getPayslipUrl']);
    });

    Route::prefix('attendance')->group(function () {
        Route::get('clock-status', [AttendanceController::class, 'getClockStatus']);
        Route::post('checkin', [AttendanceController::class, 'checkin']);
        Route::get('get', [AttendanceController::class, 'getAttendance']);
        Route::get('monthly', [AttendanceController::class, 'monthlyAttendance']);
    });

    Route::get('my-work-shift', [AttendanceController::class, 'getMyWorkShift']);

    Route::prefix('disciplinary')->group(function () {
        Route::get('cases', [DisciplinaryController::class, 'index']);
        Route::get('cases/{id}', [DisciplinaryController::class, 'show']);
    });

    Route::prefix('pip')->group(function () {
        Route::get('plans', [PipController::class, 'index']);
        Route::get('plans/{id}', [PipController::class, 'show']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('{id}', [NotificationController::class, 'destroy']);
    });

    Route::prefix('notices')->group(function () {
        Route::get('/', [NoticeController::class, 'index']);
        Route::get('{id}', [NoticeController::class, 'show']);
    });
});

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EssBootstrapController extends Controller
{
    /**
     * Aggregate critical ESS data for mobile app initial load (single round trip).
     */
    public function bootstrap(Request $request)
    {
        try {
            $authController = app(AuthController::class);
            $employeeController = app(EmployeeController::class);
            $leaveController = app(LeaveController::class);
            $payrollController = app(PayrollController::class);
            $attendanceController = app(AttendanceController::class);
            $notificationController = app(NotificationController::class);
            $noticeController = app(NoticeController::class);

            $userPayload = json_decode($authController->user($request)->getContent(), true);
            $profilePayload = json_decode($employeeController->profile()->getContent(), true);
            $balancesPayload = json_decode($leaveController->getAllLeaveBalances($request)->getContent(), true);
            $payslipsPayload = json_decode($payrollController->getRecentPayslips($request)->getContent(), true);
            $clockPayload = json_decode($attendanceController->getClockStatus($request)->getContent(), true);
            $notificationsPayload = json_decode($notificationController->index($request)->getContent(), true);
            $noticesPayload = json_decode($noticeController->index($request)->getContent(), true);
            $leavesPayload = json_decode($leaveController->index($request)->getContent(), true);
            $supervisorPayload = json_decode($leaveController->getSupervisor($request)->getContent(), true);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $userPayload['user'] ?? null,
                    'profile' => $profilePayload,
                    'leave_balances' => $balancesPayload['data'] ?? [],
                    'leave_requests' => $leavesPayload['data']['data'] ?? $leavesPayload['data'] ?? [],
                    'payslips' => [
                        'data' => $payslipsPayload['data'] ?? [],
                        'summary' => $payslipsPayload['summary'] ?? null,
                    ],
                    'clock_status' => $clockPayload,
                    'notifications' => $notificationsPayload,
                    'notices' => $noticesPayload['data'] ?? $noticesPayload,
                    'supervisor' => $supervisorPayload['data'] ?? null,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('ESS bootstrap error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load bootstrap data',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

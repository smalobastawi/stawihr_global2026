<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Lib\Enumerations\LeaveStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EssApprovalController extends Controller
{
    /**
     * Get the supervisor code for the currently authenticated employee
     */
    protected function getSupervisorCode()
    {
        try {
            $employee = Employee::where('user_id', auth()->id())->first();
            return $employee?->employee_id;
        } catch (\Exception $e) {
            Log::error('Error in getSupervisorCode: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if the authenticated user is a supervisor
     */
    protected function checkIfSupervisor(): bool
    {
        try {
            $supervisorCode = $this->getSupervisorCode();
            if (!$supervisorCode) {
                return false;
            }
            return Employee::where('supervisor_id', $supervisorCode)->exists();
        } catch (\Exception $e) {
            Log::error('Error checking supervisor status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all pending leave approvals for supervised employees
     */
    public function getPendingLeaveApprovals(Request $request)
    {
        try {
            $isSupervisor = $this->checkIfSupervisor();
            $supervisorCode = $this->getSupervisorCode();

            if (!$isSupervisor || !$supervisorCode) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User is not a supervisor',
                    'count' => 0,
                    'data' => [],
                ], 200);
            }

            // Get employees supervised by the current user
            $supervisedEmployees = Employee::where('supervisor_id', $supervisorCode)
                ->pluck('employee_id')
                ->toArray();

            if (empty($supervisedEmployees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'count' => 0,
                    'data' => [],
                ], 200);
            }

            // Get current financial year for context
            $currentDate = now();
            $fiscalYear = DB::table('financial_year')
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->first();

            $fiscalStartDate = $fiscalYear?->start_date ?? Carbon::now()->startOfYear()->format('Y-m-d');
            $fiscalEndDate = $fiscalYear?->end_date ?? Carbon::now()->endOfYear()->format('Y-m-d');

            // Get pending leave applications for supervised employees
            $pendingApprovals = LeaveApplication::with([
                'employee' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name', 'email', 'designation_id', 'department_id', 'location_id');
                },
                'employee.designation' => function ($query) {
                    $query->select('designation_id', 'designation_name');
                },
                'employee.department' => function ($query) {
                    $query->select('department_id', 'department_name');
                },
                'employee.branch' => function ($query) {
                    $query->select('location_id', 'location_name');
                },
                'leaveType' => function ($query) {
                    $query->select('leave_type_id', 'leave_type_name');
                }
            ])
                ->whereIn('employee_id', $supervisedEmployees)
                ->where('final_status', LeaveStatus::PENDING)
                ->whereBetween('application_date', [$fiscalStartDate, $fiscalEndDate])
                ->orderBy('application_date', 'desc')
                ->get();

            // Transform data for mobile app
            $transformedData = $pendingApprovals->map(function ($leave) {
                return [
                    'id' => $leave->leave_application_id,
                    'leave_application_id' => $leave->leave_application_id,
                    'employee_id' => $leave->employee_id,
                    'employee' => [
                        'employee_id' => $leave->employee?->employee_id,
                        'first_name' => $leave->employee?->first_name,
                        'last_name' => $leave->employee?->last_name,
                        'full_name' => trim(($leave->employee?->first_name ?? '') . ' ' . ($leave->employee?->last_name ?? '')),
                        'email' => $leave->employee?->email,
                        'designation' => $leave->employee?->designation?->designation_name,
                        'department' => $leave->employee?->department?->department_name,
                        'branch' => $leave->employee?->branch?->location_name,
                    ],
                    'leave_type' => [
                        'leave_type_id' => $leave->leaveType?->leave_type_id,
                        'leave_type_name' => $leave->leaveType?->leave_type_name,
                    ],
                    'application_from_date' => $leave->application_from_date,
                    'application_to_date' => $leave->application_to_date,
                    'application_date' => $leave->application_date,
                    'number_of_day' => $leave->number_of_day,
                    'purpose' => $leave->purpose,
                    'status' => $leave->status,
                    'final_status' => $leave->final_status,
                    'evidence' => $leave->evidence,
                    'created_at' => $leave->created_at?->toIso8601String(),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Pending leave approvals retrieved successfully',
                'count' => $transformedData->count(),
                'data' => $transformedData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching pending leave approvals: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching pending approvals',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get details of a specific leave approval
     */
    public function getLeaveApprovalDetails($id)
    {
        try {
            $isSupervisor = $this->checkIfSupervisor();
            $supervisorCode = $this->getSupervisorCode();

            if (!$isSupervisor || !$supervisorCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is not a supervisor',
                ], 403);
            }

            // Get employees supervised by the current user
            $supervisedEmployees = Employee::where('supervisor_id', $supervisorCode)
                ->pluck('employee_id')
                ->toArray();

            // Get the leave application with full details
            $leaveApplication = LeaveApplication::with([
                'employee' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name', 'email', 'phone', 'designation_id', 'department_id', 'location_id', 'date_of_joining');
                },
                'employee.designation' => function ($query) {
                    $query->select('designation_id', 'designation_name');
                },
                'employee.department' => function ($query) {
                    $query->select('department_id', 'department_name');
                },
                'employee.branch' => function ($query) {
                    $query->select('location_id', 'location_name');
                },
                'leaveType' => function ($query) {
                    $query->select('leave_type_id', 'leave_type_name');
                },
            ])
                ->where('leave_application_id', $id)
                ->whereIn('employee_id', $supervisedEmployees)
                ->first();

            if (!$leaveApplication) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave application not found or you are not authorized to view it',
                ], 404);
            }

            // Get employee leave balance
            $leaveBalance = null;
            try {
                $employeeId = $leaveApplication->employee_id;
                $leaveTypeId = $leaveApplication->leave_type_id;

                // Get used days
                $usedDays = LeaveApplication::where('employee_id', $employeeId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->sum('number_of_day') ?? 0;

                // Get pending days
                $pendingDays = LeaveApplication::where('employee_id', $employeeId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('final_status', LeaveStatus::PENDING)
                    ->sum('number_of_day') ?? 0;

                // Get total entitled days from leave group settings
                $employee = Employee::find($employeeId);
                $totalEntitled = 0;
                if ($employee && $employee->leaveGroup) {
                    $setting = DB::table('leave_group_settings')
                        ->where('leave_group_id', $employee->leaveGroup->id)
                        ->where('leave_type_id', $leaveTypeId)
                        ->first();
                    $totalEntitled = $setting?->annual_entitlement ?? 0;
                }

                $availableDays = max(0, $totalEntitled - $usedDays - $pendingDays);

                $leaveBalance = [
                    'leave_type_name' => $leaveApplication->leaveType?->leave_type_name,
                    'total_entitled' => (float) $totalEntitled,
                    'used_days' => (float) $usedDays,
                    'pending_days' => (float) $pendingDays,
                    'available_days' => (float) $availableDays,
                ];
            } catch (\Exception $e) {
                Log::error('Error calculating leave balance: ' . $e->getMessage());
            }

            // Transform data
            $transformedData = [
                'id' => $leaveApplication->leave_application_id,
                'leave_application_id' => $leaveApplication->leave_application_id,
                'employee_id' => $leaveApplication->employee_id,
                'employee' => [
                    'employee_id' => $leaveApplication->employee?->employee_id,
                    'first_name' => $leaveApplication->employee?->first_name,
                    'last_name' => $leaveApplication->employee?->last_name,
                    'full_name' => trim(($leaveApplication->employee?->first_name ?? '') . ' ' . ($leaveApplication->employee?->last_name ?? '')),
                    'email' => $leaveApplication->employee?->email,
                    'phone' => $leaveApplication->employee?->phone,
                    'designation' => $leaveApplication->employee?->designation?->designation_name,
                    'department' => $leaveApplication->employee?->department?->department_name,
                    'branch' => $leaveApplication->employee?->branch?->location_name,
                    'date_of_joining' => $leaveApplication->employee?->date_of_joining,
                ],
                'leave_type' => [
                    'leave_type_id' => $leaveApplication->leaveType?->leave_type_id,
                    'leave_type_name' => $leaveApplication->leaveType?->leave_type_name,
                ],
                'application_from_date' => $leaveApplication->application_from_date,
                'application_to_date' => $leaveApplication->application_to_date,
                'application_date' => $leaveApplication->application_date,
                'number_of_day' => $leaveApplication->number_of_day,
                'purpose' => $leaveApplication->purpose,
                'status' => $leaveApplication->status,
                'final_status' => $leaveApplication->final_status,
                'evidence' => $leaveApplication->evidence,
                'remarks' => $leaveApplication->remarks,
                'leave_balance' => $leaveBalance,
                'created_at' => $leaveApplication->created_at?->toIso8601String(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Leave approval details retrieved successfully',
                'data' => $transformedData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching leave approval details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching leave approval details',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Approve a leave application
     */
    public function approveLeave(Request $request)
    {
        $request->validate([
            'leave_application_id' => 'required|exists:leave_application,leave_application_id',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $supervisorCode = $this->getSupervisorCode();
            if (!$supervisorCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found.',
                ], 401);
            }

            $leaveApplication = LeaveApplication::findOrFail($request->leave_application_id);

            // Check if the employee is under this supervisor's supervision
            $employee = Employee::where('employee_id', $leaveApplication->employee_id)
                ->where('supervisor_id', $supervisorCode)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to approve this leave application',
                ], 403);
            }

            // Check if already processed
            if ($leaveApplication->final_status != LeaveStatus::PENDING) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This leave application has already been ' . ($leaveApplication->final_status == LeaveStatus::APPROVE ? 'approved' : 'rejected'),
                ], 422);
            }

            // Update leave application
            $leaveApplication->status = LeaveStatus::APPROVE;
            $leaveApplication->final_status = LeaveStatus::APPROVE;
            $leaveApplication->approve_by = $supervisorCode;
            $leaveApplication->approve_date = now()->format('Y-m-d');
            $leaveApplication->remarks = $request->remarks;
            $leaveApplication->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application approved successfully',
                'data' => [
                    'leave_application_id' => $leaveApplication->leave_application_id,
                    'action' => 'approved',
                    'approved_by' => $supervisorCode,
                    'approved_date' => now()->format('Y-m-d'),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving leave: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while approving the leave application',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Reject a leave application
     */
    public function rejectLeave(Request $request)
    {
        $request->validate([
            'leave_application_id' => 'required|exists:leave_application,leave_application_id',
            'remarks' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $supervisorCode = $this->getSupervisorCode();
            if (!$supervisorCode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found.',
                ], 401);
            }

            $leaveApplication = LeaveApplication::findOrFail($request->leave_application_id);

            // Check if the employee is under this supervisor's supervision
            $employee = Employee::where('employee_id', $leaveApplication->employee_id)
                ->where('supervisor_id', $supervisorCode)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to reject this leave application',
                ], 403);
            }

            // Check if already processed
            if ($leaveApplication->final_status != LeaveStatus::PENDING) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This leave application has already been ' . ($leaveApplication->final_status == LeaveStatus::APPROVE ? 'approved' : 'rejected'),
                ], 422);
            }

            // Update leave application
            $leaveApplication->status = LeaveStatus::REJECT;
            $leaveApplication->final_status = LeaveStatus::REJECT;
            $leaveApplication->reject_by = $supervisorCode;
            $leaveApplication->reject_date = now()->format('Y-m-d');
            $leaveApplication->remarks = $request->remarks;
            $leaveApplication->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application rejected successfully',
                'data' => [
                    'leave_application_id' => $leaveApplication->leave_application_id,
                    'action' => 'rejected',
                    'rejected_by' => $supervisorCode,
                    'rejected_date' => now()->format('Y-m-d'),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting leave: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while rejecting the leave application',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get approved leave history (for history view)
     */
    public function getApprovedLeaveHistory(Request $request)
    {
        try {
            $isSupervisor = $this->checkIfSupervisor();
            $supervisorCode = $this->getSupervisorCode();

            if (!$isSupervisor || !$supervisorCode) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User is not a supervisor',
                    'count' => 0,
                    'data' => [],
                ], 200);
            }

            $supervisedEmployees = Employee::where('supervisor_id', $supervisorCode)
                ->pluck('employee_id')
                ->toArray();

            if (empty($supervisedEmployees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'count' => 0,
                    'data' => [],
                ], 200);
            }

            $limit = $request->get('limit', 50);

            // Get approved/rejected leave applications
            $history = LeaveApplication::with([
                'employee' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
                'leaveType' => function ($query) {
                    $query->select('leave_type_id', 'leave_type_name');
                },
            ])
                ->whereIn('employee_id', $supervisedEmployees)
                ->whereIn('final_status', [LeaveStatus::APPROVE, LeaveStatus::REJECT])
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $history->map(function ($leave) {
                return [
                    'id' => $leave->leave_application_id,
                    'leave_application_id' => $leave->leave_application_id,
                    'employee_id' => $leave->employee_id,
                    'employee' => [
                        'employee_id' => $leave->employee?->employee_id,
                        'first_name' => $leave->employee?->first_name,
                        'last_name' => $leave->employee?->last_name,
                        'full_name' => trim(($leave->employee?->first_name ?? '') . ' ' . ($leave->employee?->last_name ?? '')),
                    ],
                    'leave_type' => [
                        'leave_type_id' => $leave->leaveType?->leave_type_id,
                        'leave_type_name' => $leave->leaveType?->leave_type_name,
                    ],
                    'application_from_date' => $leave->application_from_date,
                    'application_to_date' => $leave->application_to_date,
                    'application_date' => $leave->application_date,
                    'number_of_day' => $leave->number_of_day,
                    'purpose' => $leave->purpose,
                    'final_status' => $leave->final_status,
                    'remarks' => $leave->remarks,
                    'processed_at' => $leave->approve_date ?? $leave->reject_date,
                    'action' => $leave->final_status == LeaveStatus::APPROVE ? 'approved' : 'rejected',
                    'created_at' => $leave->created_at?->toIso8601String(),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Approval history retrieved successfully',
                'count' => $transformedData->count(),
                'data' => $transformedData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching approval history: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching approval history',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalsController extends Controller
{
    /**
     * Get pending leave approvals for employees supervised by the authenticated user.
     */
    public function getAllPendingApprovals()
    {
        $user = Auth::user();

        // Log the user's details for debugging
        \Log::info('Logged-in user details', [
            'user_id' => $user->id,
            'location_id' => $user->location_id,
            'role_id' => $user->role_id,
        ]);

        // Check if the user is a supervisor (e.g., role_id = 1)
        if ($user->role_id != 1) {
            \Log::info('User is not a supervisor, returning empty result');
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        // If the user's location_id is null, return an empty result
        if (is_null($user->location_id)) {
            \Log::info('User location_id is null, returning empty result');
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        $pendingApprovals = DB::table('approval_requests as ar')
            ->leftJoin('approval_settings as aps', 'ar.approval_setting_id', '=', 'aps.id')
            ->join('user as u', 'ar.request_by', '=', 'u.id')
            ->join('approval_setting_approvers as asa', 'aps.id', '=', 'asa.approval_setting_id')
            ->whereRaw('LOWER(ar.status) = ?', ['pending'])
            ->whereRaw('LOWER(ar.action_type) = ?', ['leave'])
            ->where('u.location_id', $user->location_id)
            ->where('ar.request_by', '!=', $user->id)
            ->where('asa.user_id', $user->id)
            ->select(
                'ar.id',
                'ar.module_id as leave_application_id',
                'ar.request_by',
                'ar.request_data',
                'ar.route_name',
                'ar.request_method',
                'ar.action_type',
                'ar.status',
                'ar.created_at',
                'aps.approvers_list',
                DB::raw('CONCAT(u.first_name, " ", u.last_name) as employee_name')
            )
            ->distinct()
            ->orderBy('ar.created_at', 'desc')
            ->get();

        \Log::info('Pending approvals fetched', ['count' => $pendingApprovals->count()]);

        return response()->json([
            'status' => 'success',
            'data' => $pendingApprovals
        ]);
    }

    /**
     * Take action on an approval request (approve or reject).
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Take action on an approval request (approve or reject).
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function takeAction(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $validated = $request->validate([
            'leave_application_id' => 'required|exists:leave_application,leave_application_id',
            'employee_id' => 'required|exists:user,id',
            'action' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);

        $leaveApplicationId = $validated['leave_application_id'];
        $employeeId = $validated['employee_id'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        try {
            DB::beginTransaction();

            // Log detailed debug information
            \Log::info('Take action debug info', [
                'user_id' => $user->id,
                'leave_application_id' => $leaveApplicationId,
                'employee_id' => $employeeId,
                'action' => $action
            ]);

            // Check if the leave application exists and belongs to the specified employee
            $leaveApplication = DB::table('leave_application')
                ->where('leave_application_id', $leaveApplicationId)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$leaveApplication) {
                \Log::info('Leave application not found or does not belong to employee', [
                    'leave_application_id' => $leaveApplicationId,
                    'employee_id' => $employeeId
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave application not found or does not belong to specified employee'
                ], 404);
            }

            // Get the employee information
            $employee = DB::table('user')
                ->where('id', $employeeId)
                ->first();

            if (!$employee) {
                \Log::info('Employee not found', [
                    'employee_id' => $employeeId
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            // CRITICAL CHANGE: More flexible authorization check
            // Find the approval request with less strict conditions
            $approvalRequest = DB::table('approval_requests as ar')
                ->where('ar.module_id', $leaveApplicationId)
                ->whereRaw('LOWER(ar.action_type) = ?', ['leave'])
                ->first();

            if (!$approvalRequest) {
                \Log::info('No approval request found for this leave application', [
                    'leave_application_id' => $leaveApplicationId
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'No approval request found for this leave application'
                ], 404);
            }

            // Log approval request details for debugging
            \Log::info('Approval request found', [
                'approval_request_id' => $approvalRequest->id,
                'request_by' => $approvalRequest->request_by,
                'approval_setting_id' => $approvalRequest->approval_setting_id
            ]);

            // Check if user is an approver for this request (more flexible check)
            $isApprover = DB::table('approval_setting_approvers')
                ->where('approval_setting_id', $approvalRequest->approval_setting_id)
                ->where('user_id', $user->id)
                ->exists();

            // Also check if user is a supervisor
            $isSupervisor = false;

            // Check by role_id
            if ($user->role_id == 1) {
                $isSupervisor = true;
            }

            // Check by supervisor relationship
            $supervisedEmployees = DB::table('employee')
                ->where('supervisor_id', $user->id)
                ->where('user_id', $employeeId)
                ->exists();

            if ($supervisedEmployees) {
                $isSupervisor = true;
            }

            // Log authorization details
            \Log::info('Authorization check', [
                'user_id' => $user->id,
                'is_approver' => $isApprover,
                'is_supervisor' => $isSupervisor,
                'user_location_id' => $user->location_id,
                'employee_location_id' => $employee->location_id
            ]);

            // If user is neither an approver nor a supervisor, deny access
            if (!$isApprover && !$isSupervisor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to take action on this leave request'
                ], 403);
            }

            // Update leave application status
            DB::table('leave_application')
                ->where('leave_application_id', $leaveApplicationId)
                ->update([
                    'status' => $action,
                    'reviewed_by' => $user->id,
                    'review_notes' => $notes,
                    'reviewed_at' => now()
                ]);

            // Log the approval action
            DB::table('approval_actions')->insert([
                'approval_request_id' => $approvalRequest->id,
                'action_taken_by' => $user->id,
                'action' => $action,
                'notes' => $notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update the approval request status
            DB::table('approval_requests')
                ->where('id', $approvalRequest->id)
                ->update([
                    'status' => $action,
                    'updated_at' => now()
                ]);

            // Also update approval_request_approvals if it exists
            $approvalDetail = DB::table('approval_request_approvals')
                ->where('approval_request_id', $approvalRequest->id)
                ->where('approver_id', $user->id)
                ->first();

            if ($approvalDetail) {
                DB::table('approval_request_approvals')
                    ->where('approval_request_id', $approvalRequest->id)
                    ->where('approver_id', $user->id)
                    ->update([
                        'action' => $action,
                        'notes' => $notes,
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new approval record if one doesn't exist
                DB::table('approval_request_approvals')->insert([
                    'approval_request_id' => $approvalRequest->id,
                    'approver_id' => $user->id,
                    'action' => $action,
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application has been ' . $action,
                'data' => [
                    'leave_application_id' => $leaveApplicationId,
                    'employee_id' => $employeeId,
                    'action' => $action,
                    'action_by' => $user->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in takeAction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again or contact support.',
                'details' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Get pending approvals for the authenticated user.
     */
    public function getPendingApprovals()
    {
        $user = Auth::user();

        $pendingApprovals = DB::table('approval_requests as ar')
            ->leftJoin('approval_settings as aps', 'ar.approval_setting_id', '=', 'aps.id')
            ->leftJoin('approval_request_approvals as ara', 'ar.id', '=', 'ara.approval_request_id')
            ->where(function ($query) use ($user) {
                $query->where('ara.approver_id', $user->id)
                    ->orWhere('ar.request_by', $user->id);
            })
            ->whereRaw('LOWER(ar.status) = ?', ['pending'])
            ->select(
                'ar.*',
                'aps.approvers_list'
            )
            ->distinct()
            ->orderBy('ar.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pendingApprovals
        ]);
    }

    /**
     * Get user's approval details (my-approvals endpoint)
     */
    public function getUserApprovalDetails()
    {
        $user = Auth::user();

        $approvalRequests = DB::table('approval_requests as ar')
            ->leftJoin('approval_settings as aps', 'ar.approval_setting_id', '=', 'aps.id')
            ->where('ar.request_by', $user->id)
            ->select(
                'ar.id',
                'ar.module_id as leave_application_id',
                'ar.request_data',
                'ar.route_name',
                'ar.request_method',
                'ar.action_type',
                'ar.status',
                'ar.created_at',
                'aps.approvers_list'
            )
            ->orderBy('ar.created_at', 'desc')
            ->get();

        if ($approvalRequests->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No approval requests found.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $approvalRequests
        ]);
    }

    /**
     * Get user's approval history
     */
    public function getApprovalHistory()
    {
        $user = Auth::user();

        $approvalHistory = DB::table('approvals')
            ->where(function ($query) use ($user) {
                $query->where('stage1_approved_by', $user->id)
                    ->orWhere('stage2_approved_by', $user->id)
                    ->orWhere('stage3_approved_by', $user->id);
            })
            ->select(
                'id',
                'approval_name',
                'action_item',
                'item_id',
                'action_type',
                'final_status',
                'stage1_approval_status',
                'stage2_approval_status',
                'stage3_approval_status',
                'stage1_approval_comments',
                'stage2_approval_comments',
                'stage3_approval_comments',
                'stage1_approval_date',
                'stage2_approval_date',
                'stage3_approval_date',
                'status',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $approvalHistory
        ]);
    }

    /**
     * Get all approval requests for the authenticated user.
     */
    public function getApprovalRequests()
    {
        $user = Auth::user();

        $requests = DB::table('approval_requests as ar')
            ->leftJoin('approval_settings as aps', 'ar.approval_setting_id', '=', 'aps.id')
            ->leftJoin('approval_request_approvals as ara', 'ar.id', '=', 'ara.approval_request_id')
            ->where(function ($query) use ($user) {
                $query->where('ara.approver_id', $user->id)
                    ->orWhere('ar.request_by', $user->id);
            })
            ->select(
                'ar.id',
                'ar.module_id as leave_application_id',
                'ar.request_by',
                'ar.request_data',
                'ar.route_name',
                'ar.request_method',
                'ar.action_type',
                'ar.status',
                'ar.effected',
                'ar.created_at',
                'aps.approvers_list',
                'ara.action as approval_action',
                'ara.notes as approval_notes'
            )
            ->distinct()
            ->orderBy('ar.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }

    /**
     * Create a new approval request.
     */
    public function createApprovalRequest(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'module_id' => 'required',
            'approval_setting_id' => 'required|exists:approval_settings,id',
            'request_data' => 'required',
            'route_name' => 'required',
            'request_method' => 'required',
            'action_type' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Create approval request
            $approvalRequestId = DB::table('approval_requests')->insertGetId([
                'module_id' => $request->module_id,
                'approval_setting_id' => $request->approval_setting_id,
                'request_by' => $user->id,
                'request_data' => $request->request_data,
                'route_name' => $request->route_name,
                'request_method' => $request->request_method,
                'action_type' => $request->action_type,
                'status' => 'pending',
                'effected' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Get approvers from settings
            $approvers = DB::table('approval_setting_approvers')
                ->where('approval_setting_id', $request->approval_setting_id)
                ->where('module_id', $request->module_id)
                ->pluck('user_id');

            // Create initial approval records for each approver
            foreach ($approvers as $approverId) {
                DB::table('approval_request_approvals')->insert([
                    'approval_request_id' => $approvalRequestId,
                    'approver_id' => $approverId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Approval request created successfully',
                'data' => ['approval_request_id' => $approvalRequestId]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create approval request', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create approval request'
            ], 500);
        }
    }

    /**
     * Get all approval history (for all users).
     */
    public function getAllApprovalHistory()
    {
        $user = Auth::user();

        // Optionally, restrict this endpoint to admins by checking role_id
        if ($user->role_id != 1) {
            \Log::info('User is not authorized to view all approval history', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view all approval history'
            ], 403);
        }

        $approvalHistory = DB::table('approvals')
            ->select(
                'id',
                'approval_name',
                'action_item',
                'item_id',
                'action_type',
                'final_status',
                'stage1_approval_status',
                'stage2_approval_status',
                'stage3_approval_status',
                'stage1_approval_comments',
                'stage2_approval_comments',
                'stage3_approval_comments',
                'stage1_approval_date',
                'stage2_approval_date',
                'stage3_approval_date',
                'status',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $approvalHistory
        ]);
    }

    /**
     * Get all approval requests (for all users).
     */
    public function getAllApprovalRequests()
    {
        $user = Auth::user();

        // Optionally, restrict this endpoint to admins by checking role_id
        if ($user->role_id != 1) {
            \Log::info('User is not authorized to view all approval requests', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view all approval requests'
            ], 403);
        }

        $requests = DB::table('approval_requests as ar')
            ->leftJoin('approval_settings as aps', 'ar.approval_setting_id', '=', 'aps.id')
            ->leftJoin('approval_request_approvals as ara', 'ar.id', '=', 'ara.approval_request_id')
            ->select(
                'ar.id',
                'ar.module_id as leave_application_id',
                'ar.request_by',
                'ar.request_data',
                'ar.route_name',
                'ar.request_method',
                'ar.action_type',
                'ar.status',
                'ar.effected',
                'ar.created_at',
                'aps.approvers_list',
                'ara.action as approval_action',
                'ara.notes as approval_notes'
            )
            ->distinct()
            ->orderBy('ar.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }


    public function getRegionalLeaveApplications(Request $request)
    {
        try {
            $user = Auth::user();

            // Check if the user is an HR Administrator
            if (!$user->hasRole('HR Administrator')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only HR Administrators can access this endpoint.'
                ], 403);
            }

            // Get the logged-in employee's region
            $employee = $user->employeeDetails;
            if (!$employee || !$employee->region_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No region assigned to the HR Administrator.'
                ], 400);
            }

            // Get the current financial year
            $fiscalYear = getCurrentFinancialYear();
            $fiscalStartDate = $fiscalYear ? $fiscalYear->start_date : date('Y-m-d', strtotime('first day of January this year'));
            $fiscalEndDate = $fiscalYear ? $fiscalYear->end_date : date('Y-m-d', strtotime('last day of December this year'));

            // Query leave applications for employees in the same region
            $leaveApplications = LeaveApplication::with(['employee' => function ($query) {
                $query->select('employee_id', 'first_name', 'last_name', 'region_id');
            }, 'leaveType' => function ($query) {
                $query->select('leave_type_id', 'leave_type_name');
            }])
                ->whereHas('employee', function ($query) use ($employee) {
                    $query->where('region_id', $employee->region_id);
                })
                ->whereBetween('application_date', [$fiscalStartDate, $fiscalEndDate])
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get(['leave_application_id', 'employee_id', 'leave_type_id', 'application_date', 'application_from_date', 'application_to_date', 'number_of_day', 'status', 'final_status']);

            return response()->json([
                'status' => 'success',
                'data' => $leaveApplications,
                'message' => 'Leave applications retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching regional leave applications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching leave applications.'
            ], 500);
        }
    }

    public function getRegionalAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            // Check if the user is an HR Administrator
            if (!$user->hasRole('HR Administrator')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only HR Administrators can access this endpoint.'
                ], 403);
            }

            // Get the logged-in employee's region
            $employee = $user->employeeDetails;
            if (!$employee || !$employee->region_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No region assigned to the HR Administrator.'
                ], 400);
            }

            // Optional: Filter by date range if provided
            $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
            $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());

            // Query attendance records for employees in the same region
            $attendanceRecords = Attendance::with(['employee' => function ($query) {
                $query->select('employee_id', 'first_name', 'last_name', 'region_id');
            }])
                ->whereHas('employee', function ($query) use ($employee) {
                    $query->where('region_id', $employee->region_id);
                })
                ->whereBetween('date', [$fromDate, $toDate])
                ->orderBy('date', 'desc')
                ->get(['id', 'employee_id', 'date', 'status', 'check_in_time', 'check_out_time']);

            return response()->json([
                'status' => 'success',
                'data' => $attendanceRecords,
                'message' => 'Attendance records retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching regional attendance records: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching attendance records.'
            ], 500);
        }
    }
}

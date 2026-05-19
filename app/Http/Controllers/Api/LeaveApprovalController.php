<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;

class LeaveApprovalController extends Controller
{
    /**
     * Get pending leave applications for approval
     */
    public function getPendingApprovals()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $applications = LeaveApplication::with(['employee', 'leaveType'])
                ->where(function ($query) {
                    $query->where('status', 1) // Pending
                        ->orWhere('hr_approval', 1)
                        ->orWhere('ceo_approval_type', 1)
                        ->orWhere('final_status', 1);
                })
                ->latest('leave_application_id')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Pending leave applications retrieved successfully.',
                'data' => $applications
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pending applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function processApproval(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'approval_id' => 'required|exists:leave_applications,leave_application_id',
                'action' => 'required|in:approved,rejected',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leaveApplication = LeaveApplication::find($request->approval_id);

            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }
            $role = $user->role ?? ($user->getAttribute('role') ?? null);

            if ($role === 'HR') {
                $leaveApplication->update([
                    'hr_approval' => $request->action === 'approved' ? 2 : 3, // 2=Approve, 3=Reject
                    'hr_approval_date' => Carbon::now(),
                    'hr_approval_comments' => $request->notes
                ]);
            } elseif ($role === 'CEO') {
                $leaveApplication->update([
                    'ceo_approval_type' => $request->action === 'approved' ? 2 : 3, // 2=Approve, 3=Reject
                    'ceo_approval_date' => Carbon::now(),
                    'ceo_approval_comments' => $request->notes
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized role'
                ], 403);
            }

            // Final status update
            if ($leaveApplication->hr_approval == 2 && $leaveApplication->ceo_approval_type == 2) {
                $leaveApplication->update([
                    'status' => 2, // Approved
                    'approve_date' => Carbon::now(),
                    'approve_by' => $user->id
                ]);
            } elseif ($request->action === 'rejected') {
                $leaveApplication->update([
                    'status' => 3, // Rejected
                    'reject_date' => Carbon::now(),
                    'reject_by' => $user->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Approval action processed successfully',
                'data' => $leaveApplication
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing approval action',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval history for a specific role
     */
    public function getApprovalHistory(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $query = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy']);

            if ($request->has('approval_type')) {
                switch ($request->approval_type) {
                    case 'hr':
                        $query->whereNotNull('hr_approval_date');
                        break;
                    case 'ceo':
                        $query->whereNotNull('ceo_approval_date');
                        break;
                    default:
                        $query->where(function ($q) {
                            $q->whereNotNull('hr_approval_date')
                                ->orWhereNotNull('ceo_approval_date');
                        });
                }
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $applications = $query->latest('leave_application_id')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Approval history retrieved successfully',
                'data' => $applications
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving approval history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval statistics
     */
    public function getApprovalStats()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $stats = [
                'pending' => LeaveApplication::where(function ($query) {
                    $query->where('status', 1) // Pending
                        ->orWhere('hr_approval', 1)
                        ->orWhere('ceo_approval_type', 1)
                        ->orWhere('final_status', 1);
                })->count(),
                'approved' => LeaveApplication::where('status', 2)->count(), // Approved
                'rejected' => LeaveApplication::where('status', 3)->count(), // Rejected
                'pending_hr' => LeaveApplication::where('hr_approval', 1)->count(),
                'pending_ceo' => LeaveApplication::where('ceo_approval_type', 1)->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Approval statistics retrieved successfully',
                'data' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving approval statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

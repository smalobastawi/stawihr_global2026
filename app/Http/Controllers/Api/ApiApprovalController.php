<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\ApprovalRecord;
use App\Models\Employee;
use App\Models\User;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiApprovalController extends Controller
{
    
     // Submit a new approval request.
     
    public function submitApprovalRequest(Request $request)
    {
        
        $request->validate([
            'module_id' => 'required|exists:modules,id', 
            'approval_setting_id' => 'required|exists:approval_settings,id', 
            'request_data' => 'nullable|string|max:255', 
            'route_name' => 'required|string|max:191', 
            'request_method' => 'required|string|max:191', 
            'action_type' => 'required|string|max:191', 
            'approver_ids' => 'required|array', 
            'approver_ids.*' => 'exists:user,id',
        ]);
    
        $currentUser = Auth::user(); 
    
        // Create a new approval request
        $approvalRequest = new ApprovalRequest();
        $approvalRequest->request_by = $currentUser->id; 
        $approvalRequest->module_id = $request->module_id; 
        $approvalRequest->approval_setting_id = $request->approval_setting_id;
        $approvalRequest->request_data = $request->request_data; 
        $approvalRequest->route_name = $request->route_name; 
        $approvalRequest->request_method = $request->request_method; 
        $approvalRequest->action_type = $request->action_type; 
        $approvalRequest->status = 'pending';
        $approvalRequest->effected = 0; 
        $approvalRequest->save(); 
    
        // Attach approvers (assuming a Many-to-Many relationship)
        $approvalRequest->approvers()->attach($request->approver_ids);
    
        // Return a response to indicate success
        return response()->json([
            'success' => true,
            'message' => 'Approval request submitted successfully.',
            'data' => [
                'approval_request_id' => $approvalRequest->id, 
                'request_by' => $approvalRequest->request_by, 
                'module_id' => $approvalRequest->module_id, 
                'approval_setting_id' => $approvalRequest->approval_setting_id,
                'request_data' => $approvalRequest->request_data, 
                'route_name' => $approvalRequest->route_name,
                'request_method' => $approvalRequest->request_method,
                'action_type' => $approvalRequest->action_type,
                'status' => $approvalRequest->status, 
                'effected' => $approvalRequest->effected, 
            ],
        ]);
    }

   
     // Get all approval requests for the logged-in user.
    
    public function getApprovalRequests(Request $request)
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('SuperAdmin')) {
            $approvalRequests = ApprovalRequest::with('requester', 'module', 'approvals')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $approvalRequests = ApprovalRequest::with('requester', 'module', 'approvals')
                ->where('status', 'pending')
                ->whereHas('module.approvers', function ($query) use ($currentUser) {
                    $query->where('user_id', $currentUser->id);
                })
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Approval requests retrieved successfully.',
            'data' => $approvalRequests,
        ]);
    }

  
     // View details of a specific approval request.
    
    public function getApprovalRequestDetails($id)
    {
        $approvalRequest = ApprovalRequest::with('requester', 'module', 'approvals')->find($id);

        if (!$approvalRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Approval request not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Approval request details retrieved successfully.',
            'data' => $approvalRequest,
        ]);
    }

    
    // * Approve or decline an approval request.
    
    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:1,2',
            'reason' => 'nullable|string|max:255',
        ]);

        $approvalRequest = ApprovalRequest::find($id);

        if (!$approvalRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Approval request not found.',
            ], 404);
        }

        $status = $request->status; 
        $reason = $request->reason;

        // Create a new approval record
        $approval = new ApprovalRecord(); 
        $approval->action = $status == 1 ? 'approve' : 'decline';
        $approval->notes = $reason;
        $approval->approval_request_id = $approvalRequest->id;
        $approval->approver_id = Auth::id();
        $approval->save();

        // Update approval request status
        if ($status == 2) {
            $approvalRequest->status = 'declined';
        } else {
            $approversCount = $approvalRequest->module->approvers()->count();
            $alreadyApprovedCount = $approvalRequest->approvals()->count();

            if ($alreadyApprovedCount >= $approversCount) {
                $approvalRequest->status = 'approved';
            }
        }
        $approvalRequest->save();

        return response()->json([
            'success' => true,
            'message' => $status == 1 ? 'Approval request approved.' : 'Approval request declined.',
        ]);
    }

  
     //* Get all approval records for the logged-in user.
     
    public function getApprovalRecords()
    {
        $superAdmin = Auth::user()->hasRole('SuperAdmin');
        $employeeId = $superAdmin ? null : Employee::where('user_id', Auth::id())->value('employee_id');

        $records = $this->fetchApprovalRecords($superAdmin, $employeeId);

        return response()->json([
            'success' => true,
            'message' => $records->isEmpty() ? 'No approval records found.' : 'Approval records retrieved successfully.',
            'data' => $records,
        ]);
    }

    
    // Fetch approval records based on user role.
     
    private function fetchApprovalRecords($isSuperAdmin, $employeeId)
    {
        if ($isSuperAdmin) {
            return ApprovalRecord::with('requester')->get();
        }

        if (!$employeeId) {
            return collect(); 
        }

        return ApprovalRecord::whereJsonContains('approver_id', $employeeId)
            ->with('requester')
            ->get();
    }
}

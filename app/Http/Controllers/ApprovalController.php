<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequestUpdateRequest;
use App\Models\Approval;
use App\Models\ApprovalRequestApproval;
use App\Models\PayGrade;

use App\Http\Requests\StoreApprovalRequest;
use App\Http\Requests\UpdateApprovalRequest;
use App\Models\AttendaceOvertimes;
use App\Models\ApprovalRecord;
use App\Models\ApprovalRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAttendanceApprove;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;


class ApprovalController extends Controller
{

    public function index(Request $request){
            $currentUser=Auth::user(); 
             
             if(in_array('SuperAdmin',$currentUser->roles->pluck('name')->toArray())){
                //dd($currentUser->roles->pluck('name')->toArray());
                $approvalRequests=ApprovalRequest::with('requester', 'module', 'approvals')
                ->orderBy('id','desc')->take(100)->get();
             }else{
             $currentUser=Auth::user();
              $approvalRequests = ApprovalRequest::with('requester', 'module', 'approvals')
             ->where('status', 'pending')
             ->whereHas('module.approvers', function ($query) use ($currentUser) {
                 $query->where('user_id', $currentUser->id);
             })->orderBy('id','desc')->take(100)
             ->get();
        }

        return view('admin.approvals.indexv')->with([
            'approvalRequests'=>$approvalRequests
        ]); 

    }

    public function show(ApprovalRequest $approval_request ){

        return view('admin.approvals.showv')->with(['approval_request'=> $approval_request]);

    }

    public function approve(ApprovalRequestUpdateRequest $request,ApprovalRequest $approval_request ){
       
        $reason=$request->reason;
         
        $status=$request->status;
        $requestApproval=new ApprovalRequestApproval();
        $requestApproval->action = ($status == 1) ? 'approve' : (($status == 2) ? 'decline' : 'unknown');
        $requestApproval->notes=$reason;
        $requestApproval->approval_request_id=$approval_request->id;
        $requestApproval->approver_id=Auth::user()->id;
        $requestApproval->save();

        

        if($status==2){
            $approval_request->status='declined';
            $approval_request->save();
        }
        else{
            $approversCount=$approval_request->module->approvers()->count();
            $alreadyApprovedCount=$approval_request->approvals()->count();
            if($alreadyApprovedCount >= $approversCount){
                $approval_request->status= 'approved';
                $approval_request->save();
            }
        }
        return response()->json(['success' => true]);

    }
    public function index1()
    {
        $super_admin = Auth::user()->hasRole('SuperAdmin');
        $user_id = Auth::id();
        $employee_id = $super_admin ? null : Employee::where('user_id', $user_id)->value('employee_id');
        
        // Fetch approval records based on user role
        $records_data = $this->fetchApprovalRecords($super_admin, $employee_id);
    
        // Collect all approver IDs and fetch employees
        $all_approver_ids = $this->getApproverIds($records_data);
        $employees = Employee::whereIn('employee_id', $all_approver_ids)->get()->keyBy('employee_id');
    
        // Process each approval record
        foreach ($records_data as $data) {
            $this->processRecord($data, $employees);
        }
    
        return view('admin.approvals.index', ['data' => $records_data]);
    }


    /**
 * Fetch approval records based on user role.
 */
private function fetchApprovalRecords($isSuperAdmin, $employee_id)
{
    if ($isSuperAdmin) {
        return ApprovalRecord::with('requester')->get();
    }

    if (!$employee_id) {
        return collect(); 
    }

    return ApprovalRecord::whereJsonContains('approver_id', $employee_id)
        ->with('requester')
        ->get();
}

/**
 * Extract unique approver IDs from records.
 */
private function getApproverIds($records_data)
{
    return $records_data->pluck('approver_id')
        ->map(fn($item) => json_decode($item, true))
        ->flatten()
        ->unique()
        ->filter()
        ->toArray();
}

/**
 * Process an individual approval record.
 */
private function processRecord($data, $employees)
{
    $approver_ids = json_decode($data['approver_id'], true);
    $response_approvers = json_decode($data['response_approver_id'], true) ?? [];
    $approved_ids = collect($response_approvers)
        ->where('status', '1') 
        ->pluck('id')
        ->filter()
        ->toArray();

    $data->affected_staff = $this->determineAffectedStaff($data);
    $data->next_approver = $this->determineNextApprover($approver_ids, $approved_ids, $employees);
    $data->final_approver = $this->determineFinalApprover($approver_ids, $employees);
}

/**
 * Determine affected staff based on model type.
 */
private function determineAffectedStaff($data)
{
    $employee = Employee::where('user_id', $data['requested_by'])->first();

    if (!$employee) {
        return '';
    }

    switch ($data->model_type) {
        case 'Employee Management':
        case 'Leave Management':
            return "{$employee->first_name} {$employee->middle_name} {$employee->last_name}";
        default:
            return '';
    }
}

/**
 * Determine the next approver.
 */
private function determineNextApprover($approver_ids, $approved_ids, $employees)
{
    foreach ($approver_ids as $approver_id) {
        if (!in_array($approver_id, $approved_ids) && isset($employees[$approver_id])) {
            $employee = $employees[$approver_id];
            return "{$employee->first_name} {$employee->last_name}";
        }
    }

    return null;
}

/**
 * Determine the final approver.
 */
private function determineFinalApprover($approver_ids, $employees)
{
    $final_approver = end($approver_ids);

    if ($final_approver && isset($employees[$final_approver])) {
        $employee = $employees[$final_approver];
        return "{$employee->first_name} {$employee->last_name}";
    }

    return null;
}

  
    public function view($id){
        $approval_record = ApprovalRecord::findOrFail($id);

        $model_type = $approval_record->model_type;
        switch($model_type){
            case 'Payroll':
            $newData = json_decode($approval_record->new, true);
            break;
            case 'Leave':
            $newData = $approval_record->leave->approval_records;
            default:
            $newData = json_decode($approval_record->new,true);
        }
        $all_approvers = json_decode($approval_record->approver_id, true);
        $all_approvers = array_unique($all_approvers);
        $approval_record['approvers'] = \App\Models\Employee::whereIn('employee_id', $all_approvers)->get(['employee_id', 'first_name', 'last_name']);
        $already_approved_approvers = json_decode($approval_record->response_approver_id, true);

        $already_approved_records = json_decode($approval_record->response_approver_id, true);
        $formatted_approvals = [];

        if (!empty($already_approved_records)) {
            // Extract approver IDs
            $approver_ids = array_column($already_approved_records, 'id');
        
            $approvers = \App\Models\Employee::whereIn('employee_id', $approver_ids)
                ->get(['employee_id', 'first_name', 'last_name'])
                ->keyBy('employee_id');
        
            foreach ($already_approved_records as $record) {
                $approver = $approvers->get($record['id']);
                $formatted_approvals[] = [
                    'name' => $approver ? $approver->first_name . ' ' . $approver->last_name : User::where('id', Auth::user()->id)->first()->user_name,
                    'status' => $record['status'], 
                ];
            }
        }
        $approval_record['already_approved_records'] = $formatted_approvals;

        //check if logged in user is allowed to approve or reject
        $can_approve = false;
        // Get the logged-in employee's ID
        $logged_in_employee = User::where('id', Auth::user()->id)->first();
        $logged_in_employee_id = $logged_in_employee && $logged_in_employee->employeeDetails 
            ? $logged_in_employee->employeeDetails->id 
            : null;
        
        if(Auth::user()->hasRole('superadmin') || in_array($logged_in_employee_id, $all_approvers)){

            $can_approve = true;
            return view('admin.approvals.show', ['data'=>$approval_record,'newData'=>$newData,'can_approve'=>$can_approve]);

        }else{
            $can_approve = true;

            // return redirect()->back()->with('error', 'You are not authorized to view this record.');
            return view('admin.approvals.show', ['data'=>$approval_record,'newData'=>$newData,'can_approve'=>$can_approve]);

        }
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        $approvalRecord = ApprovalRecord::findOrFail($data['approval_id']);

        $request->validate([
            'reason' => 'required|string|max:255',
            'status' => 'required',
        ]);
        //if validation fails return validation error

    
        // Get the logged-in user's employee ID    
          // Get the logged-in employee's ID
          $logged_in_employee = User::where('id', Auth::user()->id)->first();
          $loggedInEmployeeId = $logged_in_employee && $logged_in_employee->employeeDetails 
              ? $logged_in_employee->employeeDetails->id 
              : null;

        // Decode the current response approvers
        $responseApprovers = json_decode($approvalRecord->response_approver_id, true) ?? [];
    
        // // Check if the user has already acted on this record
        foreach ($responseApprovers as $response) {

            if(isset($loggedInEmployeeId)){
                if ($response['id'] == $loggedInEmployeeId) {
                    return response()->json(['success' => false, 'message' => 'You have already acted on this approval.']);
                }
            }
           
        }
       
        // Append the logged-in user's action
        $responseApprovers[] = [
            'id' => $loggedInEmployeeId,
            'status' => $request->status,
            'reason' => $request->reason,
            'timestamp' => now(),
        ];

        //check if no. of approved responses equals to number of stages, if so then append the record to the respective table for it to be rendered
        // Update the approval record
        $approvalRecord->response_approver_id = json_encode($responseApprovers);
        $approvalRecord->save();
    

        // count how many responses have a status of "approved" (status = 1)
        $approvedResponses = collect($responseApprovers)->filter(function ($response) {
        return $response['status'] == 1;
         })->count();
        $totalStages = $approvalRecord->stages;

        // Check if the number of approved responses equals the number of stages
        if ($approvedResponses == $totalStages) {
            
            if($approvalRecord->action_type == 'create'){
                $model_type = $approvalRecord->model_type;
                switch($model_type){
                    case 'Payroll':
                        $newData = json_decode($approvalRecord->new, true);
                        $payroll = new Paygrade();
                        $payroll->fill($newData);
                        $payroll->save();
                        break;
                    
                    default:
                        $newData = json_decode($approvalRecord->new, true);
                        $model = new $model_type();
                        $model->fill($newData);
                        $model->save();
                }
            }
            
          
        }
  
        return response()->json(['success' => true]);
    }
    
}

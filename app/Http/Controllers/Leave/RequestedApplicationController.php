<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\LeaveStatus;
use App\Repositories\LeaveRepository;
use App\Mail\Leave\StaffLeaveApprovalMail;
use App\Mail\Leave\StaffLeaveRejectionMail;
use App\Mail\Leave\HrLeaveApprovalBySupervisor;
use App\Mail\Leave\SupervisorLeaveApprovalByHR;
use App\Mail\Leave\SupervisorLeaveRejectedByHRMail;
use App\Models\Location;

class RequestedApplicationController extends Controller
{

    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        $activeFinancialYear = getCurrentFinancialYear();
        if ($activeFinancialYear == null) {
            $fiscal_start_date = date('Y-m-d', strtotime('first day of January this year'));
            $fiscal_end_date   = date('Y-m-d', strtotime('last day of December this year'));
        } else {
            $fiscal_start_date = $activeFinancialYear->start_date;
            $fiscal_end_date   = $activeFinancialYear->end_date;
        }

        $hasSupervisorWiseEmployee = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $supervisor_approval       = LeaveApplication::select('status')
            ->pluck('status');
        if (count($hasSupervisorWiseEmployee) == 0) {
            $results = [];
        } else {
            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->whereBetween('application_date', [date($fiscal_start_date), date($fiscal_end_date)])
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();
            $supervisor_approval = LeaveApplication::select('status')
                ->pluck('status');
        }

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.leave.leaveApplication.leaveApplicationList', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role], compact('supervisor_approval'));
    }

    public function viewDetails($id)
    {
        $user                 = Auth::user();
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->first(); //Removed : ->where('status',1)
        $signed_in_user_role = $user->roles()->first();

        if (! $leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }
        $supervisor_id = $leaveApplicationData->employee->supervisor_id;

        $supervisor_details = $leaveApplicationData->employee->supervisor();
        $currentBalance     = $this->leaveRepository->calCulateEmployeeLeaveBalance($leaveApplicationData->leave_type_id, $leaveApplicationData->employee_id);
        // dd($leaveApplicationData);
        return view('admin.leave.leaveApplication.leaveDetails', ['leaveApplicationData' => $leaveApplicationData, 'currentBalance' => $currentBalance, 'signed_in_user_role' => $signed_in_user_role, 'supervisor_id' => $supervisor_id, 'supervisor_details' => $supervisor_details]);
    }

    public function update(Request $request, $id)
    {

        $loggedinUser        = Auth::user();
        $data                = LeaveApplication::findOrFail($id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $input               = $request->all();
        if ($request->status == LeaveStatus::APPROVE) {

            $input['hr_approval'] = LeaveStatus::APPROVE;
            //$input['ceo_approval_type'] = $request->status;
            $input['final_status']         = $request->status;
            $input['hr_approval_date']     = date('Y-m-d');
            $input['hr_approval_comments'] = $request->remarks;
            $input['approve_by']           = session('logged_session_data.employee_id');

            $input['approve_date'] = date('Y-m-d');
            $input['final_status'] = $request->status;
            $input['approve_by']   = session('logged_session_data.employee_id');
        } else {

            $input['hr_approval']      = $request->status;
            $input['hr_approval_date'] = date('Y-m-d');
            $input['final_status']     = $request->status;
            $input['approve_by']       = session('logged_session_data.employee_id');

            $input['reject_date']  = date('Y-m-d');
            $input['reject_by']    = session('logged_session_data.employee_id');
            $input['status']       = $request->status;
            $input['final_status'] = $request->status;
        }

        //Leave email details extraction here
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->first();
        $leaveType                = LeaveType::where('leave_type_id', $leaveApplicationData->leave_type_id)->pluck('leave_type_name')->first();
        $getEmployeeEmail         = $leaveApplicationData->employee->email;
        $supervisor_id            = $leaveApplicationData->employee->supervisor_id;
        $signed_in_user_privilege = Employee::select('supervisor_id')->where('employee_id', $leaveApplicationData->employee->employee_id)->pluck('supervisor_id')->first();

        $supervisor_email = Employee::select('email')
            ->where('employee_id', $supervisor_id)
            ->pluck('email')
            ->first();

        $hr_id = User::select('id')->where('role_id', '2')->pluck('id')->first();
        if ($hr_id == $leaveApplicationData->employee->employee_id) {
            return response()->view('errors.permissions_denied', [], 403);
        }

        $employee   = Employee::where('employee_id', $data->employee_id)->first();
        $hrApprover = $employee->hr();
        if ($hrApprover) {
            $hr_email = $hrApprover->email;
        }

        $mailContent = ([
            'leave_from_date'  => $leaveApplicationData->application_from_date,
            'staff_first_name' => $leaveApplicationData->employee->first_name,
            'staff_last_name'  => $leaveApplicationData->employee->last_name,
            'leave_to_date'    => $leaveApplicationData->application_to_date,
            'no_of_days'       => $leaveApplicationData->number_of_day,
            'leaveType'        => $leaveType,
            'remarks'          => $request->remarks,
        ]);

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }
        //send mail to employee

        if ($request->status == LeaveStatus::APPROVE) {

            try {
                \Mail::to($getEmployeeEmail)->send(new StaffLeaveApprovalMail($mailContent));
            } catch (\Exception $e) {
                \Log::info($e->getMessage() . ' Employee Leave application email failed');
            }
            //                //send mail to supervisor
            // try {
            //     \Mail::to($supervisor_email)->send(new SupervisorLeaveApprovalByHR($mailContent));
            // } catch (\Exception $e) {
            //     \Log::info($e->getMessage() . ' Supervisor Leave application email failed');
            // }

            //Send email to HR
            // try {
            //     \Mail::to($supervisor_email)->send(new HrLeaveApprovalBySupervisor($mailContent));
            // } catch (\Exception $e) {
            //     \Log::info($e->getMessage() . ' Supervisor Leave application email failed');
            // }

        }

        // else the request is definitely a rejection. send mail o people.
        else {
            try {
                \Mail::to($getEmployeeEmail)->send(new StaffLeaveRejectionMail($mailContent));
            } catch (\Exception $e) {
                \Log::info($e->getMessage() . ' Employee Leave rejection email failed');
            }
            //send mail to supervisor
            // try {
            //     \Mail::to($supervisor_email)->send(new SupervisorLeaveRejectedByHRMail($mailContent));
            // } catch (\Exception $e) {
            //     \Log::info($e->getMessage() . ' Supervisor Leave rejection email failed');

            // }
        }
        if ($bug == 0) {
            if ($request->status == LeaveStatus::APPROVE) {

                return redirect()->route('pendingLeaveRequests.pendingLeaveRequests')->with('success', 'Leave application approved successfully. ');
            } else {

                return redirect()->route('pendingLeaveRequests.pendingLeaveRequests')->with('success', 'Leave application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function approveOrRejectLeaveApplication(Request $request)
    {

        $data  = LeaveApplication::findOrFail($request->leave_application_id);
        $input = $request->all();

        if ($request->status == LeaveStatus::APPROVE) {
            $input['approve_date'] = date('Y-m-d');
            $input['final_status'] = LeaveStatus::APPROVE;
            $input['approve_by']   = session('logged_session_data.employee_id');
        } else {
            $input['reject_date']  = date('Y-m-d');
            $input['final_status'] = LeaveStatus::REJECT;
            $input['reject_by']    = session('logged_session_data.employee_id');
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }
        if ($bug == 0) {
            if ($request->status == LeaveStatus::APPROVE) {
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }

    public function hrPending()
    {

        $hasHRWiseEmployee = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        //fiscal year calculation here
        $fiscal_year       = getCurrentFinancialYear();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date = $fiscal_year->end_date;
        $loggedInEmployee = employeeInfo();
        if ($loggedInEmployee) {
            $supervisorId = $loggedInEmployee->employee_id;
            $results = LeaveApplication::whereHas('employee', function ($query) use ($supervisorId) {
                $query->where('supervisor_id', $supervisorId);
            })
                ->with(['employee', 'leaveType']) // Optional: eager load relationships
                ->whereBetween('application_date', [date($fiscal_start_date), date($fiscal_end_date)])
                ->where('final_status', 1)
                ->get();
        } else {
            $results = [];
        }

        $supervisor_approval = LeaveApplication::select('status')
            ->pluck('status');
        return view('admin.leave.leaveApplication.hrPendingLeaveApplications', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role
        ], compact('supervisor_approval'));
    }

    public function ceoPending()
    {
        $hasHRWiseEmployee   = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        //fiscal year calculation here
        $fiscal_year       = getCurrentFinancialYear();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date   = $fiscal_year->end_date;
        $results           = LeaveApplication::with(['employee', 'leaveType'])
            ->where('final_status', 1)
            ->whereBetween('application_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->orderBy('final_status', 'asc')
            ->orderBy('leave_application_id', 'desc')
            ->get();
        $supervisor_approval = LeaveApplication::select('status')
            ->pluck('status');
        return view('admin.leave.leaveApplication.ceoPendingLeaveApplications', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role], compact('supervisor_approval'));
    }

   public function allLeaveApplications(Request $request)
{
    $currentUser         = Auth::user();
    $currentEmployee = $currentUser->employeeDetails;
    $hasHRWiseEmployee   = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
    $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

    // Fiscal year calculation
    $departments       = Department::all();
    $locations          = Location::all();
    $fiscal_year       = getCurrentFinancialYear();
    $fiscal_start_date = $fiscal_year->start_date;
    $fiscal_end_date   = $fiscal_year->end_date;
    $leaveTypes = LeaveType::all();
    $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
    $logged_in_employee = employeeInfo();

    // Default to current month + previous 3 months
    $default_start_date = date('d/m/Y', strtotime('-1 months'));
    $default_end_date = date('d/m/Y');

   if ($currentUser->hasRole('HR Administrator') || $currentUser->hasRole('SuperAdmin')) {
        $query = LeaveApplication::where('employee_id', 2616)->with(['employee', 'leaveType'])
            ->orderBy('status', 'asc')
            ->orderBy('leave_application_id', 'desc');
    } else {
        if ($logged_in_employee) {
            $employeesTocheck = Employee::where('supervisor_id', $currentEmployee->employee_id)
                ->orWhere('employee_id', $currentEmployee->employee_id)
                ->pluck('employee_id')
                ->toArray();
            $query = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $employeesTocheck)
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc');
        } else {
            $query = LeaveApplication::with(['employee', 'leaveType'])
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc');
        }
    }

    // Apply filters if any filter parameter is present
    $from_date       = $request->input('from_date');
    $to_date         = $request->input('to_date');
    $location_id       = $request->input('location_id');
    $department_id   = $request->input('department_id');
    $final_status    = $request->input('final_status');
    $financial_year_id = $request->input('financial_year_id');
    $leave_type_id   = $request->input('leave_type_id');

    // Financial year filter - if selected, override date range
    if ($financial_year_id) {
        $selectedFiscalYear = FinancialYear::find($financial_year_id);
        if ($selectedFiscalYear) {
            $from_date = date('d/m/Y', strtotime($selectedFiscalYear->start_date));
            $to_date = date('d/m/Y', strtotime($selectedFiscalYear->end_date));
        }
    }

    // Date filter - use selected dates or default to current month + previous 3 months
    // Check against application_from_date, application_to_date, and application_date
    if ($from_date && $to_date) {
        $fromDateFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
        $toDateFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));

        $query->where(function ($q) use ($fromDateFormatted, $toDateFormatted) {
            $q->whereBetween('application_from_date', [$fromDateFormatted, $toDateFormatted])
                ->orWhereBetween('application_to_date', [$fromDateFormatted, $toDateFormatted])
                ->orWhereBetween('application_date', [$fromDateFormatted, $toDateFormatted]);
        });
    } else {
        // Default: show current month + previous 3 months when page loads
        $defaultFrom = date('Y-m-01', strtotime('-3 months'));
        $defaultTo = date('Y-m-t');

        $query->where(function ($q) use ($defaultFrom, $defaultTo) {
            $q->whereBetween('application_from_date', [$defaultFrom, $defaultTo])
                ->orWhereBetween('application_to_date', [$defaultFrom, $defaultTo])
                ->orWhereBetween('application_date', [$defaultFrom, $defaultTo]);
        });
    }

    // Location filter
    if ($location_id) {
        $query->whereHas('employee', function ($q) use ($location_id) {
            $q->where('location_id', $location_id);
        });
    }

    // Department filter
    if ($department_id) {
        $query->whereHas('employee', function ($q) use ($department_id) {
            $q->where('department_id', $department_id);
        });
    }

    // Final status filter
    if ($final_status) {
        $query->where('final_status', $final_status);
    }

    // Leave type filter
    if ($leave_type_id) {
        $query->where('leave_type_id', $leave_type_id);
    }

    $supervisor_approval = LeaveApplication::pluck('status');
    $results = $query->where('leave_application_id', 3336)->get();

    // Calculate holiday adjustment data for each leave application
    foreach ($results as $result) {
        $holidayData = getHolidayAdjustment(
            $result->employee, 
            $result->application_from_date, 
            $result->application_to_date, 
            $result->leave_type_id
        );
       
        $result->holiday_adjustment = $holidayData['holiday_count'];
        
        $result->final_days = $holidayData['applicable_on'] === 'calendar_days'
            ? max(0, $result->number_of_day - $holidayData['holiday_count'])
            : $result->number_of_day;
    }

    
    // Non-AJAX response
    return view('admin.leave.leaveApplication.allLeaveApplications', [
        'results'             => $results,
        'locations'            => $locations,
        'leaveTypes'          => $leaveTypes,
        'financialYears'      => $financialYears,
        'departments'         => $departments,
        'signed_in_user_role' => $signed_in_user_role,
        'start_date'          => $default_start_date,
        'end_date'            => $default_end_date,
    ], compact('supervisor_approval'));
}
    /**
     * HR Recall functionality - Allow HR to recall an approved leave
     */
    public function recall($id)
    {
        try {
            $leave = LeaveApplication::findOrFail($id);

            // Check if leave can be recalled (must be approved)
            if ($leave->final_status != LeaveStatus::APPROVE) {
                return redirect()->route('allLeaveApplications.allLeaveApplications')
                    ->with('error', 'Only approved leaves can be recalled.');
            }

            // Check if leave is past
            $currentDate = now()->startOfDay();
            $applicationToDate = Carbon::parse($leave->application_to_date)->startOfDay();
            $isPastLeave = $applicationToDate->lt($currentDate);

            if ($isPastLeave) {
                return redirect()->route('allLeaveApplications.allLeaveApplications')
                    ->with('error', 'Past approved leaves cannot be recalled.');
            }

            // Update leave status to RECALL
            $leave->final_status = LeaveStatus::RECALL;
            $leave->status = LeaveStatus::RECALL;
            $leave->save();

            return redirect()->route('allLeaveApplications.allLeaveApplications')
                ->with('success', 'Leave has been recalled successfully.');
        } catch (\Exception $e) {
            Log::error('Error recalling leave: ' . $e->getMessage());
            return redirect()->route('allLeaveApplications.allLeaveApplications')
                ->with('error', 'Error recalling leave: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $error1 = 0;
        $bug    = 0;
        try {
            $data = LeaveApplication::FindOrFail($id);
            $date = Carbon::createFromFormat('Y-m-d', $data['application_from_date'])->isPast();

            if ($date == 1) {
                echo 'hasForeignKey';
                $error1 = 'Cant delete a leave whose date already passed';
            } else {
                $data->delete();
                $bug = 0;
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            $bug = $e->$e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
            return redirect()->back()->with('error', 'Something Error Found !' . $error1 . ', Please try again.');
        }
    }
}

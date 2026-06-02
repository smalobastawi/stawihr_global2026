<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use Exception;

use App\Models\User;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\LeaveApplicationEvent;
use App\Lib\Enumerations\LeaveStatus;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use App\Mail\Leave\LeaveApplicationMail;
use App\Http\Requests\ApplyForLeaveRequest;
use App\Mail\Leave\HR_LeaveApplicationMail;
use App\Mail\Leave\StaffLeaveApplicationMail;
use App\Notifications\LeaveApplicationSubmitted;
use App\Mail\Leave\SupervisorLeaveApplicationMail;


class ApplyForLeaveController extends Controller
{

    protected $commonRepository;
    protected $leaveRepository;
    public function __construct(
        CommonRepository $commonRepository,
        LeaveRepository $leaveRepository
    ) {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository = $leaveRepository;
    }
    public function index()
    {
        $fiscal_year = getCurrentFinancialYear();
        if ($fiscal_year == null) {
            $fiscal_start_date = today();
            $fiscal_end_date = today();
        } else {
            $fiscal_start_date = $fiscal_year->start_date;
            $fiscal_end_date = $fiscal_year->end_date;
        }

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy'])
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->whereBetween('application_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->orderBy('leave_application_id', 'desc')
            ->paginate(10);
        return view('admin.leave.applyForLeave.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role, 'fiscal_year' => $fiscal_year]);
    }


    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $leaveTypeList = $this->commonRepository->leaveTypeList();
        $getEmployeeInfo = $this->commonRepository->getEmployeeDetails(Auth::user()->id);

        $employeeList = $this->commonRepository->employeeListForLeaves();

        return view('admin.leave.applyForLeave.leave_application_form', [
            'leaveTypeList' => $leaveTypeList,
            'getEmployeeInfo' => $getEmployeeInfo,
            'signed_in_user_role' => $signed_in_user_role,
            'employeeList' => $employeeList,
        ]);
    }
    public function getEmployeeLeaveBalance(Request $request)
    {
        $leave_type_id = $request->leave_type_id;
        $employee_id = $request->employee_id;
        if ($leave_type_id != '' && $employee_id != '') {
            $balanceData = $this->leaveRepository->calculateEmployeeLeaveBalanceWithAdvanced($leave_type_id, $employee_id);
            $advanceInfo = $this->leaveRepository->getAdvanceDaysInfo($leave_type_id, $employee_id);

            // Return structured response for frontend with adjustment info (same as ESS controller)
            return response()->json([
                'regular_balance' => round($balanceData['regular_balance'], 1),
                'advance_available' => round($balanceData['advance_available'], 1),
                'total_available' => round($balanceData['total_available'], 1),
                'total_entitlement' => round($balanceData['total_entitlement'], 1),
                'earned_days' => round($balanceData['earned_days'], 1),
                'used_days' => round($balanceData['used_days'], 1),
                'pending_days' => round($balanceData['pending_days'], 1),
                'applicable_on' => $balanceData['applicable_on'],
                'is_advance_period' => $advanceInfo['is_within_period'],
                'advance_days_allowed' => round($balanceData['advance_days_allowed'], 1),
                'max_advance_limit' => round($advanceInfo['max_limit'], 1),
                'period_months' => $advanceInfo['period_months'],
                'accrual_rate' => round($advanceInfo['accrual_rate'], 1),
                // Adjustment fields
                'has_adjustments' => $balanceData['adjustment_details']['has_adjustments'],
                'adjustment_additions' => round($balanceData['adjustment_details']['total_additions'], 1),
                'adjustment_deductions' => round($balanceData['adjustment_details']['total_deductions'], 1),
                'net_adjustment' => round($balanceData['adjustment_details']['net_adjustment'], 1),
                'adjustment_count' => $balanceData['adjustment_details']['adjustment_count']
            ]);
        }

        return response()->json(['error' => 'Invalid parameters'], 400);
    }


    public function applyForTotalNumberOfDays(Request $request)
    {
        $leaveTypeId = $request->leave_type_id;
        $application_from_date = dateConvertFormtoDB($request->application_from_date);
        $application_to_date = dateConvertFormtoDB($request->application_to_date);

        return $this->leaveRepository->calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date, $leaveTypeId);
    }


    public function store(ApplyForLeaveRequest $request)
    {
        $hr_email = null;
        $applyingId = json_decode($request['employee_id']);

        $employee = Employee::where('employee_id', $applyingId)->first();
        $hrApprover = $employee->hr();
        if ($hrApprover) {
            $hr_email = $hrApprover->email;
        }

        try {
            DB::beginTransaction();
            $input = $request->all();

            $getEmployeeEmail = $this->commonRepository->getEmployeeInfo(json_decode($request['employee_id']));
            $getEmployeeEmail = $getEmployeeEmail->email;
            $getEmployeeBranchId = $getEmployeeEmail->location_id ?? null;
            $getEmployeeFirstName = $this->commonRepository->getEmployeeInfo(json_decode($request['employee_id']))->first_name;
            $getEmployeeSecondName = $this->commonRepository->getEmployeeInfo(json_decode($request['employee_id']))->last_name;
            //        $getEmployeeEmail = $this->commonRepository->getEmployeeInfo(Auth::user()->id)->email;
            //        $getEmployeeFirstName = $this ->commonRepository->getEmployeeInfo(Auth::user()->id)->first_name;
            //        $getEmployeeSecondName = $this ->commonRepository->getEmployeeInfo(Auth::user()->id)->last_name;

            //$supervisor_id = Employee::where('employee_id', Auth::user()->id)->pluck('supervisor_id')->first();
            // $supervisor_id =DB::table('employee')->select('supervisor_id')->where('id',Auth::user()->id)->pluck('supervisor_id')->first();
            if (isset($getEmployeeBranchId)) {

                $supervisor_id = DB::table('employee')->where('employee_id', $applyingId)->where('location_id', $getEmployeeBranchId)->pluck('supervisor_id')->first();
            } else {

                //Here no branch id was found
                $supervisor_id = DB::table('employee')->where('employee_id', $applyingId)->pluck('supervisor_id')->first();
            }

            $currentFinancialYear = getCurrentFinancialYear();

            $supervisor_email = Employee::where('employee_id', $supervisor_id)->pluck('email')->first();

            $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
            $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
            $input['application_date'] = date('Y-m-d');


            //approval for the mks system
            $input['ceo_approval_type'] = 1;
            $input['ceo_approval_date'] = date('Y-m-d');
            $input['hr_approval'] = 1;
            $input['approve_by'] = Auth::user()->id;
            //  $input['hr_approval_date'] = date('Y-m-d');
            $input['hr_approval_date'] = date('Y-m-d');
            $input['final_status'] = LeaveStatus::PENDING;
            $input['status'] = 1;
            $input['approve_date'] = date('Y-m-d');
            $input['financial_year_id'] = $currentFinancialYear->id;

            //continue to save the details
            try {
                $leaveApplication = LeaveApplication::create($input);
                $bug = 0;
            } catch (Exception $e) {
                $bug = $e->getMessage();
                Log::info($e->getMessage());
            }

            $leaveType = LeaveType::where('leave_type_id', $request->leave_type_id)->pluck('leave_type_name');
            $leaveLatest = LeaveApplication::latest()->pluck('leave_application_id')->first();
            $mailContent = ([
                'leave_from_date' => $request->application_from_date,
                'staff_first_name' => $getEmployeeFirstName,
                'staff_last_name' => $getEmployeeSecondName,
                'leave_to_date' => $request->application_to_date,
                'no_of_days' => $request->number_of_day,
                'latest_leave' => $leaveLatest,
                'leaveType' => $leaveType,
            ]);


            //Dont send any email to anybody
            //
            //        //send mail to employee
            try {
                Mail::to($getEmployeeEmail)->send(new StaffLeaveApplicationMail($mailContent));
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' Staff Leave application email failed');
            }
            //send mail to supervisor
            try {
                Mail::to($supervisor_email)->send(new SupervisorLeaveApplicationMail($mailContent));
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' Supervisor Leave application email failed');
            }
            //send mail to hr
            // send notification to all P&C
            try {
                $approvers = $employee->getLocationLeaveApprovers();

                foreach ($approvers as $approver) {
                    event(new LeaveApplicationEvent($leaveApplication, $approver->employee_id));

                    // Check if user exists and is notifiable
                    if ($approver->user && method_exists($approver->user, 'notify')) {
                        $approver->user->notify(new LeaveApplicationSubmitted($leaveApplication));
                    } else {
                        Log::warning("Approver {$approver->employee_id} has no notifiable user account");
                    }
                }
            } catch (Exception $e) {
                Log::error('Notifications to Leave application failed: ' . $e->getMessage(), [
                    'employee_id' => $employee->employee_id,
                    'error' => $e->getTraceAsString()
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = 1;
            \Log::info($e->getMessage() . 'Leave application  failed');
        }

        if ($bug == 0) {
            return redirect()->route('applyForLeave.index')->with('success', 'Leave application successfully send.');
        } else {
            return redirect()->route('applyForLeave.index')->with('error', 'Something error found !, Please try again.');
        }
    }

    public function manualUpload()
    {
        $hasHRWiseEmployee = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();


        //fiscal year calculation here
        $fiscal_year = getCurrentFinancialYear();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date = $fiscal_year->end_date;
        $results = LeaveApplication::with(['employee', 'leaveType'])
            ->whereBetween('application_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->where('application_type', 'manual_upload')
            ->orderBy('status', 'asc')
            ->orderBy('leave_application_id', 'desc')
            ->get();
        $supervisor_approval = LeaveApplication::select('status')
            ->pluck('status');
        return view('admin.leave.leaveApplication.manualUpload', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role], compact('supervisor_approval'));
    }

    public function manualUploadSave()
    {
        //
    }

    public function manualUploadView()
    {
        $sample_file_link = url('admin_assets/sample_files/sample leaves_import_file.xlsx');
        return view('admin.leave.leaveApplication.import_excel', compact('sample_file_link'));
    }

    /**
     * Get employee details including supervisor info for AJAX requests
     */
    public function getEmployeeDetails($employeeId)
    {
        $employee = Employee::with('supervisor')->where('employee_id', $employeeId)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return response()->json([
            'employee_id' => $employee->employee_id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'supervisor' => $employee->supervisor ? [
                'employee_id' => $employee->supervisor->employee_id,
                'first_name' => $employee->supervisor->first_name,
                'middle_name' => $employee->supervisor->middle_name,
                'last_name' => $employee->supervisor->last_name,
                'email' => $employee->supervisor->email,
            ] : null,
        ]);
    }

    /**
     * Get leave types available for an employee based on their leave group
     */
    public function getEmployeeLeaveTypes($employeeId)
    {
        $employee = Employee::where('employee_id', $employeeId)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Get applicable leave types based on employee's leave group and gender
        $leaveTypes = $employee->applicableLeaveTypes()->pluck('leave_type_name', 'leave_type_id');

        return response()->json($leaveTypes);
    }

    /**
     * Show form for admin to apply leave on behalf of an employee
     */
    public function applyOnBehalfCreate()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $employeeList = $this->commonRepository->employeeListForLeaves();

        // Leave types will be loaded dynamically based on selected employee's leave group
        return view('admin.leave.applyForLeave.apply_on_behalf_form', [
            'signed_in_user_role' => $signed_in_user_role,
            'employeeList' => $employeeList,
        ]);
    }

    /**
     * Store leave application submitted on behalf of an employee
     */
    public function applyOnBehalfStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'leave_type_id' => 'required',
            'application_from_date' => 'required',
            'application_to_date' => 'required',
            'number_of_day' => 'required|numeric',
        ]);

        $applyingId = json_decode($request['employee_id']);
        $employee = Employee::where('employee_id', $applyingId)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        try {
            DB::beginTransaction();
            $input = $request->all();

            $getEmployeeEmail = $employee->email;
            $getEmployeeBranchId = $employee->location_id ?? null;
            $getEmployeeFirstName = $employee->first_name;
            $getEmployeeSecondName = $employee->last_name;

            if (isset($getEmployeeBranchId)) {
                $supervisor_id = DB::table('employee')->where('employee_id', $applyingId)->where('location_id', $getEmployeeBranchId)->pluck('supervisor_id')->first();
            } else {
                $supervisor_id = DB::table('employee')->where('employee_id', $applyingId)->pluck('supervisor_id')->first();
            }

            $currentFinancialYear = getCurrentFinancialYear();
            $supervisor_email = Employee::where('employee_id', $supervisor_id)->pluck('email')->first();

            $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
            $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
            $input['application_date'] = date('Y-m-d');
            $input['employee_id'] = $applyingId;

            //approval for the mks system
            $input['ceo_approval_type'] = 1;
            $input['ceo_approval_date'] = date('Y-m-d');
            $input['hr_approval'] = 1;
            $input['approve_by'] = Auth::user()->id;
            $input['hr_approval_date'] = date('Y-m-d');
            $input['final_status'] = LeaveStatus::PENDING;
            $input['status'] = 1;
            $input['approve_date'] = date('Y-m-d');
            $input['financial_year_id'] = $currentFinancialYear->id;

            //continue to save the details
            try {
                $leaveApplication = LeaveApplication::create($input);
                $bug = 0;
            } catch (Exception $e) {
                $bug = $e->getMessage();
                Log::info($e->getMessage());
            }

            $leaveType = LeaveType::where('leave_type_id', $request->leave_type_id)->pluck('leave_type_name');
            $leaveLatest = LeaveApplication::latest()->pluck('leave_application_id')->first();
            $mailContent = ([
                'leave_from_date' => $request->application_from_date,
                'staff_first_name' => $getEmployeeFirstName,
                'staff_last_name' => $getEmployeeSecondName,
                'leave_to_date' => $request->application_to_date,
                'no_of_days' => $request->number_of_day,
                'latest_leave' => $leaveLatest,
                'leaveType' => $leaveType,
            ]);

            //send mail to employee
            try {
                Mail::to($getEmployeeEmail)->send(new StaffLeaveApplicationMail($mailContent));
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' Staff Leave application email failed');
            }

            //send mail to supervisor
            try {
                Mail::to($supervisor_email)->send(new SupervisorLeaveApplicationMail($mailContent));
            } catch (Exception $e) {
                Log::info($e->getMessage() . ' Supervisor Leave application email failed');
            }

            //send notification to all P&C
            try {
                $approvers = $employee->getLocationLeaveApprovers();

                foreach ($approvers as $approver) {
                    event(new LeaveApplicationEvent($leaveApplication, $approver->employee_id));

                    if ($approver->user && method_exists($approver->user, 'notify')) {
                        $approver->user->notify(new LeaveApplicationSubmitted($leaveApplication));
                    } else {
                        Log::warning("Approver {$approver->employee_id} has no notifiable user account");
                    }
                }
            } catch (Exception $e) {
                Log::error('Notifications to Leave application failed: ' . $e->getMessage(), [
                    'employee_id' => $employee->employee_id,
                    'error' => $e->getTraceAsString()
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = 1;
            \Log::info($e->getMessage() . 'Leave application failed');
        }

        if ($bug == 0) {
            return redirect()->route('applyOnBehalf.create')->with('success', 'Leave application successfully submitted on behalf of employee.');
        } else {
            return redirect()->route('applyOnBehalf.create')->with('error', 'Something error found!, Please try again.');
        }
    }
}

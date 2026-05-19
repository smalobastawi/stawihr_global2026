<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Models\User;
use App\LeaveRollover;
use App\Models\Notice;
use App\Models\Warning;
use App\Models\Employee;
use App\Models\IpSetting;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Termination;
use Illuminate\Http\Request;
use App\Models\EmployeeAward;
use App\Models\FinancialYear;
use App\Models\LeaveApplication;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeExperience;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Lib\Enumerations\LeaveStatus;
use App\Repositories\LeaveRepository;
use App\Lib\Enumerations\GeneralStatus;
use Spatie\Activitylog\Models\Activity;
use App\Repositories\AttendanceRepository;
use App\Models\EmployeeEducationQualification;
use DateTime;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{


    protected $leaveApplication, $notice, $employeeExperience, $department, $employee, $employeeAward, $attendanceRepository, $warning, $termination;
    protected $leaveRepository;
    public function __construct(
        LeaveApplication    $leaveApplication,
        Notice              $notice,
        EmployeeExperience $employeeExperience,
        Department $department,
        Employee $employee,
        EmployeeAward $employeeAward,
        AttendanceRepository $attendanceRepository,
        Warning             $warning,
        Termination $termination,
        LeaveRepository $leaveRepository,
    ) {

        $this->leaveApplication = $leaveApplication;
        $this->notice = $notice;
        $this->employeeExperience = $employeeExperience;
        $this->department = $department;
        $this->employee = $employee;
        $this->employeeAward = $employeeAward;
        $this->attendanceRepository = $attendanceRepository;
        $this->warning = $warning;
        $this->termination = $termination;
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;
       
        $date = date('Y-m-d');
        $today = $date;
        $activeFinancialYear = getCurrentFinancialYear();
        if ($activeFinancialYear == null) {
            $fiscal_start_date = date('Y-m-d', strtotime('first day of January this year'));
            $fiscal_end_date = date('Y-m-d', strtotime('last day of December this year'));
        } else {
            $fiscal_start_date = $activeFinancialYear->start_date;
            $fiscal_end_date = $activeFinancialYear->end_date;
        }
        $ip_setting = IpSetting::orderBy('id', 'desc')->first();
        $ip_attendance_status = 0;
        $ip_check_status = 0;
        $login_employee = employeeInfo();
        $count_user_login_today = null;
        $count_user_login_present = null;
        if ($login_employee) {
            $count_user_login_today = Attendance::where('employee_id', $login_employee->employee_id)->where('date', date('Y-m-d'))->where('time_in', '!=', NULL)->count();
            $count_user_login_present = Attendance::where('employee_id', $login_employee->employee_id)->where('date', date('Y-m-d'))->where('time_out', '!=', NULL)->count();
        }
        if ($login_employee) {
            $now = now(); // Current datetime
            
            // Get the employee's shift details
            $shift = $login_employee->workShift;
            
            if ($shift) {
                // Parse shift times
                $shiftStartTime = Carbon::parse($shift->start_time);
                $shiftEndTime = Carbon::parse($shift->end_time);
                
                // Determine the relevant attendance date range based on shift
                if ($shiftStartTime->lt($shiftEndTime)) {
                    // Day shift (same calendar date)
                    $attendanceDate = $now->format('Y-m-d');
                    $attendance = Attendance::where('employee_id', $login_employee->employee_id)
                                          ->where('date', $attendanceDate)
                                          ->first();
                } else {
                    // Night shift (spans across midnight)
                    if ($now->gte(Carbon::parse($shift->start_time))) {
                        // Current time is after shift start (evening)
                        $attendanceDate = $now->format('Y-m-d');
                    } else {
                        // Current time is before shift end (morning of next day)
                        $attendanceDate = $now->copy()->subDay()->format('Y-m-d');
                    }
                    
                    $attendance = Attendance::where('employee_id', $login_employee->employee_id)
                                          ->where('date', $attendanceDate)
                                          ->first();
                }
                
                // Calculate checkout window (1 hour after shift end)
                $checkoutWindowStart = $now->copy()->setTimeFromTimeString($shift->end_time);
                $checkoutWindowEnd = $checkoutWindowStart->copy()->addHour();
                
                // Adjust for night shift
                if ($shiftStartTime->gt($shiftEndTime)) {
                    if ($now->lt($checkoutWindowStart)) {
                        $checkoutWindowStart->subDay();
                        $checkoutWindowEnd->subDay();
                    }
                }
                
                // Determine if checkout button should be shown
                $show_checkout_button = false;
                if ($attendance && $attendance->time_in && !$attendance->time_out) {
                    if ($now->between($checkoutWindowStart, $checkoutWindowEnd) || $now->gt($checkoutWindowEnd)) {
                        $show_checkout_button = true;
                    }
                }
                
                // Original counts
                $count_user_login_today = $attendance && $attendance->time_in ? 1 : 0;
                $count_user_login_present = $attendance && $attendance->time_out ? 1 : 0;
            }
        }
        $totalEmployee = $this->employee->where('status', GeneralStatus::ACTIVE)->count();
        $totalDepartment = $this->department->count();
        if ($ip_setting) {

            // if 0 then attendance will not take
            $ip_attendance_status = $ip_setting->status;
            // if 0 then ip will not checked for attendance
            $ip_check_status = $ip_setting->ip_status;
        }

        $leaveApplicationsAppliedToday = $this->leaveApplication
            ->whereDate('application_date', $today)
            ->count();

        if (!Auth::user()->hasAnyRole(['SuperAdmin', 'HR Administrator'])) {
            $date = date('Y-m-d');
            $attendanceData = Attendance::where('date', $date)->with(['employee', 'department'])->get();

            $attendanceDataUser = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(date("Y-m-01"), date("Y-m-d"), session('logged_session_data.employee_id'));

            $employeeTotalAward = $this->employeeAward
                ->select(DB::raw('count(*) as totalAward'))
                ->where('employee_id', session('logged_session_data.employee_id'))
                ->whereBetween('month', [date("Y-01"), date("Y-12")])
                ->first();

            $notice = $this->notice->with('createdBy')->orderBy('notice_id', 'DESC')->where('status', 'Published')->get();
            $terminationData = $this->termination->with('terminateBy')->where('terminate_to', session('logged_session_data.employee_id'))->first();

            //  $hasSupervisorWiseEmployee = $this->employee->select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
            $hasSupervisorWiseEmployee = Employee::where('supervisor_id', session('logged_session_data.employee_id'))
                ->pluck('employee_id')
                ->toArray();
            $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();


            $leaveApplication = $this->leaveApplication->with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->where('final_status', LeaveStatus::PENDING)
                // ->whereIn('employee_id',$employeeIds)
                ->orderBy('final_status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();



            // If the logged-in user is the supervisor
            $onLeaveTodayCount = 0;
            if ($login_employee) {
                $onLeaveTodayCount = $this->leaveApplication->with(['employee', 'leaveType'])
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->whereDate('application_from_date', '<=', $today)
                    ->whereDate('application_to_date', '>=', $today)
                    ->whereHas('employee', function ($query) use ($login_employee) {
                        $query->where('supervisor_id', $login_employee->employee_id);
                    })
                    ->orderBy('leave_application_id', 'desc')
                    ->count();
            }

            $employeeInfo = $this->employee->with('designation')->where('employee_id', session('logged_session_data.employee_id'))->first();

            $employeeTotalLeave = $this->leaveApplication->select(DB::raw('IFNULL(SUM(number_of_day), 0) as totalNumberOfDays'))
                ->where('employee_id', session('logged_session_data.employee_id'))
                ->where('final_status', LeaveStatus::APPROVE)
                ->whereBetween('approve_date', [$fiscal_start_date,  $fiscal_end_date])
                // ->whereIn('employee_id',$employeeIds)
                ->first();
            $warning = $this->warning->with(['warningBy'])->where('warning_to', session('logged_session_data.employee_id'))->get();

            // date of birth in this month

            $firstDayThisMonth = date('Y-m-d');
            $lastDayThisMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayThisMonth)));

            $from_date_explode = explode('-', $firstDayThisMonth);
            $from_day = $from_date_explode[2];
            $from_month = $from_date_explode[1];
            $concatFormDayAndMonth = $from_month . '-' . $from_day;

            $to_date_explode = explode('-', $lastDayThisMonth);
            $to_day = $to_date_explode[2];
            $to_month = $to_date_explode[1];
            $concatToDayAndMonth = $to_month . '-' . $to_day;

            $upcoming_birtday = Employee::orderBy('date_of_birth', 'desc')->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= '" . $concatFormDayAndMonth . "' AND DATE_FORMAT(date_of_birth, '%m-%d') <= '" . $concatToDayAndMonth . "' ")->get();

            $data = [
                'attendanceData' => $attendanceData,
                'attendanceDataUser' => $attendanceDataUser,
                'employeeTotalAward' => $employeeTotalAward,
                'notice' => $notice,
                'leaveApplication' => $leaveApplication,
                'employeeInfo' => $employeeInfo,
                'employeeTotalLeave' => $employeeTotalLeave,
                'warning' => $warning,
                'terminationData' => $terminationData,
                'upcoming_birtday' => $upcoming_birtday,
                'signed_in_user_role' => $signed_in_user_role,
                'ip_attendance_status' => $ip_attendance_status,
                'ip_check_status' => $ip_check_status,
                'count_user_login_today' => $count_user_login_today,
                'count_user_login_present' => $count_user_login_present,
                'totalEmployee' => $totalEmployee,
                'totalDepartment' => $totalDepartment,
                'totalAttendance' => count($attendanceData),
                'totalAbsent' => $totalEmployee - count($attendanceData),
                'onLeaveTodayCount' => $onLeaveTodayCount,
                'totalOnLeaveToday' => $onLeaveTodayCount,
            ];

            return view('admin.generalUserHome', $data);
        }
        $attendanceDataUser = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(date("Y-m-01"), date("Y-m-d"), session('logged_session_data.employee_id'));




        $totalOnLeaveToday = $this->leaveApplication->with(['employee', 'leaveType'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->whereDate('application_from_date', '<=', $today)
            ->whereDate('application_to_date', '>=', $today)
            ->count();

        //$hasSupervisorWiseEmployee = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $hasSupervisorWiseEmployee = Employee::where('supervisor_id', session('logged_session_data.employee_id'))
            ->pluck('employee_id')
            ->toArray();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        if (count($hasSupervisorWiseEmployee) == 0) {
            $leaveApplication = [];
        } elseif ($signed_in_user_role == 10) {
            $leaveApplication = $this->leaveApplication->with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->where('final_status', LeaveStatus::PENDING)
                //  ->whereIn('employee_id',$employeeIds)
                ->orderBy('final_status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();
        } else {
            $leaveApplication = $this->leaveApplication->with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->where('final_status', LeaveStatus::PENDING)
                // ->whereIn('employee_id',$employeeIds)
                ->orderBy('final_status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();
        }
        $onLeaveTodayCountForAdmins = 0;
        if ($login_employee) {
            $onLeaveTodayCountForAdmins = $this->leaveApplication->where('final_status', LeaveStatus::APPROVE)
                ->whereDate('application_from_date', '<=', $today)
                ->whereDate('application_to_date', '>=', $today)
                ->whereHas('employee', function ($query) use ($login_employee) {
                    $query->where('supervisor_id', $login_employee->employee_id);
                })
                ->count();
        } else {
            $onLeaveTodayCountForAdmins = $this->leaveApplication->where('final_status', LeaveStatus::APPROVE)
                ->whereDate('application_from_date', '<=', $today)
                ->whereDate('application_to_date', '>=', $today)
                ->count();
        }

        $attendanceData = Attendance::where('date', $date)->with(['employee', 'department'])->get();

        $employeeAward = $this->employeeAward->with(['employee' => function ($d) {
            $d->with('department');
        }])->limit(10)->orderBy('employee_award_id', 'DESC')->get();

        $notice = $this->notice
            ->with('createdBy')->orderBy('notice_id', 'DESC')
            ->where('status', 'Published')
            ->get();

        // date of birth in this month
        $firstDayThisMonth = date('Y-m-d');
        $lastDayThisMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayThisMonth)));
        $from_date_explode = explode('-', $firstDayThisMonth);
        $from_day = $from_date_explode[2];
        $from_month = $from_date_explode[1];
        $concatFormDayAndMonth = $from_month . '-' . $from_day;

        $to_date_explode = explode('-', $lastDayThisMonth);
        $to_day = $to_date_explode[2];
        $to_month = $to_date_explode[1];
        $concatToDayAndMonth = $to_month . '-' . $to_day;

        $upcoming_birtday = Employee::orderBy('date_of_birth', 'desc')->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= '" . $concatFormDayAndMonth . "' AND DATE_FORMAT(date_of_birth, '%m-%d') <= '" . $concatToDayAndMonth . "' ")->get();
        //$signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        //$myAttendanceStatus = Attendance::where('employee_id', $login_employee->employee_id)->where('date', $date)->first();
        $activityLogs = Activity::take(20)->with('causer')->orderBy('created_at', 'desc')->get();
        $activityLogs->transform(function ($item, $key) {
            $properties = json_decode($item->properties);
            // Ensure $properties is an object before accessing its attributes
            if (is_object($properties) && isset($properties->attributes)) {
                $item->attributes = $properties->attributes;
            } else {
                $item->attributes = null; // or handle it as needed
            }

            if (isset($properties->old)) {
                $item->old = $properties->old;
            } else {
                $item->old = null; // or handle it as needed
            }

            return $item;
        });

        $data = [
            'attendanceData' => $attendanceData,
            'totalEmployee' => $totalEmployee,
            'totalDepartment' => $totalDepartment,
            'totalAttendance' => count($attendanceData),
            'totalAbsent' => $totalEmployee - count($attendanceData),
            'employeeAward' => $employeeAward,
            'notice' => $notice,
            'leaveApplication' => $leaveApplication,
            'upcoming_birtday' => $upcoming_birtday,
            'signed_in_user_role' => $signed_in_user_role,
            'ip_attendance_status' => $ip_attendance_status,
            'ip_check_status' => $ip_check_status,
            'count_user_login_today' => $count_user_login_today,
            // 'myAttendanceStatus' => $myAttendanceStatus
            'count_user_login_present' => $count_user_login_present,
            'activityLogs' => $activityLogs,
            'onLeaveTodayCount' => $onLeaveTodayCountForAdmins,
            'totalOnLeaveToday' => $leaveApplicationsAppliedToday,
            'attendanceDataUser' => $attendanceDataUser,
        ];

        return view('admin.adminhome', $data);
    }


    public function profile()
    {
        $currentDate          = now();
        $currentFinancialYear = FinancialYear::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();
        $fiscal_start_date = $currentFinancialYear->start_date;
        $fiscal_end_date = $currentFinancialYear->end_date;


        $login_employee = employeeInfo();
        $employeeInfo = null;
        $employeeExperience = null;
        $employeeEducation = null;
        $supervisor = null;

        if($login_employee){
         $leaveTypes = $login_employee->applicableLeaveTypes();
        }else{
            $leaveTypes =[];
        }
       
        $annualLeaveDays = LeaveType::where('leave_type_id', 2)
            ->pluck('num_of_day')
            ->first();
        $rollover_leaves = LeaveRollover::where('employee_id', session('logged_session_data.employee_id'))
            ->where('final_status', LeaveStatus::APPROVE)
            ->whereBetween('date_approved', [date($fiscal_start_date), date($fiscal_end_date)])
            //->pluck('days_requested')
            ->first();
        $approvedLeaves = LeaveApplication::where('employee_id', session('logged_session_data.employee_id'))
            ->where('final_status', 2)
            ->where('leave_type_id', 2)
            ->whereBetween('approve_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->sum('number_of_day');

        if ($rollover_leaves != null) {
            $totalLeavesForTheYear = ($rollover_leaves['days_requested'] + $annualLeaveDays) - $approvedLeaves;
        } else {
            $totalLeavesForTheYear = $annualLeaveDays - $approvedLeaves;
        }
        if ($login_employee) {
            $employeeInfo = Employee::with(['workLocation'])->where('employee.employee_id', session('logged_session_data.employee_id'))->first();
            $employeeExperience = EmployeeExperience::where('employee_id', session('logged_session_data.employee_id'))->get();
            $employeeEducation = EmployeeEducationQualification::where('employee_id', session('logged_session_data.employee_id'))->get();
            $supervisor = Employee::where('employee.employee_id', $employeeInfo->supervisor_id)->first();
        }
        // I want to calcultate the leave consumption for the current fiscal year for each leave type and prepare it for display
        $leaveTyesData = [];
        if ($login_employee) {
            foreach ($leaveTypes as $leaveType) {

                $leaveUsed = LeaveApplication::where('employee_id', session('logged_session_data.employee_id'))
                    ->where('final_status', 2)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->whereBetween('approve_date', [date($fiscal_start_date), date($fiscal_end_date)])
                    ->sum('number_of_day');

                $totalDays = $employeeInfo->getEarnedLeaveDays($leaveType->leave_type_id);
                $rolloverDays = LeaveRollover::where('employee_id', $login_employee->employee_id)
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->where('financial_year_id', $currentFinancialYear->id)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->value('days_requested') ?? 0;;
                $leaveTye['name'] = $leaveType->leave_type_name;
                $leaveTye['days_entitled'] = $login_employee->leaveGroup->settings()->where('leave_type_id', $leaveType->leave_type_id)->first()->annual_entitlement;
                $leaveTye['leave_type_id'] = $leaveType->leave_type_id;
                $leaveTye['totalDays'] =  $totalDays;
                $leaveTye['days_used'] = $leaveUsed;
                $leaveTye['roll_over_days'] = $rolloverDays;
                $leaveTye['totalBlance'] =  ($totalDays + $rolloverDays) - $leaveUsed;
                $leaveTyesData[] = $leaveTye;
            }
        }


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.user.user.profile', [
            'employeeInfo' => $employeeInfo,
            'employeeExperience' => $employeeExperience,
            'employeeEducation' => $employeeEducation,
            'supervisor_info' => $supervisor,
            'signed_in_user_role' => $signed_in_user_role,
            'leaveTypes' => $leaveTypes,
            'rollover_leaves' => $rollover_leaves,
            'totalLeavesForTheYear' => $totalLeavesForTheYear,
            'leaveTyesData' => $leaveTyesData,
        ]);
    }


    public function mail()
    {

        $user = array(
            'name' => "Learning Laravel",
        );

        Mail::send('emails.mailExample', $user, function ($message) {
            $message->to("kamrultouhidsak@gmail.com");
            $message->subject('E-Mail Example');
        });

        return "Your email has been sent successfully";
    }
}

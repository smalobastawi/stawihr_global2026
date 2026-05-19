<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Lib\Enumerations\AttendanceEntryType;
use App\Models\BiometricUser;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeAward;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\WhiteListedIp;
use App\Models\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Http;


class AttendanceController extends Controller
{
    protected $biometimeAPIURL;
    function __construct(

        Department $department,
        Employee $employee,
        EmployeeAward $employeeAward,
        AttendanceRepository $attendanceRepository,

    ) {
        $this->department = $department;
        $this->employee = $employee;
        $this->attendanceRepository = $attendanceRepository;
        $this->biometimeAPIURL = config('app.BIOTIME_API_URL');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->get('date') == null) {
            $date = date('Y-m-d');
        } else {
            $date = dateConvertFormtoDB($request->get('date'));
        }

        $department = $request->get('department_id');
        $departmentList = Department::get();
        $employeeShifts = WorkShift::get();
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');
        $user = Auth::user();
        $allowedIds = [];

        if ($user->hasRole(['SuperAdmin', 'HR Administrator'])) {
            // Admins see all employees
            $allowedIds = Employee::where('status', 1)->pluck('employee_id')->toArray();
        } else {
            // Regular users see themselves and their subordinates
            $employeeId = $user->employeeDetails->employee_id ?? null;

            if ($employeeId) {
                $subordinateIds = $user->employeeDetails->subordinates()->pluck('employee_id')->toArray();
                $allowedIds = array_merge([$employeeId], $subordinateIds);
            }
        }

        $attendanceData2 = Employee::select([
            'employee.employee_id',
            'employee.department_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),
            DB::raw('(SELECT DATE_FORMAT(MIN(a.time_in), \'%h:%i %p\') 
                    FROM attendances a 
                    WHERE a.date = ? 
                    AND a.employee_id = employee.employee_id
                    AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS inTime'),
            DB::raw('(SELECT a.presence_status 
                    FROM attendances a
                    WHERE a.date = ? 
                    AND a.employee_id = employee.employee_id
                    AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS presence'),
            DB::raw('(SELECT DATE_FORMAT(MAX(a.lunch_checkin), \'%h:%i %p\') 
                    FROM attendances a
                    WHERE a.date = ? 
                    AND a.employee_id = employee.employee_id
                    AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS lunch_checkin'),
            DB::raw('(SELECT DATE_FORMAT(MAX(a.time_out), \'%h:%i %p\') 
                    FROM attendances a
                    WHERE a.date = ? 
                    AND a.employee_id = employee.employee_id
                    AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS outTime')
        ])
            ->setBindings([$date, $date, $date, $date]) // Bind dates for security
            ->where('employee.status', 1)
            ->whereIn('employee.employee_id', $allowedIds ?: [0]) // Fallback if empty
            ->orderBy('employee.first_name', 'ASC')
            ->get();


        $presences = [
            "ABSENT" => "ABSENT",
            "PRESENT" => "PRESENT",
           
        ];


        return view(
            'admin.attendance.manualAttendance.newAttendance',
            [
                'departmentList' => $departmentList,
                'attendanceData' => $attendanceData2,
                'reportingTime' => $reportingTime,
                'leavingTime' => $leavingTime,
                'todayDate' => $date,
                'presences' => $presences,
                'employeeShifts' => $employeeShifts,
            ]
        );
    }

    public function filterData(Request $request)
    {

        $date = dateConvertFormtoDB($request->get('date'));

        $department = $request->get('department_id');
        $departmentList = Department::get();
        $employeeShifts = WorkShift::get();
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');
        // First get the supervisor's allowed employee IDs
        $user = Auth::user();
        $allowedIds = [];

        if ($user->hasRole(['SuperAdmin', 'HR Administrator'])) {
            // Admins see all employees
            $allowedIds = Employee::where('status', 1)->pluck('employee_id')->toArray();
        } else {
            // Regular users see themselves and their subordinates
            $employeeId = $user->employeeDetails->employee_id ?? null;

            if ($employeeId) {
                $subordinateIds = $user->employeeDetails->subordinates()->pluck('employee_id')->toArray();
                $allowedIds = array_merge([$employeeId], $subordinateIds);
            }
        }


        // Now build  department-specific query
        $attendanceData = Employee::select([
            'employee.employee_id',
            'employee.department_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),
            DB::raw('(SELECT DATE_FORMAT(MIN(a.time_in), \'%h:%i %p\') 
            FROM attendances a 
            WHERE a.date = ? 
            AND a.employee_id = employee.employee_id
            AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS inTime'),
            DB::raw('(SELECT DISTINCT(a.presence_status) 
            FROM attendances a
            WHERE a.date = ? 
            AND a.employee_id = employee.employee_id
            AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS presence'),
            DB::raw('(SELECT DATE_FORMAT(MAX(a.lunch_checkin), \'%h:%i %p\') 
            FROM attendances a
            WHERE a.date = ? 
            AND a.employee_id = employee.employee_id
            AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS lunch_checkin'),
            DB::raw('(SELECT DATE_FORMAT(MAX(a.time_out), \'%h:%i %p\') 
            FROM attendances a
            WHERE a.date = ? 
            AND a.employee_id = employee.employee_id
            AND a.employee_id IN (' . implode(',', $allowedIds) . ')) AS outTime')
        ])
            ->setBindings([$date, $date, $date, $date]) // Bind dates for security
            ->where('employee.department_id', $department)
            ->where('employee.status', 1)
            ->whereIn('employee.employee_id', $allowedIds ?: [0]) // Fallback if empty
            ->orderBy('employee.first_name', 'ASC')
            ->get();


        $presences = [
            "ABSENT" => "ABSENT",
            "PRESENT" => "PRESENT",
          
        ];

        return view(
            'admin.attendance.2022Upgrade.index',
            [
                'departmentList' => $departmentList,
                'attendanceData' => $attendanceData,
                'reportingTime' => $reportingTime,
                'leavingTime' => $leavingTime,
                'todayDate' => $date,
                'presences' => $presences,
                'employeeShifts' => $employeeShifts,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAttendanceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $date = dateConvertFormtoDB($request->get('date'));
        $att_month = date('Y-m', strtotime($date));


        $date1 = Carbon::createFromFormat('Y-m-d', $att_month . '-25');
        $date2 = Carbon::createFromFormat('Y-m-d', $date);

        if ($date2->gt($date1)) {
            $Datetime = new DateTime($att_month);
            $Datetime->modify('+1 months');
            $month = $Datetime->format('Y-m');
        } else {
            $month = $att_month;
        }

        $msg_error = 0;
        foreach ($request->employee_id as $key => $employee_id) {
            $employeeType = Employee::where('employee_id', $employee_id)->pluck('employee_type');


            if ($request->inTime[$key] == null) {
                $timeIn = null;
            } else {
                $timeIn = $date . ' ' . date("H:i:s", strtotime($request->inTime[$key]));
            }

            if ($request->outTime[$key] == null) {
                $timeOut = null;
            } else {
                $timeOut = $date . ' ' . date("H:i:s", strtotime($request->outTime[$key]));
            }
            
            $working_time = Carbon::parse($date . $request->inTime[$key])->diffInHours(\Carbon\Carbon::parse($date . $request->outTime[$key]));

            $overtime = $working_time - 9;
            $late_time = '';

            if ($working_time < 9) {
                $late_time = 9 - $working_time;
            }

            $employee1 = Employee::where('employee_id', $employee_id)->with('workShifts')->first();
            $workShiftId1 = $employee1->workShifts()->first();

            $att_data = [
                "employee_id" => $employee_id,
                "national_id" => $employee1->national_id,
                "presence_status" => $request->presence[$key],
                "date" => $date,
                "time_in" => $timeIn,
                "time_out" => $timeOut,
               
                'working_time' => Carbon::parse($date . $request->inTime[$key])->diffInHours(\Carbon\Carbon::parse($date . $request->outTime[$key])),
                'over_time' => $overtime,
                'late_time' => $late_time,
                'department_id' => $employee1->department_id,
                'created_by' => Auth::user()->id,
                'month' => $month,
                'entry_type' => 2,
                'employee_type' => $employeeType,
                'work_shift_id' => 1,
            ];

            $attendance = Attendance::updateOrCreate(
                [
                    "employee_id" => $employee_id,
                    "date" => $date,
                ],
                $att_data
            );


        }

        if ($msg_error == 0) {
            return redirect()->route('newAttendanceIndex')->with('success', 'Attendance successfully saved.');
        } else {
            return redirect()->route('newAttendanceIndex')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ' . $msg_error);
        }
    }

    // ip attendance


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAttendanceRequest $request
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    // ip attendance

    public function ipAttendance(Request $request)

    {
        $employeeStatus = Employee::where('employee_id', request('employee_id'))->first();
        $employeeDetails = Employee::where('employee_id', request('employee_id'))->first();
        if ($employeeStatus->status != 1) {
            return redirect()->back()->with('error', 'Your  account is not active. Please contact P&C for assistance');
        }
        if ($employeeDetails->work_shift_id == null) {
            return redirect()->back()->with('error', 'Work shift not found. Contact P&C for assistance');
        }
        //Check if the employee has biometric attendance record from Biometric System
        $biometicStatusToday = helper_getBiometricAttendance();

        try {
            $national_id = $request->national_id;
            $ip_check_status = $request->ip_check_status;
            $user_ip = \Request::ip();
            $employee_id = $request->employee_id;
            $employeeDetails = Employee::find($employee_id);
            $now = now();
        
            // Get shift details
            $shift = $employeeDetails->workShift;
            
            if (!$shift) {
                return redirect()->back()->with('error', 'Employee shift not configured.');
            }
        
            // Determine attendance date based on shift
            $shiftStart = Carbon::parse($shift->start_time);
            $shiftEnd = Carbon::parse($shift->end_time);
            $isNightShift = $shiftEnd->lt($shiftStart);
        
            if ($isNightShift && $now->lt($shiftEnd)) {
                // For night shift when current time is before end time (early morning)
                $attendanceDate = $now->copy()->subDay()->format('Y-m-d');
            } else {
                // Normal case (day shift or night shift after start time)
                $attendanceDate = $now->format('Y-m-d');
            }
        
            // Check IP whitelist if enabled
            if ($ip_check_status == 1) {
                $check_white_listed = WhiteListedIp::where('white_listed_ip', $user_ip)->exists();
                if (!$check_white_listed) {
                    return redirect()->back()->with('error', 'Invalid IP Address.');
                }
            }
        
            // Handle check-in/check-out logic
            if ($request->attendanceType == "checkIn") {
                // Check if already checked in for this shift period
                $existingCheckin = Attendance::where('employee_id', $employee_id)
                    ->where('date', $attendanceDate)
                    ->whereNotNull('time_in')
                    ->first();
        
                if ($existingCheckin) {
                    return redirect()->back()->with('error', 'Attendance check-in already recorded for this shift.');
                }
        
                // Create new attendance record
                $attendance = Attendance::create([
                    'employee_id' => $employee_id,
                    'date' => $attendanceDate,
                    'time_in' => $now,
                    'presence_status' => "PRESENT",
                    'created_by' => Auth::id(),
                    'department_id' => $request->department_id,
                    'entry_type' => AttendanceEntryType::WEB,
                    'national_id' => $employeeDetails->national_id,
                    'payroll_number' => $employeeDetails->payroll_number ?? '',
                    'work_shift_id' => $shift->work_shift_id,
                ]);
        
                return redirect()->back()->with('success', 'Check-in recorded successfully.');
        
            } else {
                // Handle check-out
                $attendance = Attendance::where('employee_id', $employee_id)
                    ->where('date', $attendanceDate)
                    ->first();
        
                if (!$attendance) {
                    return redirect()->back()->with('error', 'No check-in found for this shift.');
                }
        
                if ($attendance->time_out) {
                    return redirect()->back()->with('error', 'Check-out already recorded for this shift.');
                }
        
                // Calculate working hours
                $timeIn = Carbon::parse($attendance->time_in);
                $working_hours = $timeIn->diffInHours($now);
                $standard_hours = 9; // Adjust this based on your requirements
        
                $updateData = [
                    'time_out' => $now,
                    'working_time' => $working_hours,
                    'over_time' => max(0, $working_hours - $standard_hours),
                    'late_time' => max(0, $standard_hours - $working_hours),
                ];
        
                $attendance->update($updateData);
        
                return redirect()->back()->with('success', 'Check-out recorded successfully.');
            }
        
        } catch (\Exception $e) {
            \Log::error("Attendance error: " . $e->getMessage());
            return $e->getMessage();
            return redirect()->back()->with('error', 'An error occurred. Please contact support.');
        }
    }

    public function biometricAttendance()
    {

        $date = date('Y-m-d');
        $attendanceData = Attendance::with('employee')->get();

        $totalEmployee = $this->employee->where('status', 1)->count();

        $totalDepartment = $this->department->count();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();


        $data = [
            'attendanceData' => $attendanceData,
            'totalEmployee' => $totalEmployee,
            'totalDepartment' => $totalDepartment,
            'totalAttendance' => count($attendanceData),
            'totalAbsent' => $totalEmployee - count($attendanceData),
            'signed_in_user_role' => $signed_in_user_role,
        ];

        return view('admin.attendance.index', $data);
    }
}

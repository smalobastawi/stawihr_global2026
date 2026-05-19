<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\AttendaceOvertimes;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Department;
use App\Models\Designation;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\EmployeeAttendanceApprove;
use App\Models\EmployeeOvertime;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class OvertimeApprovalController extends Controller
{

    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function ovetimes()
    {

        $departmentList = Department::get();
        $designationList = Designation::get();
        // Get unique locations from employees
        $locationList = Location::where('status', GeneralStatus::ACTIVE)
            ->get();
        return view('admin.attendance.overtimes.index', compact('departmentList', 'designationList', 'locationList'));
    }

    public function filterOvertime(Request $request)
    {
        $data = dateConvertFormtoDB($request->get('date'));
        $department = $request->get('department_id');
        $designation = $request->get('designation_id');
        $location = $request->get('location');
        $dateFrom = $request->get('date_from') ? dateConvertFormtoDB($request->get('date_from')) : null;
        $dateTo = $request->get('date_to') ? dateConvertFormtoDB($request->get('date_to')) : null;

        $departmentList = Department::get();
        $designationList = \App\Models\Designation::get();
        $locationList = Location::where('status', GeneralStatus::ACTIVE)
            ->get();

        $result = [];
        $week_days = [];
        $week_data = [];

        if ($request) {
            $results = $this->getEmployeeWeeklyAttendanceHere($request, $department, $designation, $location, $dateFrom, $dateTo);

            if (count($results) > 0 && isset($results["data"]['attendance'])) {
                $week_days = $results["data"]["week_days"];
                $week_data = $results["data"]["attendance"];
                $attendanceData = $results["attendanceData1"];
            } else {
                $attendanceData = [];
            }
        }

        return view('admin.attendance.overtimes.index', [
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'locationList' => $locationList,
            'attendanceData' => $attendanceData
        ]);
    }

    public function approveOvertime(Request $request)
    {

        $date = dateConvertFormtoDB($request->date);
        $dataFormat = [];
        $bug = 0;

        // Check if in_time is provided and is an array
        if (!isset($request->in_time) || !is_array($request->in_time)) {
            return redirect()->back()->with('error', 'No overtime data submitted for approval.');
        }

        $count = count($request->in_time);
        for ($i = 0; $i < $count; $i++) {
            if ($request->approval_status[$i] == 'approved') {

                if ($request->approved_overtime[$i] == null) {
                    $approved_over_time = $request->overtime_worked[$i];
                } else {
                    $approved_over_time = $request->approved_overtime[$i];
                }
                $employeeSupervised = Employee::where('employee_id', $request->employee_id[$i])
                    ->where('supervisor_id', Auth::user()->employeeDetails->employee_id)
                    ->first();

                //Skip if the emplyee is not supervised by the user.
                if (!$employeeSupervised) {
                    continue;
                }

                $dataFormat[$i] = [
                    'payroll_number' => $request->payroll_number[$i],
                    'employee_id' => $request->employee_id[$i],
                    'time_in' => $request->in_time[$i],
                    'time_out' => $request->out_time[$i],
                    'total_time_worked' => $request->worked_hours[$i],
                    'approved_over_time' => $approved_over_time,
                    'department_id' => $request->department_id[$i],
                    'attendance_entry_id' => $request->attendance_entry_id[$i],
                    'date' => $date,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];
                $attendanceData[$i] =  [
                    'approved_over_time' => $dataFormat[$i]['approved_over_time'],
                    'overtime_approval_by' => Auth::user()->id,
                ];

                try {
                    if (count($dataFormat) > 0) {
                        AttendaceOvertimes::updateOrCreate(['attendance_entry_id' => $dataFormat[$i]['attendance_entry_id']], $dataFormat[$i]);
                        $attendance = Attendance::findOrFail($dataFormat[$i]['attendance_entry_id']);
                        $attendance->update($attendanceData[$i]);
                    }
                    $bug = 0;
                } catch (\Exception $e) {
                    $bug = $e->getMessage();
                    \Log::error($e);
                }
            }
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Employee overtime approved.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function getEmployeeWeeklyAttendanceHere($request = false, $department = null, $designation = null, $location = null, $dateFrom = null, $dateTo = null)
    {
        if ($request && is_object($request) && isset($request->date)) {
            $data = dateConvertFormtoDB($request->date);
        } elseif (is_string($request)) {
            // Handle case where date is passed as string (backward compatibility)
            $data = dateConvertFormtoDB($request);
        } else {
            $data = date("Y-m-d");
        }

        // Use date range if provided, otherwise use weekly dates
        if ($dateFrom && $dateTo) {
            $week_dates = [];
            $currentDate = \Carbon\Carbon::parse($dateFrom);
            $endDate = \Carbon\Carbon::parse($dateTo);

            while ($currentDate->lte($endDate)) {
                $week_dates[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }
        } else {
            $week_dates = $this->weekDates($data);
            // Use week date range for holiday lookup when no specific dates provided
            $dateFrom = $week_dates[0] ?? null;
            $dateTo = $week_dates[count($week_dates) - 1] ?? null;
        }

        $weekly_data = [];
        $attendance1 = [];
        $allHolidays = $this->attendanceRepository->getNonWorkingDays($dateFrom, $dateTo);

        foreach ($allHolidays as $weekday) {
            $date = $weekday['date'];

            $employeeIds = Employee::where('supervisor_id', session('logged_session_data.employee_id'))
                ->pluck('employee_id')
                ->toArray();
            $query = Attendance::with(['employee.designation', 'employee.department', 'overtimeApproval'])
                ->where('date', $date)
                ->whereIn('employee_id', $employeeIds)
                ->where('over_time', '>', 0); // Only overtime > 1 hour as per previous requirement



            // Apply department filter
            if ($department) {
                $query->whereHas('employee', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            }

            // Apply designation filter
            if ($designation) {
                $query->whereHas('employee', function ($q) use ($designation) {
                    $q->where('designation_id', $designation);
                });
            }

            // Apply location filter
            if ($location) {
                $query->whereHas('employee', function ($q) use ($location) {
                    $q->where('location_id', $location);
                });
            }

            $queryResults = $query->get();
            $str_date = strtotime($date);
            $dtToUse = date("l", $str_date) . " (" . date("d", $str_date) . date("S", $str_date) . ")";

            foreach ($queryResults as $value) {
                $weekly_data["attendance"][$value->employee->first_name . ' ' . $value->employee->last_name . "/" . $value->employee->payroll_number][$dtToUse] = $value;
                $weekly_data['holiday_type'] = $weekday['type'];
                //skip those not supervised by the logged in user
                if ($value->employee->supervisor_id == Auth::user()->employeeDetails->employee_id) {
                    $attendance1[] = $value;
                }
            }
            $weekly_data["week_days"][] = $dtToUse;
        }
        return ['data' => $weekly_data, 'attendanceData1' => $attendance1];
    }

    public function weekDates($date)
    {
        $the_date = new DateTime($date);
        $year = $the_date->format("Y");
        $week = $the_date->format("W");
        $dto = new DateTime();
        $week_dates = [];
        $dto->setISODate($year, $week);
        $week_dates[] = $dto->format('Y-m-d');
        for ($i = 0; $i < 6; $i++) {
            $dto->modify("+1 days");
            $week_dates[] = $dto->format("Y-m-d");
        }
        /*$dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');*/

        return $week_dates;
    }

    public function calculateOvertimeHours($employeeId, $fromDate, $toDate)
    {
        // Get all dates in the period
        $allDates = [];
        $currentDate = strtotime($fromDate);
        $endDate = strtotime($toDate);

        while ($currentDate <= $endDate) {
            $allDates[] = date('Y-m-d', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }

        // Classify dates
        $holidays = DB::table('holiday_details')
            ->where('from_date', '>=', $fromDate)
            ->where('to_date', '<=', $toDate)
            ->get()
            ->pluck('from_date')
            ->toArray();

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayNames = array_column($weeklyHolidays, 'day_name');

        $overtimeSummary = [
            'weekday' => 0,
            'weekend' => 0,
            'holiday' => 0,
            'employeeID' => $employeeId,
            'dates' => [], // Changed to array to store all dates with overtime
            'details' => [], // Added to store detailed breakdown
            'total_hours_worked' => 0, // New field for total hours worked
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'date' => null,
        ];

        foreach ($allDates as $date) {
            $dayName = date('l', strtotime($date));

            if (in_array($date, $holidays)) {
                $type = 'holiday';
            } elseif (in_array($dayName, $weeklyHolidayNames)) {
                $type = 'weekend';
            } else {
                $type = 'weekday';
            }

            // Get attendance for this employee on this date
            $attendance = Attendance::where('employee_id', $employeeId)
                ->where('date', $date)
                ->first();


            if ($attendance && $attendance->approved_over_time > 0) {
                $overtimeSummary[$type] += $attendance->approved_over_time;
                $overtimeSummary['total_hours_worked'] += $attendance->hours_worked ?? 0;
                $overtimeSummary['date'] = $date;
                // Store detailed breakdown
                $overtimeSummary['details'][] = [
                    'date' => $date,
                    'type' => $type,
                    'overtime_hours' => $attendance->approved_over_time,
                    'hours_worked' => $attendance->hours_worked ?? 0,
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'attendance_id' => $attendance->id
                ];

                // Track all dates with overtime
                $overtimeSummary['dates'][] = $date;
            }
        }

        return $overtimeSummary;
    }

    public function countOvertimeDays($employeeId, $fromDate, $toDate)
    {
        // Get all attendance records for the employee in date range
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        // Get all holidays in the period (including overlapping ones)
        $holidays = DB::table('holiday_details')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('from_date', [$fromDate, $toDate])
                    ->orWhereBetween('to_date', [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->where('from_date', '<', $fromDate)
                            ->where('to_date', '>', $toDate);
                    });
            })
            ->get();

        // Create array of all holiday dates
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $current = Carbon::parse($holiday->from_date);
            $end = Carbon::parse($holiday->to_date);

            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                if ($dateStr >= $fromDate && $dateStr <= $toDate) {
                    $holidayDates[$dateStr] = true;
                }
                $current->addDay();
            }
        }

        // Get weekly holidays configuration
        $weeklyHolidays = DB::table('weekly_holiday')
            ->pluck('day_name')
            ->toArray();

        // Initialize counters
        $weekdays = 0;
        $weekends = 0;
        $holidays = 0;
        $uniqueDates = [];

        foreach ($attendances as $attendance) {
            $date = $attendance->date->format('Y-m-d');

            // Skip if we've already counted this date
            if (in_array($date, $uniqueDates)) {
                continue;
            }

            $uniqueDates[] = $date;
            $dayName = $attendance->date->format('l');

            if (isset($holidayDates[$date])) {
                $holidays++;
            } elseif (in_array($dayName, $weeklyHolidays)) {
                $weekends++;
            } else {
                $weekdays++;
            }
        }

        return [
            'employee_id' => $employeeId,
            'period_start' => $fromDate,
            'period_end' => $toDate,
            'summary' => [
                'weekdays_worked' => $weekdays,
                'weekends_worked' => $weekends,
                'holidays_worked' => $holidays,
                'total_days_worked' => count($uniqueDates),
            ],
            'unique_dates_worked' => $uniqueDates
        ];
    }

    public function updateOvertimesToPayroll(Request $request)
    {

        // Get dates from Payroll Period
        $payrollPeriod = PayrollPeriod::getCurrentPeriod();

        $startDate = $payrollPeriod->input_period_start;
        $endDate = $payrollPeriod->input_period_end;

        $overtimeData = [];
        $activeEmployees = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('attendance', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->pluck('employee_id')
            ->toArray();

        foreach ($activeEmployees as $employeeId) {


            $overtimeData[] =  $this->countOvertimeDays($employeeId, $startDate, $endDate);
        }

        // Now process the overtimeData to update payroll records
        foreach ($overtimeData as $days) {

            $this->updateEmployeeOvertime($employeeId, $days, $payrollPeriod);
        }

        return redirect()->back()->with('success', 'Overtime records updated successfully.');
    }

    protected function getDateType($date)
    {
        $dayName = date('l', strtotime($date));
        $isHoliday = DB::table('holiday_details')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->exists();

        if ($isHoliday) {
            return 'holiday';
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayNames = array_column($weeklyHolidays, 'day_name');

        if (in_array($dayName, $weeklyHolidayNames)) {
            return 'weekend';
        }

        return 'weekday';
    }

    protected function updateEmployeeOvertime($employeeId, $days, $payrollPeriod)
    {

        // Get or create overtime record for this employee and period
        $employeePayroll = EmployeePayroll::find($employeeId);
        $overtime = EmployeeOvertime::firstOrNew([
            'employee_id' => $employeeId,
            'month_year' => date('Y-m', strtotime($payrollPeriod->input_period_end)),
            'payroll_period_id' => $payrollPeriod->id,
            'payroll_month' => date('m', strtotime($payrollPeriod->input_period_end))
        ]);


        // $overtime->weekday_total = $days['summary']['weekdays_worked'];
        $overtime->weekend_days_totals = $days['summary']['weekends_worked'];
        $overtime->public_holiday_days_totals = $days['summary']['holidays_worked'];

        // Calculate totals

        // Save the record
        $overtime->save();
    }
}

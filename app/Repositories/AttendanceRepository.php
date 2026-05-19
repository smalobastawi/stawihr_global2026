<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;

use App\Lib\Enumerations\LeaveStatus;

use App\Lib\Enumerations\UserStatus;

use App\Models\Advances;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveApplication;

use App\Models\Employee;
use DateTime;
use DateInterval;

class AttendanceRepository
{

    public function getEmployeeDailyAttendance($fromDate = false, $toDate = false, $department_id = false)
    {
        // Set default dates if not provided
        if (!$fromDate) {
            $fromDate = date("Y-m-d 00:00:00"); // Start of today
        } else {
            $fromDate = dateConvertFormtoDB($fromDate) . ' 00:00:00';
        }

        if (!$toDate) {
            $toDate = date("Y-m-d 23:59:59"); // End of today
        } else {
            $toDate = dateConvertFormtoDB($toDate) . ' 23:59:59';
        }

        // Base query with datetime range
        $query = Attendance::with(['employee' => function ($q) {
            $q->with('branch')->with('department')->with('designation');
        }])
            ->whereBetween('time_in', [$fromDate, $toDate]);

        // Apply department filter if provided
        if ($department_id) {
            $query->where('department_id', $department_id);
        }


        $queryResults1 = $query->get();

        //
        $results = [];
        $results1 = [];

        foreach ($queryResults1 as $value) {
            // Safely access nested properties with null coalescing operator
            $departmentName = $value->employee->department->department_name ?? 'Unknown Department';
            $designationName = $value->employee->designation->designation_name ?? 'Unknown Designation';
            $branchName = $value->employee->location->location_name ?? 'Unknown location';
            $gender = $value->employee->gender ?? 'Unknown Gender';
            $presenceStatus = $value->presence_status ?? 'Unknown Status';

            // Group data by department
            $results1["department"][$departmentName][] = $value->groupBy('department_id');

            // Group data by designation
            $results1["designation"][$designationName][] = $value->groupBy('designation_id');

            // Group data by branch, gender, and presence status
            $results1["branch"][$branchName][$gender][$presenceStatus][] = $value;

            // Group data by branch
            $results1["branch_data"][$branchName][] = $value;

            // Group data by branch and gender
            $results1["branch_gender"][$branchName][$gender][] = $value;

            // Group data by presence status
            $results1["presence_data"][$presenceStatus][] = $value;
        }
        $results1["total_data"] = count($queryResults1);
        return $results1;
    }
    public function getEmployeeDailyAttendanceTable($fromDate = false, $toDate = false, $department_id = false, $location_id = false)
    {
        // Set default dates if not provided
        if (!$fromDate) {
            $fromDate = date("Y-m-d 00:00:00"); // Start of today
        } else {
            $fromDate = dateConvertFormtoDB($fromDate) . ' 00:00:00';
        }

        if (!$toDate) {
            $toDate = date("Y-m-d 23:59:59"); // End of today
        } else {
            $toDate = dateConvertFormtoDB($toDate) . ' 23:59:59';
        }
        $query = Attendance::with(['employee' => function ($q) {
            $q->with('branch')->with('department')->with('designation');
        }])
            ->whereBetween('time_in', [$fromDate, $toDate]);

        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        if ($location_id) {
            $query->whereHas('employee', function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            });
        }
        $queryResults1 = $query->orderBy('date', 'DESC')->get();
        return $queryResults1;
    }

    public function getEmployeeWeeklyAttendance($date = false)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        $week_dates = $this->weekDates($data);
        $weekly_data = [];

        foreach ($week_dates as $weekday) {
            $queryResults = Attendance::with('employee')->where('date', $weekday)->get();
            $str_date = strtotime($weekday);
            $dtToUse = date("l", $str_date) . " (" . date("d", $str_date) . date("S", $str_date) . ")";

            foreach ($queryResults as $value) {
                $weekly_data["attendance"][$value->employee->first_name . ' ' . $value->employee->last_name . "/" . $value->employee->national_id][$dtToUse] = $value;
            }
            $weekly_data["week_days"][] = $dtToUse;
        }

        return $weekly_data;

        foreach ($week_dates as $wkdt) {

            $queryResults = DB::select("call `SP_DailyAttendance`('" . $wkdt . "')");
            $str_date = strtotime($wkdt);
            $dtToUse = date("l", $str_date) . " (" . date("d", $str_date) . date("S", $str_date) . ")";

            foreach ($queryResults as $value) {

                $weekly_data["attendance"][$value->fullName . "/" . $value->finger_print_id][$dtToUse] = $value;
            }
            $weekly_data["week_days"][] = $dtToUse;
        }
        return $weekly_data;
    }

    public function getEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {

        $monthlyAttendanceData = DB::select("CALL `SP_monthlyAttendance`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
        $workingDates = $this->number_of_working_days_date($from_date, $to_date);
        $employeeLeaveRecords = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id);


        $holidaysWorked = 0;
        foreach ($monthlyAttendanceData as $monthlyAttendanceDatas) {

            if (!in_array($monthlyAttendanceDatas->date, $workingDates)) {

                $holidaysWorked += 1;
            }
        }
        $dataFormat = [];
        $tempArray = [];
        $tempArray['holidaysWorked'] = $holidaysWorked;

        if ($workingDates && $monthlyAttendanceData) {
            foreach ($workingDates as $data) {
                $flag = 0;
                foreach ($monthlyAttendanceData as $value) {
                    if ($data == $value->date) {
                        $flag = 1;
                        break;
                    }
                }

                $breaktimes = new \DateTime('1:20');
                $workingHoursBeforeBreak = new \DateTime($value->workingHour);
                $fullDayWorkingHours = $workingHoursBeforeBreak->diff($breaktimes)->format('%H:%I:%S');

                $dailyHoursWorkedBeforeBreak = new \DateTime($value->working_time);
                $dailyHoursWorked = $dailyHoursWorkedBeforeBreak->diff($breaktimes)->format('%H:%I:%S');

                if ($flag == 0) {
                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName;
                    $tempArray['department_name'] = $value->department_name;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date'] = $data;
                    $tempArray['working_time'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['lateCountTime'] = '';
                    $tempArray['ifLate'] = '';
                    $tempArray['totalLateTime'] = '';
                    $tempArray['workingHours'] = '';
                    if (in_array($data, $employeeLeaveRecords)) {
                        $tempArray['action'] = 'Leave';
                    } else {
                        $tempArray['action'] = 'Absence';
                    }
                    $dataFormat[] = $tempArray;
                } else {
                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName;
                    $tempArray['department_name'] = $value->department_name;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date'] = $value->date;
                    $tempArray['working_time'] = $dailyHoursWorked;
                    $tempArray['in_time'] = $value->in_time;
                    $tempArray['out_time'] = $value->out_time;
                    $tempArray['lateCountTime'] = $value->lateCountTime;
                    $tempArray['ifLate'] = $value->ifLate;
                    $tempArray['totalLateTime'] = $value->totalLateTime;
                    $tempArray['workingHours'] = $fullDayWorkingHours;
                    $tempArray['action'] = '';
                    $dataFormat[] = $tempArray;
                }
            }
        }

        return $dataFormat;
    }
    public function newGetEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {

        $workingDates = $this->number_of_working_days_date($from_date, $to_date);
        $employeeLeaveRecords = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id);
        $monthlyAttendanceData = Attendance::with('employee')->whereBetween('date', [$from_date, $to_date])->with('department')->where('employee_id', $employee_id)->orderBy('date', 'asc')->groupBy('date')->get();

        $datetime1 = new DateTime($from_date);
        $datetime2 = new DateTime($to_date);
        $interval = $datetime1->diff($datetime2)->format('%a');

        $totalDaysInMonth = (int)$interval + 1;

        $holidaysWorked = 0;
        foreach ($monthlyAttendanceData as $monthlyAttendanceDatas) {

            if (!in_array($monthlyAttendanceDatas->date, $workingDates)) {

                $holidaysWorked += 1;
            }
        }

        $dataFormat = [];
        $tempArray = [];
        $tempArray['holidaysWorked'] = $holidaysWorked;
        $tempArray['totalDaysInMonth'] = $totalDaysInMonth;

        if ($monthlyAttendanceData) {
            foreach ($monthlyAttendanceData as $value) {
                $flag = 1;
                $totalWorkingHour = Carbon::parse($value->time_in)->diffInHours(\Carbon\Carbon::parse($value->time_out));

                //$totalWorkingHour= Carbon::createFromFormat( 'H', $totalWorkingHour, 'Africa/Nairobi')->format('H:i');
                $totalWorkingHour = Carbon::now()->setTime($totalWorkingHour, 0)->format('H:i');

                $breaktimes = new \DateTime('1:20');
                $workingHoursBeforeBreak = new \DateTime(strval($totalWorkingHour));
                $fullDayWorkingHours = $workingHoursBeforeBreak->diff($breaktimes)->format('%H:%I:%S');
                $clockOut = \Carbon\Carbon::parse($value->time_out);
                $clockIn = \Carbon\Carbon::parse($value->time_in);

                $totalDuration1 = $clockOut->diffInSeconds($clockIn);

                $dailyHoursWorkedBeforeBreak1 = gmdate('H:i', $totalDuration1);
                $dailyHoursWorkedBeforeBreak = new \DateTime($dailyHoursWorkedBeforeBreak1);
                $dailyHoursWorked = $dailyHoursWorkedBeforeBreak->diff($breaktimes)->format('%H:%I:%S');

                if ($flag == 0) {
                    $tempArray['employee_id'] = $value->employee->employee_id;
                    $tempArray['fullName'] = $value->employee->first_name . '' . $value->employee->last_name;
                    $tempArray['department_name'] = $value->department->department_name;
                    $tempArray['national_id'] = $value->national_id;
                    $tempArray['date'] = $value->date;
                    $tempArray['working_time'] = '';
                    $tempArray['time_in'] = '';
                    $tempArray['time_out'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['lateCountTime'] = '';
                    $tempArray['ifLate'] = '';
                    $tempArray['totalLateTime'] = '';
                    $tempArray['workingHours'] = '';
                    $tempArray['presence_status'] = '';
                    if (in_array($value->date, $employeeLeaveRecords)) {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'ABSENT') {
                        $tempArray['action'] = 'Absence';
                    } elseif ($value->presence_status == 'AWP') {
                        $tempArray['action'] = 'Absence';
                    } elseif ($value->presence_status == 'AL') {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'OFF') {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'PL') {
                        $tempArray['action'] = 'Leave';
                    }
                    $dataFormat[] = $tempArray;
                } else {
                    $tempArray['employee_id'] = $value->employee->employee_id;
                    $tempArray['fullName'] = $value->employee->first_name . '' . $value->employee->last_name;
                    $tempArray['department_name'] = $value->department?->department_name;
                    $tempArray['national_id'] = $value->national_id;
                    $tempArray['date'] = $value->date;
                    $tempArray['working_time'] = $dailyHoursWorked;
                    $tempArray['in_time'] = $value->time_in;
                    $tempArray['out_time'] = $value->time_out;
                    $tempArray['lateCountTime'] = $value->lateCountTime;
                    $tempArray['ifLate'] = $value->ifLate;
                    $tempArray['totalLateTime'] = $value->totalLateTime;
                    $tempArray['workingHours'] = $fullDayWorkingHours;
                    $tempArray['action'] = 'PRESENSE';
                    if (in_array($value->date, $employeeLeaveRecords)) {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'ABSENT') {
                        $tempArray['action'] = 'Absence';
                    } elseif ($value->presence_status == 'AWP') {
                        $tempArray['action'] = 'Absence';
                    } elseif ($value->presence_status == 'AL') {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'OFF') {
                        $tempArray['action'] = 'Leave';
                    } elseif ($value->presence_status == 'PL') {
                        $tempArray['action'] = 'Leave';
                    }
                    $tempArray['presence_status'] = $value->presence_status;
                    $dataFormat[] = $tempArray;
                }
            }
        }

        return $dataFormat;
    }

    public function number_of_working_days_date($from_date, $to_date)
    {
        $holidays = DB::select("call SP_getHoliday('" . $from_date . "','" . $to_date . "')");
        //$holidays = DB::table('holiday_details')->select('from_date','to_date')->where('from_date', '>=', $from_date)->where('to_date', '<=', $to_date)->get();
        //dd($data, $holidays);
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                //Changed the -d to 24 to allow generation of payroll before 25(official end-date of the month
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select("call SP_getWeeklyHoliday()");
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {
            //get weekly  holiday name
            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName = date("l", $timestamp);

            if (!in_array(date('Y-m-d', $target), $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                array_push($workingDate, date('Y-m-d', $target));
            }
            /*
            The Commented lines below are the correct ones.
            They were changed to allow generation of payslips before end of the month
            */
            if ($to_date <= date('Y-m-d', $target)) {
                break;
            }
            //            if(date('Y-m-d') <= date('Y-m-d', $target)){
            //                break;
            //            }
            $target += (60 * 60 * 24);
        }

        return $workingDate;
    }


    public function new_number_of_working_days_date($from_date, $to_date)
    {
        //$holidays  = DB::select(DB::raw('call SP_getHoliday("'. $from_date .'","'.$to_date .'")'));

        $holidays = DB::table('holiday_details')->select('from_date', 'to_date')->where('from_date', '>=', $from_date)->where('to_date', '<=', $to_date)->get();
        //dd( $holidays);
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                //Changed the -d to 24 to allow generation of payroll before 25(official end-date of the month
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select("call SP_getWeeklyHoliday()");
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {
            //get weekly  holiday name
            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName = date("l", $timestamp);

            if (!in_array(date('Y-m-d', $target), $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                array_push($workingDate, date('Y-m-d', $target));
            }
            /*
            The Commented lines below are the correct ones.
            They were changed to allow generation of payslips before end of the month
            */
            if ($to_date <= date('Y-m-d', $target)) {
                break;
            }
            //            if(date('Y-m-d') <= date('Y-m-d', $target)){
            //                break;
            //            }
            $target += (60 * 60 * 24);
        }

        return $workingDate;
    }

    public function getEmployeeLeaveRecord($from_date, $to_date, $employee_id)
    {
        $queryResult = LeaveApplication::select('application_from_date', 'application_to_date')
            ->where('status', LeaveStatus::APPROVE)
            ->where('application_from_date', '>=', $from_date)
            ->where('application_to_date', '<=', $to_date)
            ->where('employee_id', $employee_id)
            ->get();
        $leaveRecord = [];
        foreach ($queryResult as $value) {
            $start_date = $value->application_from_date;
            $end_date = $value->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leaveRecord[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }
        return $leaveRecord;
    }

    public function findAttendanceSummaryReport($month)
    {
        $data = findMonthToAllDate($month);
        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'national_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('employee.status', UserStatus::$ACTIVE)->get();

        $start_date = $month . '-26';
        $end_date = date("Y-m-25", strtotime($start_date));
        $firstOfCurrentMonth = date($month . '-01');
        $endOfCurrentMonth = date($month . "-t");

        if (strtotime($start_date) > strtotime($firstOfCurrentMonth)) {
            $start_date = new DateTime($start_date);
            $interval = new DateInterval('P1M');
            $start_date->sub($interval);
            $start_date = $start_date->format('Y-m-d');
        } else {
            $end_date = new DateTime($end_date);
            $interval = new DateInterval('P1M');
            $end_date->add($interval);
            $end_date = $end_date->format('Y-m-d');
            //            dd($start_date, $end_date);
        }

        $attendance = DB::table('attendances')->select('national_id', 'date')->whereBetween('date', [$start_date, $end_date])->get();

        $leave = LeaveApplication::select('application_from_date', 'application_to_date', 'employee_id', 'leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
            ->where('leave_application.status', LeaveStatus::APPROVE)
            ->where('leave_application.application_from_date', '>=', $start_date)
            ->where('leave_application.application_to_date', '<=', $end_date)
            ->get();
        $govtHolidays = DB::select("call SP_getHoliday('" . $start_date . "','" . $end_date . "')");

        $dataFormat = [];
        $tempArray = [];
        foreach ($employees as $employee) {
            foreach ($data as $key => $value) {
                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['national_id'] = $employee->national_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeAttendance($attendance, $employee->national_id, $value['date']);
                if ($hasAttendance) {
                    $ifHoliday = $this->ifHoliday($govtHolidays, $value['date']);
                    if ($ifHoliday) {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['gov_day_worked'] = 'yes';
                        $tempArray['leave_type'] = '';
                    } else {
                        $tempArray['attendance_status'] = 'present';
                        $tempArray['gov_day_worked'] = '';
                        $tempArray['gov_day_worked'] = 'no';
                    }
                } else {
                    $hasLeave = $this->ifEmployeeWasLeave($leave, $employee->employee_id, $value['date']);
                    if ($hasLeave) {
                        $tempArray['attendance_status'] = 'leave';
                        $tempArray['gov_day_worked'] = 'no';
                        $tempArray['leave_type'] = $hasLeave;
                    } else {
                        if ($value['date'] > date("Y-m-d")) {
                            $tempArray['attendance_status'] = '';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                        } else {
                            $ifHoliday = $this->ifHoliday($govtHolidays, $value['date']);
                            if ($ifHoliday) {
                                $tempArray['attendance_status'] = 'holiday';
                                $tempArray['gov_day_worked'] = 'no';
                                $tempArray['leave_type'] = '';
                            } else {
                                $tempArray['attendance_status'] = 'absence';
                                $tempArray['gov_day_worked'] = 'no';
                                $tempArray['leave_type'] = '';
                            }
                        }
                    }
                }

                $dataFormat[$employee->fullName][] = $tempArray;
            }
        }

        return $dataFormat;
    }

    public function hasEmployeeAttendance($attendance, $finger_print_id, $date)
    {
        foreach ($attendance as $key => $val) {
            if (($val->national_id == $finger_print_id && $val->date == $date)) {
                return true;
            }
        }
        return false;
    }

    public function ifEmployeeWasLeave($leave, $employee_id, $date)
    {
        $leaveRecord = [];
        $temp = [];
        foreach ($leave as $value) {
            if ($employee_id == $value->employee_id) {
                $start_date = $value->application_from_date;
                $end_date = $value->application_to_date;
                while (strtotime($start_date) <= strtotime($end_date)) {
                    $temp['employee_id'] = $employee_id;
                    $temp['date'] = $start_date;
                    $temp['leave_type_name'] = $value->leave_type_name;
                    $leaveRecord[] = $temp;
                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                }
            }
        }

        foreach ($leaveRecord as $val) {
            if (($val['employee_id'] == $employee_id && $val['date'] == $date)) {
                return $val['leave_type_name'];
            }
        }

        return false;
    }

    public function ifHoliday($govtHolidays, $date)
    {
        $govt_holidays = [];
        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }

        $weeklyHolidays = DB::select("call SP_getWeeklyHoliday()");
        $timestamp = strtotime($date);
        $dayName = date("l", $timestamp);
        foreach ($weeklyHolidays as $v) {
            if ($v->day_name == $dayName) {
                return true;
            }
        }

        return false;
    }

    public function weekDates($date)
    {
        //        dd($date);
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

    public function getWeeklyReport(Request $request)
    {
        $data = dateConvertFormtoDB($request->get('date'));
        $department = $request->get('department_id');
        $departmentList = Department::get();
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');


        $attendanceData = Employee::select(
            'employee.national_id',
            'employee.department_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.in_time), \'%h:%i %p\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.national_id ) AS inTime'),
            DB::raw('(SELECT absence_description  FROM absentees
                                                            WHERE absentees.date = "' . $data . '" AND absentees.employee_id = employee.national_id ) AS presence'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.out_time), \'%h:%i %p\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.national_id ) AS outTime')
        )
            ->where('employee.department_id', $department)
            ->where('employee.status', 1)
            ->orderBy('employee.first_name', 'ASC')
            ->get();
    }

    public function migrateGetEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {

        $monthlyAttendanceData = DB::select("CALL `SP_monthlyAttendance`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
        $employeeLeaveRecords = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id);


        $dataFormat = [];
        $tempArray = [];

        foreach ($monthlyAttendanceData as $value) {

            $breaktimes = new \DateTime('1:20');
            $workingHoursBeforeBreak = new \DateTime($value->workingHour);
            $fullDayWorkingHours = $workingHoursBeforeBreak->diff($breaktimes)->format('%H:%I:%S');

            $dailyHoursWorkedBeforeBreak = new \DateTime($value->working_time);
            $dailyHoursWorked = $dailyHoursWorkedBeforeBreak->diff($breaktimes)->format('%H:%I:%S');


            $tempArray['employee_id'] = $value->employee_id;
            $tempArray['fullName'] = $value->fullName;
            $tempArray['department_name'] = $value->department_name;
            $tempArray['finger_print_id'] = $value->finger_print_id;
            $tempArray['date'] = $value->date;
            $tempArray['working_time'] = $dailyHoursWorked;
            $tempArray['in_time'] = $value->in_time;
            $tempArray['out_time'] = $value->out_time;
            $tempArray['lateCountTime'] = $value->lateCountTime;
            $tempArray['ifLate'] = $value->ifLate;
            $tempArray['totalLateTime'] = $value->totalLateTime;
            $tempArray['workingHours'] = $fullDayWorkingHours;
            $tempArray['action'] = '';
            if (in_array($value->date, $employeeLeaveRecords)) {
                $tempArray['action'] = 'Leave';
            } elseif ($value->in_time != null) {
                $tempArray['action'] = 'PRESENT';
            } else {
                $tempArray['action'] = 'Absence';
            }
            $dataFormat[] = $tempArray;
        }

        dd($dataFormat);
        return $dataFormat;
    }
    public function getEmployeeMealRecord($date = false, $department_id = false, $employee_type_id = false)
    {
        if ($date) {
            $date1 = dateConvertFormtoDB($date);
        } else {
            $date1 = date("Y-m-d");
        }



        $queryResults1 = Attendance::with(['employee' => function ($q) {
            $q->with('branch')->with('department')->with('designation');
        }])->where('date', $date1)->where('lunch_checkin', '!=', null)->get();
        //check if department is included in the search

        if ($department_id) {
            $queryResults1 = Attendance::with(['employee' => function ($q) {
                $q->with('branch')->with('department')->with('designation');
            }])->where('date', $date1)->where('department_id', $department_id)->where('lunch_checkin', '!=', null)->get();
        }
        if ($employee_type_id) {
            $queryResults1 = Attendance::with(['employee' => function ($q) use ($employee_type_id) {
                $q->with('branch')->with('department')->with('designation');
            }])->where('date', $date1)->where('employee_type', $employee_type_id)->where('lunch_checkin', '!=', null)->get();
        }

        if ($employee_type_id && $department_id) {
            $queryResults1 = Attendance::with(['employee' => function ($q) use ($employee_type_id, $department_id) {
                $q->with('branch')->with('department')->with('designation');
            }])->where('date', $date1)->where('employee_type', $employee_type_id)
                ->where('department_id', $department_id)->where('lunch_checkin', '!=', null)->get();
        }
        //

        // dd($queryResults1);
        $results = [];
        $results1 = [];
        foreach ($queryResults1 as $value) {

            $results1["department"][$value->employee->department->department_name][] = $value->groupBy('department_id');
            $results1["designation"][$value->employee->designation->designation_name][] = $value->groupBy('designation_id');

            $results1["branch"][$value->employee->location->location_name][$value->employee->gender][$value->presence_status][] = $value;
            $results1["branch_data"][$value->employee->location->location_name][] = $value;
            $results1["branch_gender"][$value->employee->location->location_name][$value->gender][] = $value;
            $results1["presence_data"][$value->presence_status][] = $value;
        }
        $results1["total_data"] = count($queryResults1);
        return $results1;
    }

    /**
     * Get all non-working days (holidays) within a date range
     * 
     * @param string $from_date Start date (Y-m-d format)
     * @param string $to_date End date (Y-m-d format)
     * @return array Array of non-working dates
     */
    public function getNonWorkingDays($from_date, $to_date)
    {
        // Return empty array if dates are not provided or invalid
        if (empty($from_date) || empty($to_date)) {
            return [];
        }

        // Get all government holidays within the date range
        $govtHolidays = DB::table('holiday_details')
            ->select('from_date', 'to_date')
            ->where('from_date', '>=', $from_date)
            ->where('to_date', '<=', $to_date)
            ->get();

        // Expand holiday date ranges into individual dates
        $publicHolidays = [];
        foreach ($govtHolidays as $holiday) {
            $start_date = $holiday->from_date;
            $end_date = $holiday->to_date;

            while (strtotime($start_date) <= strtotime($end_date)) {
                $publicHolidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        // Get weekly holidays configuration
        $weeklyHolidays = DB::table('weekly_holiday')
            ->select('day_name')
            ->get()
            ->pluck('day_name')
            ->toArray();

        // Generate all dates in the range and check if they're non-working days
        $nonWorkingDays = [];
        $currentDate = strtotime($from_date);
        $endDate = strtotime($to_date);

        while ($currentDate <= $endDate) {
            $date = date('Y-m-d', $currentDate);
            $dayName = date('l', $currentDate);

            // Check if it's a public holiday or weekly holiday
            if (in_array($date, $publicHolidays)) {
                $nonWorkingDays[] = [
                    'date' => $date,
                    'type' => 'public_holiday',
                    'day_name' => $dayName
                ];
            } elseif (in_array($dayName, $weeklyHolidays)) {
                $nonWorkingDays[] = [
                    'date' => $date,
                    'type' => 'weekend',
                    'day_name' => $dayName
                ];
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        return $nonWorkingDays;
    }
}

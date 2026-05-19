<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Repositories\CommonRepository;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateInterval;

class SystemUpgradeController extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;

    public function __construct(CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;
    }

    public function index()
    {
        return view('admin.migrateAttendanceData');
    }

    public function migrateAttendanceData(Request $request)
    {

        if ($request->year == 2020) {
            $maxValue = 12;
            $minValue = 7;
        } elseif ($request->year == 2021) {
            $maxValue = 12;
            $minValue = 1;
        } else {
            $maxValue = 5;
            $minValue = 1;
        }

        $employeeDetails = Employee::where('employee_id', '!=', 1)->get();
        for ($month = $minValue; $month <= $maxValue; $month++) {
            $month1 = $request->year . '-' . $month;


            foreach ($employeeDetails as $employee_details1) {


                $attendanceData = $this->payrollRepository->migrateGetEmployeeOtmAbsLvLtAndWokDaysForMigrating(
                    $employee_details1->employee_id,
                    $month1
                );


                foreach ($attendanceData as $attendances) {
                    $departmentId = Department::where('department_name', $attendances['department_name'])->first()->department_id;
                    $presenceStatus = '';

                    $working_time = Carbon::parse($attendances['date'] . $attendances['in_time'])->diffInHours(\Carbon\Carbon::parse($attendances['date'] . $attendances['out_time']));

                    if ($attendances['action'] == 'Absence') {
                        $presenceStatus = 'ABSENT';
                        dd($attendances, 'absent1');

                    } elseif ($attendances['in_time'] != null) {
                        $presenceStatus = 'PRESENT';

                    } elseif ($attendances['action'] == 'Leave') {
                        $presenceStatus = 'AL';

                    }
                    if ($attendances['in_time'] == null) {
                        $timeIn = null;
                        $presenceStatus = 'ABSENT';
                        dd($attendances, 'absent2');
                    } else {
                        $timeIn = dateConvertFormtoDB($attendances['date']) . ' ' . date("H:i:s", strtotime($attendances['in_time']));
                    }

                    if ($attendances['out_time'] == null) {
                        $timeOut = null;
                    } else {
                        $timeOut = dateConvertFormtoDB($attendances['date']) . ' ' . date("H:i:s", strtotime($attendances['out_time']));
                    }
                    $late_time = '';
                    $overtime = '';

                    if ($working_time > 9) {
                        $overtime = $working_time - 9;
                    }
                    if ($working_time < 9 and $working_time != 0) {
                        $late_time = 9 - $working_time;
                    }
                    $att_data = [
                        "employee_id" => $employee_details1->employee_id,
                        'fingerprint_id' => $attendances['finger_print_id'],
                        "presence_status" => $presenceStatus,
                        "date" => $attendances['date'],
                        'month' => $month1,
                        "time_in" => $timeIn,
                        "time_out" => $timeOut,
                        'working_time' => $attendances['working_time'],
                        'workingHours' => $attendances['workingHours'],
                        'total_time_worked' => $working_time,
                        'over_time' => $overtime,
                        'late_time' => $late_time,
                        'department_id' => $departmentId,
                        'created_by' => Auth::user()->id,
                    ];


                    $attendance = Attendance::updateOrCreate(
                        [
                            "employee_id" => $employee_details1->employee_id,
                            "date" => $attendances['date'],
                        ],
                        $att_data);
                }
            }
        }

        return dd('Data migration successful');
    }

    public function migrateData2(Request $request)
    {
        $employeeDetails = Employee::where('employee_id', '!=', 1)->get();

        if ($request->year == 2020) {
            $maxValue = 12;
            $minValue = 7;
        } elseif ($request->year == 2021) {
            $maxValue = 12;
            $minValue = 1;
        } else {
            $maxValue = 5;
            $minValue = 1;
        }
        foreach ($employeeDetails as $employee_details1) {

            for ($month = $minValue; $month <= $maxValue; $month++) {
                $month1 = $request->year . '-' . $month;

                $employeeAttendance = $this->payrollRepository->getEmployeeOtmAbsLvLtAndWokDays(
                    $employeeDetails,
                    $employee_details1->employee_id,
                    $month1,
                    50,
                    1,
                    1
                );

                foreach ($employeeAttendance as $attendances) {

                    $working_time = Carbon::parse($attendances['date'] . $attendances['in_time'])->diffInHours(\Carbon\Carbon::parse($attendances['date'] . $attendances['out_time']));

                    if ($attendances['action'] == 'Absence') {
                        $presenceStatus = 'ABSENT';

                    } elseif ($attendances['action'] == '') {
                        $presenceStatus = 'PRESENT';

                    } elseif ($attendances['action'] == 'Leave') {
                        $presenceStatus = 'AL';
                    }


                    if ($attendances['in_time'] == null) {
                        $timeIn = null;
                    } else {
                        $timeIn = dateConvertFormtoDB($attendances['date']) . ' ' . date("H:i:s", strtotime($attendances['in_time']));
                    }

                    if ($attendances['out_time'] == null) {
                        $timeOut = null;
                    } else {
                        $timeOut = dateConvertFormtoDB($attendances['date']) . ' ' . date("H:i:s", strtotime($attendances['out_time']));
                    }
                    $late_time = '';
                    $overtime = '';

                    if ($working_time > 9) {
                        $overtime = $working_time - 9;
                    }
                    if ($working_time < 9 and $working_time != 0) {
                        $late_time = 9 - $working_time;
                    }

                    $att_data = [
                        "employee_id" => $attendances['employee_id'],
                        'fingerprint_id' => $attendances['finger_print_id'],
                        "presence_status" => $presenceStatus,
                        "date" => $attendances['date'],
                        'month' => $month1,
                        "time_in" => $timeIn,
                        "time_out" => $timeOut,
                        'working_time' => $attendances['working_time'],
                        'workingHours' => $attendances['workingHours'],
                        'total_time_worked' => $working_time,
                        'over_time' => $overtime,
                        'late_time' => $late_time,
                        'department_id' => $employee_details1->department_id,
                        'created_by' => Auth::user()->id,
                    ];


                    $attendance = Attendance::updateOrCreate(
                        [
                            "employee_id" => $employee_details1->employee_id,
                            "date" => $attendances['date'],
                        ],
                        $att_data);
                }

            }
        }

        return 'Data for april migrated';
    }

    public function migrateData3(Request $request)
    {
        if ($request->year == 2020) {
            $maxValue = 12;
            $minValue = 7;
        } elseif ($request->year == 2021) {
            $maxValue = 12;
            $minValue = 1;
        } else {
            $maxValue = 5;
            $minValue = 1;
        }

        for ($month = $minValue; $month <= $maxValue; $month++) {
            $month1 = $request->year . '-' . $month;

            $Datetime = DateTime::createFromFormat('Y-m', $month1);
             $Datetime1 = $Datetime->modify('-1 months');

            $month2 = $Datetime1->format('Y-m');

            $startingDate = $month2.'-26';
            $endingDate = $month1 . '-25';

            //dd($startingDate, $endingDate, $month1);
            $attendanceData11 = Attendance::whereBetween('date', [$startingDate, $endingDate])->get();

            foreach ($attendanceData11 as $attendance)
            {
                $attendance->month =  $month1;
                $attendance->save();
            }
        }
        dd('Update successful');
    }

}

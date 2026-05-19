<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;

use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\WorkShift;
use Cassandra\Time;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Models\Department;

use App\Models\Employee;
use App\Models\IpSetting;
use App\Models\WhiteListedIp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ManualAttendanceController extends Controller
{

    public function manualAttendance()
    {
        $departmentList = Department::get();
        $data = Carbon::now()->format('Y-m-d');
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');

        $attendanceData = Employee::select('employee.payroll_number', 'employee.department_id', 'employee.employee_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.in_time), \'%h:%i %p\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.payroll_number ) AS inTime'),
            DB::raw('(SELECT absence_description  FROM absentees
                                                             WHERE absentees.date = "' . $data . '" AND absentees.employee_id = employee.payroll_number ) AS presence'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.out_time), \'%h:%i %p\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.payroll_number ) AS outTime'))
            ->where('employee.status', 1)
            ->orderBy('employee.first_name', 'ASC')
            ->get();
        $presences = [
            "ABSENT" => "ABSENT",
            "PRESENT" => "PRESENT",
            "OFF" => "OFF",
            "AWP" => "AWP",
            "SICK" => "SICK",
            "AL" => "AL",
            "ML" => "ML",
            "Training" => "Training",
            "PL" => "PL",
            "CL" => "CL",
        ];

        return view('admin.attendance.manualAttendance.index',
            [
                'departmentList' => $departmentList,
                'todayDate' => $data,
                'attendanceData' => $attendanceData,
                'reportingTime' => $reportingTime,
                'leavingTime' => $leavingTime,
                'presences' => $presences,
            ]
        );
    }


    public function filterData(Request $request)
    {
        $data = dateConvertFormtoDB($request->get('date'));
        $department = $request->get('department_id');
        $departmentList = Department::get();
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');

        $attendanceData = Employee::select('employee.payroll_number', 'employee.department_id',
            DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) as fullName'),

            DB::raw('(SELECT DATE_FORMAT(MIN(view_employee_in_out_data.in_time), \'%h:%i %p\')  FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date = "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.payroll_number ) AS inTime'),
            DB::raw('(SELECT absence_description  FROM absentees
                                                            WHERE absentees.date = "' . $data . '" AND absentees.employee_id = employee.payroll_number ) AS presence'),

            DB::raw('(SELECT DATE_FORMAT(MAX(view_employee_in_out_data.out_time), \'%h:%i %p\') FROM view_employee_in_out_data
                                                             WHERE view_employee_in_out_data.date =  "' . $data . '" AND view_employee_in_out_data.finger_print_id = employee.payroll_number ) AS outTime'))
            ->where('employee.department_id', $department)
            ->where('employee.status', 1)
            ->orderBy('employee.first_name', 'ASC')
            ->get();


        $presences = [
            "ABSENT" => "ABSENT",
            "PRESENT" => "PRESENT",
            "OFF" => "OFF",
            "AWP" => "AWP",
            "SICK" => "SICK",
            "AL" => "AL",
            "ML" => "ML",
            "Training" => "Training",
            "PL" => "PL",
            "CL" => "CL",
        ];

        return view('admin.attendance.manualAttendance.index',
            ['departmentList' => $departmentList,
                'attendanceData' => $attendanceData,
                'reportingTime' => $reportingTime,
                'leavingTime' => $leavingTime,
                'todayDate' => $data,
                'presences' => $presences,
            ]);
    }

    // ip attendance

    // get to attendance ip setting page

    public function setupDashboardAttendance()
    {
        $ip_setting = IpSetting::orderBy('updated_at', 'desc')->first();
        $white_listed_ip = WhiteListedIp::all();


        return view('admin.attendance.setting.dashboard_attendance', [
            'ip_setting' => $ip_setting,
            'white_listed_ip' => $white_listed_ip
        ]);
    }

    // post new attendance

    public function postDashboardAttendance(Request $request)
    {

        try {

            DB::beginTransaction();

            $setting = IpSetting::orderBy('id', 'desc')->firstOrNew();

            $setting->status = $request->status;
            $setting->ip_status = $request->ip_status;
            $setting->save();

            if ($request->ip) {

                WhiteListedIp::orderBy('id', 'desc')->delete();
                foreach ($request->ip as $value) {

                    if ($value != '') {

                        $white_listed_ip = new WhiteListedIp;

                        $white_listed_ip->white_listed_ip = $value;

                        $white_listed_ip->save();
                    }

                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Employee Attendance Setting Updated');


        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function deletedups2022()
    {
        $duplicated = \DB::table('absentees')
            ->select(['id', 'employee_id', 'date', \DB::raw('count(*) as occurences')])
            ->groupBy(['employee_id', 'date'])
            ->having('occurences', '>', 1)
            ->get();

        foreach ($duplicated as $duplicate) {
            Absentee::where('id', $duplicate->id)->delete();
        }
        return 'success';
    }
}

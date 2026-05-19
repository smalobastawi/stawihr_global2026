<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Attendance\DateTime;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AttendanceEntryType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\DB;

class ApiAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $this->validate(
                $request,
                [
                    'employee_id' => 'required',
                ]
            );

            $employee_id = $request->employee_id;
            $check = Employee::where('employee_id', $employee_id)->where('status', '=', 1)->count();

            if ($check <= 0) {

                return response()->json(['status' => 400, 'message' => 'Employee Information not found or  employee not registered in payroll software']);
            } else {
                $att_data = [];
                $att_data['employee_id'] = $request->empoyee_id;

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

                if ($request->time_in == null) {
                    $timeIn = null;
                } else {
                    $timeIn = dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->time_in));
                }

                if ($request->time_out == null) {
                    $timeOut = null;
                } else {
                    $timeOut = dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->time_out));
                }

                if ($request->lunch_check_in == null) {
                    $lunch_check_in = null;
                } else {
                    $lunch_check_in = dateConvertFormtoDB($request->date) . ' ' . date("H:i:s", strtotime($request->lunch_check_in));
                }
                $working_time = Carbon::parse($date . $request->time_in)->diffInHours(\Carbon\Carbon::parse($date . $request->time_out));
                $overtime = 0;
                $late_time = '';

                if ($working_time > 10) { // Only calculate overtime if working time is more than 10 hours (9 regular + 1 overtime)
                    $overtime = $working_time - 9;
                }

                if ($working_time < 9) {
                    $late_time = 9 - $working_time;
                }
                $att_data = [
                    "employee_id" => $request->employee_id,
                    "presence_status" => 'PRESENT',
                    "date" => $date,
                    "time_in" => $timeIn,
                    "time_out" => $timeOut,
                    "lunch_checkin" => $lunch_check_in,
                    'working_time' => $working_time,
                    'over_time' => $overtime,
                    'late_time' => $late_time,
                    'department_id' => $request->department_id,
                    'sensor_id' => $request->department_id,
                    'created_by' => 0,
                    'updated_by' => 0,
                    'month' => $month,
                    'entry_type' => AttendanceEntryType::MOBILE_APP,
                ];
                $attendance = Attendance::updateOrCreate(
                    ["employee_id" => $request->employee_id, "date" => $date,],
                    $att_data
                );

                return response()->json(['status' => 200, 'message' => 'Employee attendance updated']);
            }
        } catch (\Exception $e) {

            return $e;
        }
    }

    public static function update_overtimes_for_date($date)
{
    $attendances = Attendance::where('working_time', null)
        ->where('over_time', null)
        ->where('time_in', '!=', null)
        ->where('time_out', '!=', null)
        ->where('date', $date)
        ->get();

    if ($attendances->isEmpty()) {
        return 'success';
    }

    foreach ($attendances as $attendance) {
        $overtime = 0;
        $late_time = 0;
        $isLate = 'no';
        $dateFormatted = Carbon::parse($attendance->date)->format('Y-m-d');
        
        if ($attendance->work_shift_id) {
            $workshift = WorkShift::find($attendance->work_shift_id);
            
           if ($workshift) {
    // Keep as Carbon objects for comparison
    $start_time = $workshift->start_time;  // already a Carbon instance
    $time_in = $attendance->time_in;       // assuming this is a Carbon instance
    
    // Create reporting time using attendance date and workshift time
    $reporting_time = Carbon::parse($attendance->date->format('Y-m-d'))
        ->setTime($start_time->hour, $start_time->minute, $start_time->second);
    
    if ($time_in->greaterThan($reporting_time) && $reporting_time->diffInMinutes($time_in) > 20) {
        $isLate = 'yes';
        $late_time = $reporting_time->diffInMinutes($time_in);
    }
}else {
                // Log error if workshift not found
                self::logWorkshiftError($attendance);
                continue;
            }
        } else {
            // Log error if workshift_id is null
            self::logWorkshiftError($attendance);
            continue;
        }

        $time_in = Carbon::parse($attendance->time_in);
        $time_out = Carbon::parse($attendance->time_out);
        $working_time = $time_in->diffInHours($time_out);

        // Calculate overtime only if working time exceeds 10 hours
        if ($working_time > 8) {
            $overtime = $working_time - 8;
        }

     

        // Only create overtime approval if overtime is more than 1 hour
        if ($overtime > 1) {
            $attendanceMonth = Carbon::parse($attendance->date)->format('m');

            $overTimeApprovalData = [
                'date' => $attendance->date,
                'month' => $attendanceMonth,
                'national_id' => $attendance->national_id,
                'employee_id' => $attendance->employee_id,
                'department_id' => $attendance->department_id,
                'approved_over_time' => 0,
                'time_in' => $attendance->time_in,
                'time_out' => $attendance->time_out,
                'working_time' => $working_time,
                'workingHours' => $attendance->workingHours,
                'total_time_worked' => $attendance->total_time_worked,
                'is_late' => $isLate,
                'late_time' => $late_time,
                'over_time' => $overtime,
                'approval_status' => $attendance->approval_status,
                'presence_status' => $attendance->presence_status,
                'entry_type' => $attendance->entry_type,
                'work_shift_id' => $attendance->work_shift_id,
                'employee_type' => $attendance->employee_type,
                'stage1_approval_status' => 1,
                'stage2_approval_status' => 1,
                'stage3_approval_status' => 1,
                'stage1_approved_by' => 0,
                'stage2_approved_by' => 0,
                'stage3_approved_by' => 0,
                'attendance_entry_id' => $attendance->id,
            ];

            // Create or update overtime approval
            $attendance->overtimeApproval()->updateOrCreate(
                ['attendance_entry_id' => $attendance->id],
                $overTimeApprovalData
            );
        }
           // Update attendance record
        $attendance->update([
            'working_time' => $working_time,
            'over_time' => $overtime,
            'late_time' => $late_time,
            'is_late' => $isLate,
        ]);
    }

    return 'success';
}

private static function logWorkshiftError($attendance)
{
    try {
        DB::table('error_logs')->insert([
            'log_name' => 'Workshift Not Found',
            'description' => 'Workshift Not Found',
            'affected_employee_id' => $attendance->payroll_number,
            'subject' => 'Workshift Not Found',
            'subject_id' => $attendance->payroll_number,
            'causer' => 0,
            'logged_check_time' => now()->format('Y-m-d H:i:s'),
            'date' => Carbon::parse($attendance->date)->format('Y-m-d'),
            'error_type' => 'Workshift Not Found',
            'module' => 'overtime'
        ]);
    } catch (\Exception $e) {
        // Handle exception if needed
    }
}
}

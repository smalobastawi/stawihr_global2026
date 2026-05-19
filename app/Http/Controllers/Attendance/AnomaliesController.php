<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveApplication;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\Employee;
use DateTime;
use Illuminate\Support\Facades\Log;

class AnomaliesController
{


    public function attendanceAnomalies(Request $request)
    {

        $departmentList = Department::get();
        $employeeShifts = WorkShift::get();
        $reportingTime = Carbon::parse("08:00")->format('h:i A');
        $leavingTime = Carbon::parse("17:00")->format('h:i A');
        $lunchTime = Carbon::parse("12:00")->format('h:i A');
        $date = date('Y-m-d');

        $attendanceData4 = Attendance::where('presence_status', '=', 'PRESENT')->where('date', $date)
            ->where(function ($query) {
                $query->where('lunch_checkin', '=', null)
                    ->orWhere('time_in', '=', null)
                    ->orWhere('time_out', '=', null);
            })->get();

        if (!empty($request->all())) {
            $department = $request->get('department_id');
            $workShiftId = $request->get('work_shift_id');
            $date = dateConvertFormtoDB($request->get('date'));
            $attendanceData4 = Attendance::where('presence_status', '=', 'PRESENT')->where('department_id', $department)
                ->where('work_shift_id', $workShiftId)
                ->where('date', $date)
                ->where(function ($query) {
                    $query->where('lunch_checkin', '=', null)
                        ->orWhere('time_in', '=', null)
                        ->orWhere('time_out', '=', null);
                })->get();
        }


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

        return view('admin.attendance.anomalies.index',
            ['departmentList' => $departmentList,
                'attendanceData' => $attendanceData4,
                'reportingTime' => $reportingTime,
                'leavingTime' => $leavingTime,
                'lunchTime' => $lunchTime,
                'todayDate' => $date,
                'presences' => $presences,
                'employeeShifts' => $employeeShifts,
            ]);
    }

    public function storeAnomalies(Request $request)
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
            if ($request->lunchCheckinTime[$key] == null) {
                $lunchCheckIn = null;
            } else {
                $lunchCheckIn = $date . ' ' . date("H:i:s", strtotime($request->lunchCheckinTime[$key]));
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
                "lunch_checkin" => $lunchCheckIn,
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
                $att_data);


            try {

                switch ($att_data['presence_status']) {
                    case('OFF'):

                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = LeaveType::where('leave_type_name', '=', 'AL')->pluck('leave_type_id')->first();
                        $input['number_of_day'] = 1;


                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            \Log::info($e->getMessage());
                        }
                        $msg = 'Post successfully updated.';


                        break;

                    case('AWP'):

                        $msg = 'Attendance successfully updated.';

                        break;
                    case('AL'):

                        //This is for annual leave update
                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = LeaveType::where('leave_type_name', '=', 'AL')->pluck('leave_type_id')->first();
                        $input['number_of_day'] = 1;


                        //continue to save the leave details
                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            \Log::info($e->getMessage());
                        }
                        $msg = 'Post successfully updated.';

                        break;
                    case('ML'):

                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = 5;
                        $input['number_of_day'] = 1;


                        //continue to save the leave details
                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            \Log::info($e->getMessage());
                        }


                        $msg = 'Post successfully updated.';

                        break;
                    case('PL'):

                        //Update leave for paternity leave
                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = LeaveType::where('leave_type_name', '=', 'AL')->pluck('leave_type_id')->first();
                        $input['number_of_day'] = 1;


                        //continue to save the leave details
                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            \Log::info($e->getMessage());
                        }


                        $msg = 'Post successfully updated.';

                        break;
                    case('SICK'):

                        //Update leave for sick leave
                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = LeaveType::where('leave_type_name', '=', 'SICK')->pluck('leave_type_id')->first();
                        $input['number_of_day'] = 1;


                        //continue to save the leave details
                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            \Log::info($e->getMessage());
                        }


                        $msg = 'Post successfully updated.';

                        break;

                    case('Training'):

                        //Update leave for Training leave
                        $input['application_from_date'] = dateConvertFormtoDB($date);
                        $input['application_to_date'] = dateConvertFormtoDB($date);
                        $input['application_date'] = date('Y-m-d');
                        //approval for the mks system
                        $input['ceo_approval_type'] = 2;
                        $input['ceo_approval_date'] = date('Y-m-d');
                        $input['hr_approval'] = 2;
                        $input['approve_by'] = Auth::user()->id;
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['hr_approval_date'] = date('Y-m-d');
                        $input['final_status'] = 2;
                        $input['status'] = 2;
                        $input['approve_date'] = date('Y-m-d');
                        $input['employee_id'] = $employee_id;
                        $input['leave_type_id'] = LeaveType::where('leave_type_name', '=', 'Training')->pluck('leave_type_id')->first();
                        $input['number_of_day'] = 1;


                        //continue to save the leave details
                        try {
                            $leaveApplication = LeaveApplication::updateOrCreate(
                                ['application_from_date' => dateConvertFormtoDB($date), "employee_id" => $employee_id],
                                $input
                            );
                            $leaveApplication->save();
                            $bug = 0;
                        } catch (\Exception $e) {
                            $bug = $e->getMessage();
                            Log::error($e->getMessage());
                        }


                        $msg = 'Post successfully updated.';

                        break;

                    default:
                        $msg = 'Something went wrong.';
                }
            } catch (\Exception $e) {
                DB::rollback();
//            $bug = $e->getMessage();
                $bug = 0;
                $msg_error = $e;
                Log::error($e->getMessage());
            }
        }

        if ($msg_error == 0) {
            return redirect()->route('attendance.anomalies')->with('success', 'Attendance successfully saved.');
        } else {
            return redirect()->route('attendance.anomalies')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ' . $msg_error);
        }

    }


}
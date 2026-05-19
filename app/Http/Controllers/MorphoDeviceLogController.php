<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Http\Requests\StoreMorphoDeviceLogRequest;
use App\Http\Requests\UpdateMorphoDeviceLogRequest;
use App\Models\Attendance;
use App\Models\BiometricRunLog;
use App\Models\Employee;
use App\Models\MorphoDeviceLog;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MorphoDeviceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \App\Http\Requests\StoreMorphoDeviceLogRequest $request
     * @return \Illuminate\Http\Response
     */
    public function stor1e(Request $request)
    {
        //log the transaction time and date
        $apiKey        = env('JAVA_API_KEY');
        $requestApiKey = \Request::header('API_KEY');
        if (! ($requestApiKey && $requestApiKey == $apiKey)) {
            abort(401, 'UnAuthorised');
        }

        //

        $deviceLog             = new BiometricRunLog();
        $deviceLog->time       = Carbon::now()->format('Y-m-d H:i');
        $deviceLog->machine_ip = \Request::ip();
        $deviceLog->save();

        $datas = json_decode(file_get_contents("php://input"));
        foreach ($datas as $data) {

            $timeReceived = $data->year . '-' . $data->month . '-' . $data->day . ' ' . $data->hour . ':' . $data->minute . ':' . $data->second;
            $dateReceived = $data->year . '-' . $data->month . '-' . $data->day;
            $timeSTamp    = Carbon::parse($timeReceived)->format('Y-m-d H:i:s');
            $recordDate   = Carbon::parse($dateReceived)->format('Y-m-d');
            $recordMonth  = Carbon::parse($dateReceived)->format('Y-m');
            $todayDate    = Carbon::now()->format('Y-m-d');

            $devlog                  = MorphoDeviceLog::firstOrNew(['id_no' => $data->id_no, 'hour' => $data->hour, 'second' => $data->second]);
            $devlog->id_no           = $data->id_no;
            $devlog->user_first_name = $data->user_first_name;
            $devlog->user_name       = $data->user_name;
            $devlog->device_id       = $data->device_id;
            $devlog->year            = $data->year;
            $devlog->month           = $data->month;
            $devlog->day             = $data->day;
            $devlog->hour            = $data->hour;
            $devlog->minute          = $data->minute;
            $devlog->second          = $data->second;
            $devlog->save();

            $employee_id     = Employee::where('national_id', $data->id_no)->pluck('employee_id')->first();
            $employee_dept   = Employee::where('national_id', $data->id_no)->pluck('department_id')->first();
            $alreadyAtWork   = Attendance::where('national_id', $data->id_no)->where('date', $recordDate)->pluck('time_in')->first();
            $clockedOut      = Attendance::where('national_id', $data->id_no)->where('date', $recordDate)->pluck('time_out')->first();
            $lunch_checkedin = Attendance::where('national_id', $data->id_no)->where('date', $recordDate)->pluck('lunch_checkin')->first();
            $atWorkToday     = 0;
            $leftWork        = 0;
            $servedLunch     = 0;
            if (! blank($alreadyAtWork)) {
                $atWorkToday = 1;
            }
            if (! blank($clockedOut)) {
                $leftWork = 1;
            }
            if (! blank($lunch_checkedin)) {
                $servedLunch = 1;
            }

            if ($atWorkToday == 0) {
                if ($employee_id != '') {
                    if ($data->device_ip === "192.168.140.87") {
                        $timeIn   = $timeSTamp;
                        $att_data = [
                            "employee_id"     => $employee_id,
                            "presence_status" => 'PRESENT',
                            "date"            => $recordDate,
                            'time_in'         => $timeIn,
                            'department_id'   => $employee_dept,
                            'created_by'      => 1,
                            'updated_by'      => 1,
                            'month'           => $recordMonth,
                            'national_id'     => $data->id_no,
                            'sensor_id'       => $data->device_serial,
                        ];
                        $attendance = Attendance::updateOrCreate(
                            [
                                "employee_id" => $employee_id,
                                "national_id" => $data->id_no,
                                "date"        => $recordDate,
                            ],
                            $att_data
                        );
                    } else {
                        if ($servedLunch == 1) {
                            //do nothing if lunch record exists for the day
                        } else {
                            $timeIn    = $timeSTamp;
                            $att_data1 = [
                                "employee_id"   => $employee_id,
                                //"presence_status" => '',
                                "date"          => $recordDate,
                                'department_id' => $employee_dept,
                                'created_by'    => 1,
                                'month'         => $recordMonth,
                                'lunch_checkin' => $timeIn,
                                'national_id'   => $data->id_no,
                                'sensor_id'     => $data->device_serial,
                            ];
                            $attendance = Attendance::updateOrCreate(
                                [
                                    "employee_id" => $employee_id,
                                    "national_id" => $data->id_no,
                                    "date"        => $recordDate,
                                ],

                                $att_data1
                            );
                        }
                    }
                } else {
                    // do nothing
                }
            } else {

                if ($employee_id != '') {
                    if ($data->device_ip === "192.168.140.87") {
                        $timeReceived   = $data->year . '-' . $data->month . '-' . $data->day . ' ' . $data->hour . ':' . $data->minute . ':' . $data->second;
                        $timeSTamp1     = Carbon::parse($timeReceived)->format('Y-m-d H:i:s');
                        $alreadyAtWork1 = Attendance::where('national_id', $data->id_no)->where('date', $recordDate)->pluck('time_in')->first();

                        if ($leftWork == 1) {
                            //do nothing
                        } else {
                            $timeOut      = $timeSTamp1;
                            $timeIn       = $alreadyAtWork1;
                            $working_time = Carbon::parse($timeOut)->diffInHours(\Carbon\Carbon::parse($timeIn));
                            if ($working_time <= 1) {
                                //do nothing
                            } else {
                                $overtime  = $working_time - 9;
                                $late_time = '';

                                if ($working_time < 9) {
                                    $late_time = 9 - $working_time;
                                }
                                $att_data_clock_out = [
                                    'time_out'     => $timeSTamp1,
                                    'working_time' => $working_time,
                                    'over_time'    => $overtime,
                                    'late_time'    => $late_time,
                                    'updated_by'   => 1,
                                ];
                                $attendance = Attendance::updateOrCreate(
                                    [
                                        "employee_id" => $employee_id,
                                        "national_id" => $data->id_no,
                                        "date"        => $recordDate,
                                    ],
                                    $att_data_clock_out
                                );
                            }
                        }
                    } else {
                        if ($servedLunch == 1) {
                            //do nothing if lunch record exists for the day
                        } else {
                            $lunchCheckin = $timeSTamp;
                            $att_data1    = [
                                "employee_id"   => $employee_id,
                                // "presence_status" => '',
                                "date"          => $recordDate,
                                'department_id' => $employee_dept,
                                'created_by'    => 1,
                                'updated_by'    => 1,
                                'month'         => $recordMonth,
                                'lunch_checkin' => $lunchCheckin,
                                'national_id'   => $data->id_no,
                                'sensor_id'     => $data->device_serial,
                            ];

                            $attendance = Attendance::updateOrCreate(
                                [
                                    "employee_id" => $employee_id,
                                    "national_id" => $data->id_no,
                                    "date"        => $recordDate,
                                ],

                                $att_data1
                            );
                        }
                    }
                } else {
                    //do nothing if employee id is null
                }
            }
        }
        return 'success';
    }

    public function store(Request $request)
    {


        $datas = json_decode(file_get_contents("php://input"));
        foreach ($datas as $data) {
            // Convert `punch_time` to Carbon object
            $timestamp   = Carbon::parse($data->punch_time);
            $recordDate  = $timestamp->format('Y-m-d');
            $recordMonth = $timestamp->format('Y-m');
            $employee = Employee::where('national_id', $data->emp_code)->first();
            // Skip the record if no employee is found
            if (!$employee) {
                
                continue; // Skip to the next iteration
            }
            $id_no = $employee->national_id;

            $exists = DB::table('morpho_device_logs')
                ->where('id_no', $id_no)
                ->where('time_logged', $timestamp)
                ->exists();
            if ($exists) {
                continue; // Skip to the next iteration if the record already exists
            }
            $terminalDetails = DB::table('morpho_devices')
                ->where('device_serial', $data->terminal_sn)
                ->first();

            // Save device log to be tested. 
            $devlog = new MorphoDeviceLog();
            $devlog->id_no           = $id_no;
            $devlog->employee_id           = $employee->employee_id;
            $devlog->payroll_number           = $employee->payroll_number;
            $devlog->location_id            = $employee->location_id ?? null;
            $devlog->user_first_name = $data->first_name;
            $devlog->user_name       = $data->first_name . ' ' . $data->last_name;
            $devlog->device_id       = $data->terminal_sn;
            $devlog->year            = $timestamp->year;
            $devlog->month           = $timestamp->month;
            $devlog->day             = $timestamp->day;
            $devlog->hour            = $timestamp->hour;
            $devlog->minute          = $timestamp->minute;
            $devlog->second          = $timestamp->second;
            $devlog->location        = $terminalDetails->device_location;
            $devlog->time_logged        = $timestamp;
            $devlog->date            = $recordDate;
           
            $devlog->save();
       

        }
        // Log the transaction time and date
       

        return response()->json(['status' => 'success']);
    }


}

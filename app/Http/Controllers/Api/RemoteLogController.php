<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMorphoDeviceLogRequest;
use App\Http\Requests\UpdateMorphoDeviceLogRequest;
use App\Lib\Enumerations\AttendanceEntryType;
use App\Models\Attendance;
use App\Models\BiometricRunLog;
use App\Models\Employee;
use App\Models\LunchReport;
use App\Models\MorphoDevice;
use App\Models\MorphoDeviceLog;
use App\Models\WhiteListedIp;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoteLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreMorphoDeviceLogRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $apiKey = env('MORPHO_UPLOAD_KEY');
        $requestApiKey = Request::header('API_KEY');
        if (!($requestApiKey && $requestApiKey == $apiKey)) {
            abort(401, 'UnAuthorised');
        }

        $deviceLog = new BiometricRunLog();
        $deviceLog->time = Carbon::now()->format('Y-m-d H:i');
        $deviceLog->machine_ip = Request::ip();
        $deviceLog->save();

        $datas = json_decode(file_get_contents("php://input"));
        foreach ($datas as $data) {

            $timeReceived = $data->year . '-' . $data->month . '-' . $data->day . ' ' . $data->hour . ':' . $data->minute . ':' . $data->second;
            $dateReceived = $data->year . '-' . $data->month . '-' . $data->day;
            $timeSTamp = Carbon::parse($timeReceived)->format('Y-m-d H:i:s');
            $recordDate = Carbon::parse($dateReceived)->format('Y-m-d');
            $recordMonth = Carbon::parse($dateReceived)->format('Y-m');
            $todayDate = Carbon::now()->format('Y-m-d');

            $devlog = MorphoDeviceLog::firstOrNew(['payroll_number' => $data->payroll_number, 'hour' => $data->hour, 'second' => $data->second]);
            $devlog->id_no = $data->payroll_number;
            $devlog->user_first_name = $data->user_first_name;
            $devlog->user_name = $data->user_name;
            $devlog->device_id = $data->device_id;
            $devlog->year = $data->year;
            $devlog->month = $data->month;
            $devlog->day = $data->day;
            $devlog->hour = $data->hour;
            $devlog->minute = $data->minute;
            $devlog->second = $data->second;
            $devlog->save();

            $employee_id = Employee::where('payroll_number', $data->payroll_number)->pluck('employee_id')->first();
            $employee_dept = Employee::where('payroll_number', $data->payroll_number)->pluck('department_id')->first();
            $alreadyAtWork = Attendance::where('payroll_number', $data->payroll_number)->where('date', $recordDate)->pluck('time_in')->first();
            $clockedOut = Attendance::where('payroll_number', $data->payroll_number)->where('date', $recordDate)->pluck('time_out')->first();
            $lunch_checkedin = Attendance::where('payroll_number', $data->payroll_number)->where('date', $recordDate)->pluck('lunch_checkin')->first();
            $atWorkToday = 0;
            $leftWork = 0;
            $servedLunch = 0;
            if (!blank($alreadyAtWork)) {
                $atWorkToday = 1;
            }
            if (!blank($clockedOut)) {
                $leftWork = 1;
            }
            if (!blank($lunch_checkedin)) {
                $servedLunch = 1;
            }

            if ($atWorkToday == 0) {
                if ($employee_id != '') {
                    if ($data->device_ip === "192.168.140.87") {
                        $timeIn = $timeSTamp;
                        $att_data = [
                            "employee_id" => $employee_id,
                            "presence_status" => 'PRESENT',
                            "date" => $recordDate,
                            'time_in' => $timeIn,
                            'department_id' => $employee_dept,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'month' => $recordMonth,
                            'payroll_number' => $data->payroll_number,
                            'sensor_id' => $data->device_serial,
                        ];
                        $attendance = Attendance::updateOrCreate(
                            [
                                "employee_id" => $employee_id,
                                "payroll_number" => $data->payroll_number,
                                "date" => $recordDate,
                            ],
                            $att_data
                        );
                    } else {
                        if ($servedLunch == 1) {
                            //do nothing if lunch record exists for the day
                        } else {
                            $timeIn = $timeSTamp;
                            $att_data1 = [
                                "employee_id" => $employee_id,
                                //"presence_status" => '',
                                "date" => $recordDate,
                                'department_id' => $employee_dept,
                                'created_by' => 1,
                                'month' => $recordMonth,
                                'lunch_checkin' => $timeIn,
                                'payroll_number' => $data->payroll_number,
                                'sensor_id' => $data->device_serial,
                            ];
                            $attendance = Attendance::updateOrCreate(
                                [
                                    "employee_id" => $employee_id,
                                    "payroll_number" => $data->payroll_number,
                                    "date" => $recordDate,
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
                        $timeReceived = $data->year . '-' . $data->month . '-' . $data->day . ' ' . $data->hour . ':' . $data->minute . ':' . $data->second;
                        $timeSTamp1 = Carbon::parse($timeReceived)->format('Y-m-d H:i:s');
                        $alreadyAtWork1 = Attendance::where('payroll_number', $data->payroll_number)->where('date', $recordDate)->pluck('time_in')->first();

                        if ($leftWork == 1) {
                            //do nothing 
                        } else {
                            $timeOut = $timeSTamp1;
                            $timeIn = $alreadyAtWork1;
                            $working_time = Carbon::parse($timeOut)->diffInHours(\Carbon\Carbon::parse($timeIn));
                            if ($working_time <= 1) {
                                //do nothing
                            } else {
                                $overtime = max(0, $working_time - 9);  // This ensures overtime is never negative
                                $late_time = '';

                                if ($working_time < 9) {
                                    $late_time = 9 - $working_time;  // This will always be positive since we're in the condition where working_time < 9
                                }
                                $att_data_clock_out = [
                                    'time_out' => $timeSTamp1,
                                    'working_time' => $working_time,
                                    'over_time' => $overtime,
                                    'late_time' => $late_time,
                                    'updated_by' => 1,
                                ];
                                $attendance = Attendance::updateOrCreate(
                                    [
                                        "employee_id" => $employee_id,
                                        "payroll_number" => $data->payroll_number,
                                        "date" => $recordDate,
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
                            $att_data1 = [
                                "employee_id" => $employee_id,
                                // "presence_status" => '',
                                "date" => $recordDate,
                                'department_id' => $employee_dept,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'month' => $recordMonth,
                                'lunch_checkin' => $lunchCheckin,
                                'payroll_number' => $data->payroll_number,
                                'sensor_id' => $data->device_serial,
                            ];

                            $attendance = Attendance::updateOrCreate(
                                [
                                    "employee_id" => $employee_id,
                                    "payroll_number" => $data->payroll_number,
                                    "date" => $recordDate,
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

    /**
     * Display the specified resource.
     *
     * @param \App\Models\MorphoDeviceLog $morphoDeviceLog
     * @return \Illuminate\Http\Response
     */
    public function updateAttendanceTable(Request $request)
    {

        //api authorization here
        $sending_ip_address = $request->header('X-First');
        $sending_client_key = $request->header('X-Second');
        //check if the ip or the key is in the allowed list
        $whitelistedIPCheck = WhiteListedIp::where('white_listed_ip', $sending_ip_address)->first();

        if (blank($whitelistedIPCheck) or $sending_client_key != config('app.morpho_upload_key')) {
            $data = [
                'description' => 'client not authorised',
                'subject' => $sending_client_key,
                'affected_employee_id' => $sending_ip_address,
                'subject_id' => $sending_client_key,
                'causer' => $sending_ip_address,
                'logged_check_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'error_type' => 'attendance recording',
                'module' => 'Attendance Management',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            try {
                DB::table('error_logs')->insert($data);
            } catch (\Exception $e) {
                //do Nothing
            }
            abort(401, 'upload key mismatch');
        }

        // Log the single request to db first
        $deviceLog = new BiometricRunLog();
        $deviceLog->time = Carbon::now()->format('Y-m-d H:i');
        $deviceLog->machine_ip = Request::ip();
        $deviceLog->save();
        $datas = $request['data'];
        $testingData = $request['responses'];

        foreach ($datas as $key1 => $data) {

            $timeSTamp = Carbon::parse($data['time'])->format('Y-m-d H:i:s');

            //check if the time has already been recorded in the db then skip the whole loop
            $alreadyRecordedIn = Attendance::where('time_in', $timeSTamp)->first();
            $alreadyRecordedOut = Attendance::where('time_out', $timeSTamp)->first();
            $alreadyRecordedLunch = Attendance::where('lunch_checkin', $timeSTamp)->first();

            if (!blank($alreadyRecordedIn) or !blank($alreadyRecordedOut) or !blank($alreadyRecordedLunch)) {
                //skip this time and continue to the next
                continue;
            }

            $dateReceived = Carbon::parse($data['time'])->format('Y-m-d');

            $timeSTamp1 = Carbon::parse($data['time']);
            $recordDate = Carbon::parse($dateReceived)->format('Y-m-d');
            $recordMonth = Carbon::parse($dateReceived)->format('Y-m');
            $todayDate = Carbon::now()->format('Y-m-d');
            $checkInTime111 = $data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' ' . $data['hour'] . ':' . $data['minute'] . ':' . $data['second'];

            //save the raw log first
            $devlog = MorphoDeviceLog::firstOrNew(['id_no' => $data['payroll_number'], 'hour' => $data['hour'], 'second' => $data['second']]);
            $devlog->payroll_number = $data['payroll_number'];
            $devlog->user_first_name = $data['user_first_name'];
            $devlog->time_logged = $timeSTamp;
            $devlog->location = $data['device_location'];
            $devlog->date = $dateReceived;
            $devlog->user_name = $data['user_name'];
            $devlog->device_id = $data['device_id'];
            $devlog->year = $data['year'];
            $devlog->month = $data['month'];
            $devlog->day = $data['day'];
            $devlog->hour = $data['hour'];
            $devlog->minute = $data['minute'];
            $devlog->second = $data['second'];
            $devlog->save();

            $employee = Employee::with('workShift')->where('payroll_number', $data['payroll_number'])->first();
            //check if employee is null and skip this record
            if ($employee == null) {
                continue;
            }
            $checkInTime = $data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' ' . $data['hour'] . ':' . $data['minute'] . ':' . $data['second'];
            $checkInDate = $data['year'] . '-' . $data['month'] . '-' . $data['day'];

            $shiftStart = Carbon::parse($employee->workShift->start_time)->format($checkInDate . ' H:i:s');
            $shiftEnd = Carbon::parse($employee->workShift->end_time)->format($checkInDate . ' H:i:s');
            $shiftStarted = '';
            $shiftEnd_Pr = $shiftEnd;

            if ($shiftEnd < $shiftStart) {

                $shiftEnd1 = Carbon::createFromFormat('Y-m-d', $checkInDate)->addDays(1);
                $shiftEndDate = $shiftEnd1->format('Y-m-d');

                $shiftEndTime = Carbon::parse($employee->workShift->end_time)->format($shiftEndDate . ' H:i:s');
                $shiftEnd = $shiftEndTime;
            }

            $workShift['workShift'] = $employee->workShift;
            $workShift['shift_start'] = $shiftStart;
            $workShift['shift_end'] = $shiftEnd;
            $shiftStarted = Attendance::where('time_in', '<=', $checkInTime)
                ->where('time_in', '>=', Carbon::createFromFormat('Y-m-d', $checkInDate)->subDays(1))
                ->whereNull('time_out')->where('work_shift_id', $workShift['workShift']->work_shift_id)->where('payroll_number', $data['payroll_number'])->first();

            if (!blank($shiftStarted) && $shiftEnd_Pr < $shiftStart) {
                $shiftStart1 = Carbon::createFromFormat('Y-m-d', $checkInDate)->subDays(1);
                $shiftStart1 = $shiftStarted->date;
                $shiftStartdDate = $shiftStart1->format('Y-m-d');


                $shiftStartTime = Carbon::parse($employee->workShift->start_time)->format($shiftStartdDate . ' H:i:s');
                $shiftStart = $shiftStartTime;
                $workShift['workShift'] = $employee->workShift;
                $workShift['shift_start'] = $shiftStart;
            }

            //Log the error if employee shift is nof found
            $deviceLocation = strtoupper($data['device_location']);
            if ($workShift['workShift'] == '' && str_contains($deviceLocation, 'GATE') == true) {

                $data = [
                    'description' => 'Employee shift not found',
                    'subject' => $data['payroll_number'],
                    'affected_employee_id' => $data['payroll_number'],
                    'subject_id' => $data['payroll_number'],
                    'causer' => 0,
                    'logged_check_time' => $checkInTime,
                    'date' => $data['year'] . '-' . $data['month'] . '-' . $data['day'],
                    'error_type' => 'attendance recording',
                    'module' => 'Attendance Management',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                try {
                    DB::table('error_logs')->insert($data);
                } catch (\Exception $e) {
                    //do Nothing
                } {
                    //skip the loop and move to the next one.
                    continue;
                }
            }
            $startTimeStamp = Carbon::parse($workShift['shift_start'])->subMinute(30)->format($workShift['shift_start']);
            $endTimeStamp = Carbon::parse($workShift['shift_end'])->addMinutes(30)->format($workShift['shift_end']);

            $lateTimeStart = Carbon::parse($workShift['shift_start'])->format($data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' ' . 'H:i:s');
            $lateTimeStart1 = Carbon::parse($lateTimeStart);
            $overTimeStart = Carbon::parse($workShift['shift_end'])->format($data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' ' . 'H:i:s');

            $employee_dept = Employee::where('payroll_number', $data['payroll_number'])->pluck('department_id')->first();
            $atWorkToday = 0;
            $alreadyAtWork = '';
            $atworkYesterday = 0;
            //check if workShift is not empty before adding the minutes
            if ($workShift['workShift'] !== '') {

                $overTimeStart = Carbon::parse($workShift['shift_end'])->format($data['year'] . '-' . $data['month'] . '-' . $data['day'] . ' ' . 'H:i:s');

                $startingTime = Carbon::createFromFormat('Y-m-d H:i:s', $workShift['shift_start'])->subMinute(40)->format('Y-m-d H:i:s');
                $endingTime = Carbon::createFromFormat('Y-m-d H:i:s', $workShift['shift_end'])->addMinute(240)->format('Y-m-d H:i:s');

                $alreadyAtWork = Attendance::whereBetween('time_in', [$startingTime, $endingTime])->where('work_shift_id', $workShift['workShift']->work_shift_id)->where('payroll_number', $data['payroll_number'])->first();
                //dd([$overTimeStart,$startingTime,$endingTime,$alreadyAtWork]);
            } else {
                continue;
            }

            //$clockedOut = Attendance::where('national_id', $data['payroll_number'])->where('date', $recordDate)->where('time_out', $timeSTamp)->first();
            $clockedOut = Attendance::where('payroll_number', $data['payroll_number'])->whereBetween('time_in', [$startingTime, $endingTime])->where('time_out', '!=', null)->where('time_in', '!=', null)->first();
            $lunch_checkedin = Attendance::where('payroll_number', $data['payroll_number'])->where('date', $recordDate)->where('lunch_checkin', $timeSTamp)->first();


            $leftWork = 0;
            $servedLunch = 0;
            if (!blank($alreadyAtWork)) {
                $atWorkToday = 1;
            }
            if (!blank($clockedOut)) {
                $leftWork = 1;
            }
            if (!blank($lunch_checkedin)) {
                $servedLunch = 1;
            }
            if ($atWorkToday == 0) {

                if ($employee->employee_id != '') {
                    //convert the location to uppercase character before proceeding

                    if (str_contains($deviceLocation, 'GATE') !== false) {
                        $timeIn = $timeSTamp;
                        if ($timeSTamp > $lateTimeStart) {
                            $att_data['is_late'] = 'Yes';
                            $att_data['late_time'] = $timeSTamp1->diffInHours($lateTimeStart1);
                        }
                        $att_data = [
                            "employee_id" => $employee->employee_id,
                            "presence_status" => 'PRESENT',
                            "date" => $recordDate,
                            'time_in' => $timeIn,
                            'department_id' => $employee_dept,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'month' => $recordMonth,
                            'payroll_number' => $employee->payroll_number,
                            'national_id' => $employee->national_id,
                            'sensor_id' => $data['device_id'],
                            'employee_type' => $employee->employee_type,
                            'work_shift_id' => $workShift['workShift']->work_shift_id,
                        ];
                        $attendance = Attendance::updateOrCreate(
                            [
                                "employee_id" => $employee->employee_id,
                                "payroll_number" => $data['payroll_number'],
                                "time_in" => $timeIn,
                            ],
                            $att_data
                        );
                    }
                } //else do nothing
            } else {
                if ($employee->employee_id != '') {

                    if (strpos($deviceLocation, 'GATE') !== false) {
                        $timeSTamp1 = Carbon::parse($data['time'])->format('Y-m-d H:i:s');
                        $alreadyAtWork1 = Attendance::whereBetween('time_in', [$startingTime, $endingTime])->where('work_shift_id', $workShift['workShift']->work_shift_id)->where('payroll_number', $data['payroll_number'])->first();

                        if ($leftWork == 1) {
                            //do nothing if they left for home
                            continue;
                        } else {

                            $timeOut = $timeSTamp1;
                            $timeIn = $alreadyAtWork1->time_in;
                            $working_time = Carbon::parse($timeOut)->diffInHours(\Carbon\Carbon::parse($timeIn));

                            if ($working_time >= 1) {
                                // Calculate overtime (only positive values)
                                $overtime = max(0, Carbon::parse($timeOut)->diffInHours(\Carbon\Carbon::parse($overTimeStart)));
                                $late_time = '';

                                // Calculate late time (only when working_time is less than 9 hours)

                                $att_data_clock_out = [
                                    'time_out' => $timeSTamp1,
                                    'working_time' => $working_time,
                                    'over_time' => $overtime,
                                    //'late_time' => $late_time,
                                    'updated_by' => 1,
                                    'employee_type' => $employee->employee_type
                                ];
                                $attendance = Attendance::updateOrCreate(
                                    [
                                        "employee_id" => $employee->employee_id,
                                        "payroll_number" => $data['payroll_number'],
                                        "time_in" => $timeIn,
                                    ],
                                    $att_data_clock_out
                                );
                            } else {
                                //do nothing
                            }
                        }
                    }
                } //else do nothing
            }
        }
        return 'success';
    }

    public function getShift($shifts, $checkinTime)
    {

        $checkInDate = Carbon::parse($checkinTime)->format('Y-m-d');

        $currentWorkShift['workShift'] = '';
        $currentWorkShift['shift_start'] = '';
        $currentWorkShift['shift_end'] = '';

        foreach ($shifts as $workShift) {
            $shiftStart = Carbon::parse($workShift->start_time)->format($checkInDate . ' H:i:s');
            $shiftEnd = Carbon::parse($workShift->end_time)->format($checkInDate . ' H:i:s');

            if ($checkinTime > $shiftStart && $checkinTime < $shiftEnd) {
                $currentWorkShift['workShift'] = $workShift;
                $currentWorkShift['shift_start'] = $shiftStart;
                $currentWorkShift['shift_end'] = $shiftEnd;
                return $currentWorkShift;
            }

            if ($shiftEnd < $shiftStart) {
                $shiftEnd1 = Carbon::createFromFormat('Y-m-d', $checkInDate)->addDays(1);
                $shiftEndDate = $shiftEnd1->format('Y-m-d');

                $shiftEndTime = Carbon::parse($workShift->end_time)->format($shiftEndDate . ' H:i:s');
                $shiftEnd = $shiftEndTime;
            }

            if ($shiftStart < $checkinTime && $shiftEnd > $checkinTime) {
                $currentWorkShift['workShift'] = $workShift;
                $currentWorkShift['shift_start'] = $shiftStart;
                $currentWorkShift['shift_end'] = $shiftEnd;
                return ($currentWorkShift);
            }
        }

        return $currentWorkShift;
    }

    public static function fetchLocalRecordsFor($date)
    {


        $date = Carbon::createFromDate($date)->format('Y-m-d');
        $records = MorphoDeviceLog::with(['employee', 'biometricDevice'])
            ->where('updated_status', 0)
            ->whereDate('date', '=', $date)
            ->orderBy('time_logged', 'ASC')
            ->get();

        $uploadData = [];
        foreach ($records as $key => $record) {

            if (!$record->employee) {

                continue;
            }


            $uploadData[$key]['first_name'] = $record->employee->first_name;
            $uploadData[$key]['national_id'] = $record->id_no;
            $uploadData[$key]['middle_name'] = $record->employee->middle_name;
            $uploadData[$key]['last_name'] = $record->employee->last_name;
            $uploadData[$key]['payroll_number'] = $record->employee->payroll_number;
            $uploadData[$key]['device_location'] = $record->location;
            $uploadData[$key]['time'] = $record->time_logged;
            //
            $uploadData[$key]['user_first_name'] = $record->employee->first_name;
            $uploadData[$key]['user_name'] = $record->employee->first_name . $record->employee->last_name;
            $uploadData[$key]['device_id'] = $record->device_id;
        }

        $dataUpdate = self::updateAttendanceTableLocally($uploadData);
        return $dataUpdate;
    }

    public static function updateAttendanceTableLocally($allData)
    {
        foreach ($allData as $key1 => $data) {
            // Check if employee exists and is active
            $employee = Employee::with('workShift')
                ->where('national_id', $data['national_id'])
                ->first();
            if (!$employee->payroll_number) {
                self::logError($data, 'No payroll number for national ID', 'Checkin recording');
                continue;
            }

            if ($employee == null || $employee->status == 0 || $employee->workShift == null || $employee->workShift->status == 0) {
                if ($employee && $employee->status == 0) {
                    self::logError($data, 'Staff Profile inactive', 'Checkin recording');
                }
                continue;
            }

            // Skip if shift starts at 12:00:00 (adjust as needed)
            if ($employee->workShift->start_time == '12:00:00') {
                continue;
            }

            $timeStamp = $data['time']->format('Y-m-d H:i:s');
            $recordDate = $data['time']->format('Y-m-d');
            $recordMonth = $data['time']->format('Y-m');

            // Check for duplicate logs (within 3 minutes)
            $timeSTamp3MinutesA = Carbon::parse($data['time'])->addMinutes(3)->format('Y-m-d H:i:s');
            $timeSTamp3MinutesB = Carbon::parse($data['time'])->subMinutes(3)->format('Y-m-d H:i:s');

            $recorderAlready_time_in = Attendance::whereBetween('time_in', [$timeSTamp3MinutesB, $timeSTamp3MinutesA])
                ->where('payroll_number', $data['payroll_number'])
                ->first();

            $recorderAlready_time_out = Attendance::whereBetween('time_out', [$timeSTamp3MinutesB, $timeSTamp3MinutesA])
                ->where('payroll_number', $data['payroll_number'])
                ->first();

            if ($recorderAlready_time_in || $recorderAlready_time_out) {
                MorphoDeviceLog::where('time_logged', $timeStamp)
                    ->where('payroll_number', $data['payroll_number'])
                    ->update(['updated_status' => 1]);
                continue;
            }

            // Process shift timing (handles night shifts)
            $shiftStart = Carbon::parse($employee->workShift->start_time)->format($recordDate . ' H:i:s');
            $shiftEnd = Carbon::parse($employee->workShift->end_time)->format($recordDate . ' H:i:s');

            if ($shiftEnd < $shiftStart) {
                $shiftEnd = Carbon::parse($employee->workShift->end_time)
                    ->addDay()
                    ->format('Y-m-d H:i:s');
            }

            // Check for existing attendance record for this date
            $existingAttendance = Attendance::where('payroll_number', $data['payroll_number'])
                ->whereDate('date', $recordDate)
                ->first();
            $existingAttendance = Attendance::where('national_id', $data['national_id'])
                ->whereDate('date', $recordDate)
                ->first();


            if ($existingAttendance) {
                // If record exists but has no time_out, set time_out
                if (is_null($existingAttendance->time_out)) {
                    // Ensure new time_out is after time_in
                    if (Carbon::parse($timeStamp)->gt(Carbon::parse($existingAttendance->time_in))) {
                        $workingMinutes = Carbon::parse($existingAttendance->time_in)->diffInMinutes(Carbon::parse($timeStamp));

                        if ($workingMinutes >= 30) { // Minimum working time
                            $existingAttendance->update([
                                'time_out' => $timeStamp,
                                'updated_by' => 1,
                            ]);

                            // Mark log as processed

                            $loToUpdate = MorphoDeviceLog::where('time_logged', $timeStamp)
                                ->where('payroll_number', $data['payroll_number'])
                                ->first();

                            $loToUpdate->updated_status = 1;
                            $loToUpdate->save();
                        }
                    }
                }
                continue;
            }

            // If no existing record, create new time_in entry
            $att_data = [
                "employee_id" => $employee->employee_id,
                "presence_status" => 'PRESENT',
                "date" => $recordDate,
                "time_in" => $timeStamp,
                "department_id" => $employee->department_id,
                "created_by" => 1,
                "updated_by" => 1,
                "month" => $recordMonth,
                "payroll_number" => $employee->payroll_number,
                "national_id" => $employee->national_id,
                "sensor_id" => $data['device_id'],
                "employee_type" => $employee->employee_type,
                "work_shift_id" => $employee->workShift->work_shift_id,
                "entry_type" => AttendanceEntryType::BIOMETRIC,
            ];

            Attendance::create($att_data);

            // Mark log as processed
            MorphoDeviceLog::where('time_logged', $timeStamp)
                ->where('payroll_number', $data['payroll_number'])
                ->update(['updated_status' => 1]);
        }
        return 'success';
    }

    /**
     * Helper method to log errors.
     */
    private static function logError($data, $description, $errorType)
    {
        $logData = [
            'description' => $description,
            'subject' => $data['payroll_number'],
            'affected_employee_id' => $data['payroll_number'],
            'subject_id' => $data['payroll_number'],
            'causer' => 0,
            'logged_check_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'error_type' => $errorType,
            'module' => 'Biometric Attendance Management',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        try {
            DB::table('error_logs')->insert($logData);
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}

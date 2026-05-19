<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BiometricDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\Employee;
use Illuminate\Http\Request;

class BiometricDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zkDevice = new ZKTeco('192.168.0.100');

        return view('attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function setDeviceTime()
    {
        $zkDevice = new ZKTeco('192.168.0.100');
        $date = Date('Y-m-d H:i:s');
        $zkDevice->connect();
        $zkDevice->setTime($date);
        $zkDevice->serialNumber($date);
        $zkDevice->disconnect();
    }

    public function getRecords()
    {

        $biometricDevices = BiometricDevice::where('device_status', 'active')->get();
        foreach ($biometricDevices as $biometricDevice) {
            $zkDevice = new ZKTeco($biometricDevice->device_ip_address);
            $zkDevice->connect();
            $attendances = $zkDevice->getAttendance();
            $users = $zkDevice->getUser();
            $zkDevice->disconnect();

            foreach ($attendances as $attendance) {
                $timeIn = $attendance['timestamp'];
                $date = dateConvertFormtoDB($attendance['timestamp']);

                $checkedIn = Attendance::where('employee_id', $attendance['id'])->where('date', $date)->pluck('time_in')->first();
                $clockedOut = Attendance::where('employee_id', $attendance['id'])->where('date', $date)->pluck('time_out')->first();

                $atSchoolToday = 0;
                $leftSchool = 0;

                if (!blank($checkedIn)) {
                    $atSchoolToday = 1;
                }
                if (!blank($clockedOut)) {
                    $leftSchool = 1;
                }

                if ($atSchoolToday == 0 && $attendance['type'] == 0) {

                    $employeeDetails = Employee::where('national_id', $attendance['id'])->first();
                    $timeIn1 = date('Y-m-d H:i:s', strtotime($timeIn));
                    $att_data = [
                        "employee_id" => $employeeDetails->id,
                        "presence_status" => 'PRESENT',
                        "date" => $date,
                        "time_in" => $timeIn1,
                        "national_id" => $employeeDetails->national_id,
                        'department_id' => $employeeDetails->department_id,
                        'created_by' => Auth::user()->id,

                    ];

                    $attendance1 = Attendance::updateOrCreate(
                        [
                            "employee_id" => $employeeDetails->id,
                            "date" => $date,
                        ],
                        $att_data);
                } elseif ($leftSchool == 0 && $attendance['type'] == 1) {
                    $employeeDetails = Employee::where('national_id', $attendance['id'])->first();
                    $timeIn1 = date('Y-m-d H:i:s', strtotime($timeIn));
                    $att_data = [
                        "employee_id" => $employeeDetails->id,
                        "presence_status" => 'PRESENT',
                        "date" => $date,
                        "time_out" => $timeIn1,
                        "national_id" => $employeeDetails->national_id,
                        'department_id' => $employeeDetails->department_id,
                        'created_by' => Auth::user()->id,

                    ];

                    $attendance1 = Attendance::updateOrCreate(
                        [
                            "employee_id" => $employeeDetails->id,
                            "date" => $date,
                        ],
                        $att_data);
                }

            }
        }
        return redirect()->back()->with(['success' => 'Records updates successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBiometricDeviceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $alreadyAdded = BiometricDevice::where('device_ip_address', $request->device_ip_address)->get();
        if ($alreadyAdded->count() > 0) {
            return redirect()->route('biometricDevices')->with(['error' => 'Device already added']);
        }
        $zkDevice = 0;
        $deviceSerial = 0;
        $deviceStatus = 0;

        $ch = curl_init($request->device_ip_address);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 0) {

            $zkDevice = new ZKTeco($request->device_ip_address);
            $date = Date('Y-m-d H:i:s');
            $zkDevice->connect();
            $getSerial = $zkDevice->serialNumber();
            $deviceSerial = explode('=', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $getSerial))[1];
            $zkDevice->disconnect();
            if ($deviceSerial) {
                $deviceStatus = 'active';
            } else {
                $deviceStatus = 'offline';
            }
        } else {
            return redirect()->back()->with(['error' => 'Device not on network']);
        }

        $biometricDevice = new BiometricDevice;
        $biometricDevice->device_ip_address = $request->device_ip_address;
        $biometricDevice->device_location = $request->device_location;
        $biometricDevice->device_serial = $deviceSerial;
        $biometricDevice->device_status = $deviceStatus;

        $biometricDevice->save();
        return redirect()->route('biometricDevices')->with(['success' => 'Device added successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\BiometricDevice $biometricDevice 
     */
    public function devices(BiometricDevice $biometricDevice)
    {
        
        $biometricDevice = BiometricDevice::all();
        

        return view('admin.attendance.biodevice.index', ['results' => $biometricDevice]);
    }

    public function createDevices()
    {
        return view('admin.attendance.biodevice.form');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\BiometricDevice $biometricDevice
     * @return \Illuminate\Http\Response
     */
    public function edit($biometricDevice)
    {
        $biometricDevice = BiometricDevice::findOrFail($biometricDevice);

        return view('admin.attendance.biodevice.form', ['editModeData' => $biometricDevice]);

    }

    /**
     * Update the specified resource in storage.
     *

     */
    public function update(Request $request)
    {
//        $zkDevice = 0;
//
//        $deviceStatus = 0;
//        $deviceSerial = 0;
//
//        $ch = curl_init($request->device_ip_address);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $data = curl_exec($ch);
//        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
//
//        if ($httpcode !== 0) {
//            $zkDevice = new ZKTeco($request->device_ip_address);
//            $date = Date('Y-m-d H:i:s');
//            $zkDevice->connect();
//            $getSerial = $zkDevice->serialNumber();
//            $deviceSerial1 = explode('=', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $getSerial))[1];
//            $zkDevice->disconnect();
//            if ($deviceSerial1 !== 0) {
//                $deviceStatus = 'active';
//                $deviceSerial = $deviceSerial1;
//
//            } else {
//                $deviceStatus = 'offline';
//
//            }
//        } else {
//            redirect()->back()->with(['error' => 'Device not on network']);
//        }


        $biometricDevice = BiometricDevice::findOrFail($request->id);
        $data = [
            'device_ip_address' => $request->device_ip_address,
            'device_location' => $request->device_location,
            'device_status' => 0,
            'device_type' => $request->device_type,
        ];

        $biometricDevice->update($data);

        return redirect()->route('biometricDevices')->with(['success' => 'Update successful']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BiometricDevice $biometricDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy($biometricDevice)
    {
        $biometricDevice = BiometricDevice::findOrFail($biometricDevice);
        $biometricDevice->delete();
        // return redirect()->route('biometricDevices');
        return 'success';
    }

    public function updateDeviceStatus($id)
    {
        // Update the device status
        $zkDevice = 0;
        $deviceSerial = 0;
        $deviceStatus = 0;
        $biometricDevice = BiometricDevice::findOrFail($id);


        $ch = curl_init($biometricDevice->device_ip_address);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 0) {
            $zkDevice = new ZKTeco($biometricDevice->device_ip_address);
            $zkDevice->connect();
            $getSerial = $zkDevice->serialNumber();
            $deviceSerial = explode('=', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $getSerial))[1];
            $zkDevice->disconnect();

            if ($deviceSerial) {
                $deviceStatus = 'active';
            } else {
                $deviceStatus = 'offline';
            }
        } else {
            $deviceStatus = 'offline';
            redirect()->back()->with(['error' => 'Device not on network']);
        }

        $data = [
            'device_status' => $deviceStatus,
        ];

        $biometricDevice->update($data);

        // Redirect back to the index page
        return redirect()->route('biometricDevices')->with(['success' => 'Update successful']);

    }

    public function addUser()
    {
        $zkDevice = new ZKTeco('192.168.4.35');
        $zkDevice->connect();

        $users = $zkDevice->setUser();
        $zkDevice->disconnect();
        dd($users);
        return 'User added successfully';
    }

    public function delUser()
    {
        $zkDevice = new ZKTeco('192.168.100.106');
        $zkDevice->connect();

        $users = $zkDevice->clearUsers();
        $users1 = $zkDevice->clearAttendance();
        $zkDevice->disconnect();
        return 'users cleared';
    }
}

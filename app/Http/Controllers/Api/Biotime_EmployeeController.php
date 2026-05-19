<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\EmployeeBiometricStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\BioDevice;
use App\Models\DeviceArea;
use App\Models\DeviceTransaction;
use App\Models\MorphoDeviceLog;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Biotime_EmployeeController extends Controller
{
    private $baseApiUrl, $token;

    public function __construct()
    {
        $this->baseApiUrl = config('app.BIOTIME_API_URL', 'null'); // Default if not set in .env
        $this->token = config('app.BIOTIME_API_TOKEN'); // Extract token
    }

    // Fetch all employees
    public function uploadEmployee()
    {

        $employees = Employee::where('status', GeneralStatus::ACTIVE)->where('biometric_upload_status', EmployeeBiometricStatus::PENDING)->get();

        foreach ($employees as $employee) {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders(
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Token  ' . $this->token,
                ]
            )->post($this->baseApiUrl . '/personnel/api/employees/', [
                "emp_code" => $employee->national_id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                "department" => 1,
                "area" => [2],
                'email' => $employee->email,

            ]);

            if ($response->successful()) {
                $data = $response->json();
                $employee['biometric_user_id'] = $data['id'];
                $employee['biometric_upload_status'] = EmployeeBiometricStatus::UPLOADED;
                $employee['updated_at'] = Carbon::now();
                $employee->save();
            } else {
                Log::error('Failed to upload employee: ' . json_encode($response->json()));
            }
        }
        return response()->json(['message' => 'Employees uploaded successfully'], 200);
    }

    public  function updateBiometricCaptureStatus()
    {



        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Token  ' . $this->token,
            ]
        )->get(
            $this->baseApiUrl . '/personnel/api/employees/',
            [
                'page_size' => 10000,
            ]
        );


        if ($response->successful()) {
            $responseData = $response->json();
            $employees = $responseData['data'] ?? [];

            foreach ($employees as $key => $employee) {

                //check if employee exists in the local database
                $existingEmployee = Employee::where('national_id', $employee['emp_code'])->first();
                if (!$existingEmployee) {
                    continue; // Skip to the next employee if not found
                }

                $biometricCaptureStatus = 0;

                if (
                    $employee['fingerprint'] !== "-" ||
                    $employee['palm'] !== "-" ||
                    $employee['face'] !== "-"
                ) {
                    $biometricCaptureStatus = 1;
                }

                $emp_code = $employee['emp_code'];


                // Check if the employee exists in the local raw logs
                $existingRawLogs  = MorphoDeviceLog::where('id_no', $emp_code)->where('status', GeneralStatus::ACTIVE)->first();
                if ($existingRawLogs) {
                    //IF exists, then assign the biometric capture status as 1
                    $biometricCaptureStatus = 1;
                }

                try {

                    // Update the employee record in the database
                    Employee::where('national_id', $emp_code)->update([
                        'biometric_user_id' => $employee['id'],
                        'biometric_capture_status' => $biometricCaptureStatus,
                        'biometric_upload_status' => EmployeeBiometricStatus::UPLOADED,
                        'updated_at' => Carbon::now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error updating employee biometric status: ' . $e->getMessage());
                }
            }
        } else {
            Log::error('Failed to fetch employees Biometrics: ' . json_encode($response->json()));
        }
        return 'success';
    }
}

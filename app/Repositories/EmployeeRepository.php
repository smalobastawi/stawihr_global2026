<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\BiometricDevice;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Str;


class EmployeeRepository
{
    /**
     * Generate next payroll number based on the last generated number
     *
     * @return string
     */
    public function generatePayrollNumber(): string
    {
        $lastEmployee = Employee::whereNotNull('payroll_number')
            ->where('payroll_number', '!=', '')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee && $lastEmployee->payroll_number) {
            // Try to extract numeric part
            if (preg_match('/(\d+)/', $lastEmployee->payroll_number, $matches)) {
                $lastNumber = intval($matches[1]);
                $nextNumber = $lastNumber + 1;

                // Preserve any prefix
                $prefix = preg_replace('/\d+/', '', $lastEmployee->payroll_number);
                return $prefix . str_pad($nextNumber, strlen($matches[1]), '0', STR_PAD_LEFT);
            }
        }

        // Default starting number if no previous payroll numbers exist
        return 'EMP001';
    }

    public function makeEmployeeAccountDataFormat($data, $action = false)
    {
        //$employeeAccountData['role_id'] = $data['role_id'];
        if ($action != 'update') {
            if (config('app.password_login')) {
                $employeeAccountData['password'] = Hash::make($data['password']);
            } else {
                $employeeAccountData['password'] = Hash::make(Str::random('9'));
            }
        }



        // Trim the email to remove any whitespace
        $email = trim($data['email'] ?? '');

        // Check if the email is empty after trimming
        if (empty($email)) {
            $employeeAccountData['email'] = $data['first_name'] . rand(2, 2) . "@testcompany.com";
        } else {
            $employeeAccountData['email'] = $email;
        }

        // $employeeAccountData['role_id'] = $data['roles'];

        $employeeAccountData['last_name'] = $data['last_name'];
        $employeeAccountData['first_name'] = $data['first_name'];
        if (config('app.password_login')) {
            $employeeAccountData['user_name'] = $data['user_name'];
        } else {
            $employeeAccountData['user_name'] =  $email;
        }
        $employeeAccountData['status'] = $data['status'];
        $employeeAccountData['msisdn'] = $data['phone'];
        $employeeAccountData['created_by'] = Auth::user()->id;
        $employeeAccountData['updated_by'] = Auth::user()->id;

        // Add company_id if provided
        if (isset($data['company_id']) && !empty($data['company_id'])) {
            $employeeAccountData['company_id'] = $data['company_id'];
        }

        return $employeeAccountData;
    }

    public function makeEmployeePersonalInformationDataFormat($data)
    {
        $employeeData['first_name'] = $data['first_name'];
        $employeeData['last_name'] = $data['last_name'];
        $employeeData['middle_name'] = $data['middle_name'];
        $employeeData['national_id'] = $data['national_id'];
        $employeeData['identity_type'] = $data['identity_type'];
        $employeeData['driving_license_number'] = $data['driving_license_number'] ?? null;
        $employeeData['department_id'] = $data['department_id'];
        $employeeData['designation_id'] = $data['designation_id'];
        $employeeData['location_id'] = $data['location_id'];
        $employeeData['supervisor_id'] = $data['supervisor_id'];
        $employeeData['employee_section_id'] = $data['employee_section_id'] ?? null;
        // $employeeAccountData['email']=null;
        // $employeeData['work_shift_id'] = $data['work_shift_id'];
        // $employeeData['pay_grade_id'] = $data['pay_grade_id'];
        // $employeeData['hourly_salaries_id'] = $data['hourly_salaries_id'];
        // Trim the email to remove any whitespace
        $email = trim($data['email'] ?? '');

        // Check if the email is empty after trimming
        if (empty($email)) {
            $employeeData['email'] = $data['first_name'] . rand(2, 2) . "@testcompany.com";
        } else {
            $employeeData['email'] = $email;
        }
        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);
        $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);
        $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);
        $employeeData['marital_status'] = $data['marital_status'];
        $employeeData['address'] = $data['address'];
        if (isset($data['emergency_name']) && !empty($data['emergency_name'])) {
            $employeeData['emergency_name'] = $data['emergency_name'];
        } else {
            $employeeData['emergency_name'] = null;
        }

        if (isset($data['emergency_phone']) && !empty($data['emergency_phone'])) {
            $employeeData['emergency_phone'] = $data['emergency_phone'];
        } else {
            $employeeData['emergency_phone'] = null;
        }

        if (isset($data['emergency_relationship']) && !empty($data['emergency_relationship'])) {
            $employeeData['emergency_relationship'] = $data['emergency_relationship'];
        } else {
            $employeeData['emergency_relationship'] = null;
        }
        // $employeeData['emergency_contacts'] = $data['emergency_contacts'];
        $employeeData['gender'] = $data['gender'];
        $employeeData['religion'] = $data['religion'];
        $employeeData['phone'] = $data['phone'];
        $employeeData['status'] = $data['status'];
        $employeeData['created_by'] = Auth::user()->id;
        $employeeData['updated_by'] = Auth::user()->id;

        $employeeData['KRA_Pin'] = $data['KRA_Pin'];
        $employeeData['NSSF_no'] = $data['NSSF_no'];
        // $employeeData['NHIF_no'] = $data['NHIF_no'];
        $employeeData['shif_number'] = $data['shif_number'];

        // Auto-generate payroll number if not provided
        if (empty($data['payroll_number'])) {
            $employeeData['payroll_number'] = $this->generatePayrollNumber();
        } else {
            $employeeData['payroll_number'] = $data['payroll_number'];
        }

        $employeeData['nssf_rate_type'] = $data['nssf_rate_type'];

        if (isset($data['employee_group_id']) && !empty($data['employee_group_id'])) {
            $employeeData['employee_group_id'] = $data['employee_group_id'];
        } else {
            // Check if employee group with ID 1 exists
            $defaultGroup = \App\Models\EmployeeGroup::find(1);
            if ($defaultGroup) {
                $employeeData['employee_group_id'] = 1;
            } else {
                // Find the first active employee group
                $firstActiveGroup = \App\Models\EmployeeGroup::where('status', 1)->first();
                if ($firstActiveGroup) {
                    $employeeData['employee_group_id'] = $firstActiveGroup->id;
                } else {
                    // Set to null to avoid constraint violation
                    $employeeData['employee_group_id'] = null;
                }
            }
        }

        if (isset($data['employee_section_id']) && !empty($data['employee_section_id'])) {
            $employeeData['employee_section_id'] = $data['employee_section_id'];
        } else {
            $employeeData['employee_section_id'] = null;
        }
        $employeeData['work_shift_id'] = $data['work_shift_id'];
        $employeeData['nationality'] = $data['nationality'] ?? null;
        // Ethnicity removed per client request
        $employeeData['residential_status'] = $data['residential_status'] ?? null;
        $employeeData['personal_email'] = $data['personal_email'] ?? null;

        // Add company_id if provided
        if (isset($data['company_id']) && !empty($data['company_id'])) {
            $employeeData['company_id'] = $data['company_id'];
        }

        return $employeeData;
    }


    public function updatePersonalInformationData($data)
    {
        $employeeData['first_name'] = $data['first_name'];
        $employeeData['middle_name'] = $data['middle_name'];
        $employeeData['last_name'] = $data['last_name'];
        $employeeData['identity_type'] = $data['identity_type'];
        $employeeData['marital_status'] = $data['marital_status'];
        $employeeData['address'] = $data['address'];
        if (isset($data['emergency_name']) && !empty($data['emergency_name'])) {
            $employeeData['emergency_name'] = $data['emergency_name'];
        } else {
            $employeeData['emergency_name'] = null;
        }

        if (isset($data['emergency_phone']) && !empty($data['emergency_phone'])) {
            $employeeData['emergency_phone'] = $data['emergency_phone'];
        } else {
            $employeeData['emergency_phone'] = null;
        }

        if (isset($data['emergency_relationship']) && !empty($data['emergency_relationship'])) {
            $employeeData['emergency_relationship'] = $data['emergency_relationship'];
        } else {
            $employeeData['emergency_relationship'] = null;
        }
        // $employeeData['emergency_contacts'] = $data['emergency_contacts'];
        $employeeData['religion'] = $data['religion'];
        $employeeData['phone'] = $data['phone'];
        $employeeData['nationality'] = $data['nationality'];
        // Ethnicity removed per client request
        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);
        $employeeData['updated_by'] = Auth::user()->id;
        //dd($employeeData);
        return $employeeData;
    }

    public function makeEmployeeEducationDataFormat($data, $employee_id, $action = false)
    {
        $educationData = [];
        if (isset($data['institute'])) {
            for ($i = 0; $i < count($data['institute']); $i++) {
                $educationData[$i] = [
                    'employee_id' => $employee_id,
                    'institute' => $data['institute'][$i],
                    'board_university' => $data['board_university'][$i],
                    'degree' => $data['degree'][$i],
                    'passing_year' => $data['passing_year'][$i],
                    //'result' => $data['result'][$i],
                    'cgpa' => $data['cgpa'][$i],
                ];
                if ($action == 'update') {
                    $educationData[$i]['educationQualification_cid'] = $data['educationQualification_cid'][$i];
                }
            }
        }
        return $educationData;
    }

    public function makeEmployeeDocumentsDataFormat($data, $employee, $action = false)
    {
        $documentData = [];
        if (isset($data['document_name'])) {

            for ($i = 0; $i < count($data['document_name']); $i++) {

                $uuid = Str::uuid();

                $fileName = $data['document_name'][$i] . '_' . $uuid . '.' . $data['document_file'][$i]->getClientOriginalExtension();

                $data['document_file'][$i]->move(public_path('uploads/employeeDocs'), $fileName);
                if (file_exists(public_path('uploads/employeeDocs') . $fileName) and !empty($fileName)) {
                    unlink(public_path('uploads/employeeDocs') . $fileName);
                }

                $documentData[$i] = [
                    'employee_id' => $employee->employee_id,
                    'document_name' => $data['document_name'][$i],
                    'national_id' => $employee->national_id,
                    'date_uploaded' => Carbon::now()->format('Y-m-d'),
                    'document_type' => $data['document_type'][$i],
                    'document_link' => $fileName,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'uuid' => $uuid,
                    'uploaded_by' => Auth::id(),
                ];
                if ($action == 'update') {
                    $documentData[$i]['employeeDocuments_cid'] = $data['employeeDocuments_cid'][$i];
                }
            }
        }

        return $documentData;
    }

    public function makeEmployeeExperienceDataFormat($data, $employee_id, $action = false)
    {
        $experienceData = [];
        if (isset($data['organization_name'])) {
            for ($i = 0; $i < count($data['organization_name']); $i++) {
                $experienceData[$i] = [
                    'employee_id' => $employee_id,
                    'organization_name' => $data['organization_name'][$i],
                    'designation' => $data['designation'][$i],
                    'from_date' => dateConvertFormtoDB($data['from_date'][$i]),
                    'to_date' => dateConvertFormtoDB($data['to_date'][$i]),
                    'responsibility' => $data['responsibility'][$i],
                    'skill' => $data['skill'][$i],
                ];
                if ($action == 'update') {
                    $experienceData[$i]['employeeExperience_cid'] = $data['employeeExperience_cid'][$i];
                }
            }
        }
        return $experienceData;
    }

    /*
     * Added import data processing functions here
     */

    public function makeEmployeeAccountDataFormat_from_excel($data, $action = false)
    {
        $user_role_id = DB::table('role')->where('role_name', 'like', $data['role'])->pluck('role_id')->first();
        $employeeAccountData['role_id'] = $user_role_id;
        if ($action != 'update') {
            $employeeAccountData['password'] = Hash::make(Str::random(8));
        }
        $employeeAccountData['user_name'] = $data['first_name'] . $data['last_name'];
        $employeeAccountData['status'] = $data['status'];
        $employeeAccountData['created_by'] = Auth::user()->id;
        $employeeAccountData['updated_by'] = Auth::user()->id;

        return $employeeAccountData;
    }

    public function makeEmployeePersonalInformationDataFormat_from_excel($data)
    {
        //get department infos
        $department_id = DB::table('department')->where('department_name', 'like', $data['department'])->pluck('department_id')->first();
        $work_shift_id = DB::table('work_shift')->where('shift_name', 'like', $data['work_shift'])->pluck('work_shift_id')->first();
        $job_category_id = DB::table('job_categories')->where('name', 'like', $data['job_category'])->pluck('id')->first();
        $daily_pays_id = DB::table('daily_pays')->where('job_category', 'like', $data['job_category'])->pluck('id')->first();
        $designation_id = DB::table('designation')->where('designation_name', 'like', $data['designation'])->pluck('designation_id')->first();
        $location_id = DB::table('location')->where('location_name', 'like', $data['location'])->pluck('location_id')->first();
        $supervisor_id = DB::table('employee')->whereRaw("CONCAT_WS(' ',`first_name`, `last_name`) = ? ", $data['supervisor'])->pluck('employee_id')->first();

        $employeeData['first_name'] = $data['first_name'];
        $employeeData['last_name'] = $data['last_name'];
        $employeeData['middle_name'] = $data['middle_name'];
        $employeeData['national_id'] = $data['id_no'];
        $employeeData['department_id'] = $department_id;
        $employeeData['designation_id'] = $designation_id;
        $employeeData['location_id'] = $location_id;
        $employeeData['supervisor_id'] = $supervisor_id;
        //        $employeeData['work_shift_id'] = $work_shift_id;
        //        $employeeData['job_categories'] = $job_category_id;
        //        $employeeData['daily_pay'] = $daily_pays_id;
        $employeeData['email'] = $data['email'];
        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);
        $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);
        $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);
        $employeeData['marital_status'] = $data['marital_status'];
        $employeeData['address'] = $data['address'];
        $employeeData['emergency_contacts'] = $data['emergency_contacts'];
        $employeeData['gender'] = $data['gender'];
        $employeeData['religion'] = $data['religion'];
        $employeeData['phone'] = $data['phone'];
        $employeeData['status'] = $data['status'];
        $employeeData['job_category'] = $job_category_id;
        $employeeData['NHIF_no'] = $data['nhif_no'];
        $employeeData['NSSF_no'] = $data['nssf_no'];
        $employeeData['KRA_Pin'] = $data['kra_pin'];
        $employeeData['payroll_number'] = $data['payroll_number'];
        $employeeData['created_by'] = Auth::user()->id;
        $employeeData['updated_by'] = Auth::user()->id;

        return $employeeData;
    }

    public function jobCategoryDataFormatFromExcel($data)
    {
        $jobCategoryData['name'] = $data['job_category'];
        $jobCategoryData['house_allowance'] = $data['house_allowance'];
        $jobCategoryData['transport_allowance'] = $data['transport_allowance'];
        $jobCategoryData['banking_allowance'] = $data['banking_allowance'];
        $jobCategoryData['basic_pay'] = $data['basic_pay'];
        $jobCategoryData['gross_pay'] = $data['gross_pay'];


        return $jobCategoryData;
    }

    public function dailyPayDataFormatFromExcel($data)
    {
        $dailyPayData['job_category'] = $data['job_category'];
        $dailyPayData['house_allowance'] = $data['house_allowance'];
        // $dailyPayData['transport_allowance'] = $data['transport_allowance'];
        //$dailyPayData['banking_allowance'] = $data['banking_allowance'];
        $dailyPayData['basic_pay'] = $data['basic_pay'];
        $dailyPayData['gross_pay'] = $data['gross_pay'];
        return $dailyPayData;
    }

    public static function addToBiometric($details)
    {

        $biometricDevices = BiometricDevice::where('device_status', 'active')->get();
        if ($biometricDevices->count() <= 0) {
            return redirect()->back()->with(['error' => 'No device connection found']);
        }
        foreach ($biometricDevices as $biometricDevice) {
            $zkDevice = new ZKTeco($biometricDevice->device_ip_address);
            $zkDevice->connect();

            $users = $zkDevice->getUser();
            $total = end($users);
            $nextId = 1;

            if ($total !== false) {
                $nextId = $total['uid'] + 1;
            }
            $userAdded = $zkDevice->setUser($nextId, $details['national_id'], $details['name'], '1234', 0, $details['biometric_card_no']);
            $zkDevice->disconnect();
            return 'success';
        }
        return 'success';
    }
}

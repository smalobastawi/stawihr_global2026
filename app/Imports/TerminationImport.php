<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Imports;
use App\Models\Employee;
use App\Models\LeaversAndJoiners;
use App\Models\Termination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class TerminationImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        if (empty($row['payroll_number']) || empty($row['notice_date']) || empty($row['termination_date'])) {
            return null; // Ignore the row if required fields are missing
        }
        $notice_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['notice_date'])->format('m/d/Y');
        $termination_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['termination_date'])->format('m/d/Y');

        $convertedNotice_date =  \Carbon\Carbon::parse($notice_date)->format('Y-m-d');
        $convertedTerminationD_date = \Carbon\Carbon::parse($termination_date)->format('Y-m-d');
        $employeeDetails = $this->getEmployeeDetails($row['payroll_number']);

        if (!$employeeDetails) {
            // Create a base ValidationException
            $validationException = BaseValidationException::withMessages([
                'payroll_number' => ['Employee details not found for payroll number: ' . $row['payroll_number']],
            ]);
        
            // Throw the Maatwebsite Excel ValidationException
            throw new ExcelValidationException($validationException, [
                (object) [
                    'row' => $row['payroll_number'], // Row that failed
                    'attribute' => 'payroll_number', // Field that caused the failure
                    'errors' => ['Employee details not found'], // Error messages
                    'values' => $row, // Row values that caused the error
                ],
            ]);
        }
        $terminationData['terminate_to'] = $employeeDetails->employee_id;
        $terminationData['national_id'] = $row['national_id'];

        $terminationData['terminate_by'] = Auth::user()->id;
        $terminationData['termination_type'] = $row['termination_type'];
        $terminationData['notice_date'] = $convertedNotice_date;
        $terminationData['termination_date'] = $convertedTerminationD_date;
        $terminationData['status'] = 2;
        $terminationData['entry_type'] = 'manual_upload';
        $terminationData['created_by'] = Auth::id();
        


        $leaverData ['employee_id']= $employeeDetails->employee_id;
        $leaverData ['payroll_number']=$employeeDetails->payroll_number;
        $leaverData ['national_id']=$employeeDetails->national_id;
        $leaverData ['first_name']=$employeeDetails->first_name;
        $leaverData ['middle_name']=$employeeDetails->middle_name;
        $leaverData ['last_name']=$employeeDetails->last_name;
        $leaverData ['date_of_movement']=$convertedTerminationD_date;
       // $leaverData ['date_approved']=
        $leaverData ['approval_status']=0;
        $leaverData ['movement_type']='leaving';
        $leaverData['created_by'] = Auth::id();


        //continue to save the details

        try{
            $termination = Termination::updateOrCreate(
                [
                    "termination_date" => $convertedTerminationD_date,
                    "notice_date" => $convertedNotice_date,
                    "terminate_to" =>  $terminationData['terminate_to'],
                ],
                $terminationData);

            //create joiners or leaver

            $newLeaver = LeaversAndJoiners::updateOrCreate([
                "date_of_movement" => $convertedTerminationD_date,
                "employee_id" => $employeeDetails->employee_id],
                $leaverData );
            $bug = 0;
            //return 'success';
            LogBatch::startBatch();
            activity()->performedOn($newLeaver)->log('imported terminations');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $bug = 0;
            $failures = $e->failures();
            \Log::info($failures);
            
            
        }

    }

    public function getEmployeeDetails($payrollNumber)
    {
        $employeeId = Employee::where('payroll_number', $payrollNumber)->where('status', 1)->first();
        return $employeeId;
    }
}

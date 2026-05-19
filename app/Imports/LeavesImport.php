<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Imports;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use App\Models\LeaveApplication;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class LeavesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {if (empty($row['payroll_number']) ) {
        return null; // Ignore the row if required fields are missing
    }
        $fromDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['from_datemmddyyyy'])->format('m/d/Y');
        $toDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['to_datemmddyyyy'])->format('m/d/Y');

        //convert date to d-m-Y before proceeding
        $convertedFromDate =  \Carbon\Carbon::parse($fromDate)->format('d-m-Y');
        $convertedToDate = \Carbon\Carbon::parse($toDate)->format('d-m-Y');
        $leavesData['number_of_day'] = $this->getNumberOfLeaveDays($convertedFromDate, $convertedToDate);

        $employeeDetails = $this->getEmployeeId($row['payroll_number']);
        
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
        $leaveType=$this->getLeaveTypeId($row['leave_type']);
        if (!$leaveType) {
            // Create a base ValidationException
            $validationException = BaseValidationException::withMessages([
                'leave_type' => ['No Leave Type with name : ' . $row['leave_type'].' Set In the System.'],
            ]);
        
            // Throw the Maatwebsite Excel ValidationException
            throw new ExcelValidationException($validationException, [
                (object) [
                    'row' => $row['leave_type'], // Row that failed
                    'attribute' => 'leave_type', // Field that caused the failure
                    'errors' => ['Leave type Not Found'], // Error messages
                    'values' => $row, // Row values that caused the error
                ],
            ]);
        }



        $leavesData['employee_id'] = $this->getEmployeeId($row['payroll_number'])->employee_id;
        $leavesData['leave_type_id'] = $this->getLeaveTypeId($row['leave_type']);

        $leavesData['application_from_date'] = dateConvertFormtoDB($convertedFromDate);

        //dd(\Carbon\Carbon::parse($toDate)->format('j F, Y'), \Carbon\Carbon::parse($toDate)->format('d-m-Y'));
        $leavesData['application_to_date'] = dateConvertFormtoDB($convertedToDate);
        $leavesData['application_date'] = date('Y-m-d');


        //approval for the mks system
        $leavesData['ceo_approval_type'] = 2;
        $leavesData['ceo_approval_date'] = date('Y-m-d');
        $leavesData['hr_approval'] = 2;
        $leavesData['approve_by'] = Auth::user()->id;
        //  $leavesData['hr_approval_date'] = date('Y-m-d');
        $leavesData['hr_approval_date'] = date('Y-m-d');
        $leavesData['final_status'] = 2;
        $leavesData['status'] = 2;
        $leavesData['approve_date'] = date('Y-m-d');
        $leavesData['application_type'] = 'manual_upload';
        $leavesData['purpose'] = $row['purpose'];

        //continue to save the details

        
            $attendance= LeaveApplication::where('application_from_date',dateConvertFormtoDB($convertedFromDate))->
            where('employee_id',$leavesData['employee_id'])->first();
            if(!$attendance){
                LeaveApplication::create($leavesData);
            }
            else{
                $attendance->update($leavesData);
            } 
             //
            $bug = 0;
            //return 'success';
            //activity()->performedOn($attendance)->log('imported leave');
         

    }

    public function getNumberOfLeaveDays($application_from_date, $application_to_date)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $application_from_date . '","' . $application_to_date . '")'));
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($application_from_date);
        $countDay = 0;
        while ($target <= strtotime(date("Y-m-d", strtotime($application_to_date)))) {

            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            //get weekly  holiday name
            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);

            //if not in holidays and not in weekly  holidays
            if (!in_array($value, $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                $countDay++;
            }
        }

        return $countDay;
    }

    public function getEmployeeId($payrollNumber)
    {
        $employeeId = Employee::where('payroll_number', $payrollNumber)->first();
        return $employeeId;
    }

    public function getLeaveTypeId($leave_type_name)
    {
        $employeeId = LeaveType::where('leave_type_name', $leave_type_name)->pluck('leave_type_id')->first();
        return $employeeId;
    }
    public function getLeaveType($leaveName){
        return 1;
    }
}


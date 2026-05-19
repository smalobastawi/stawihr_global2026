<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Imports;

use App\Models\Location;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\EmployeeMovement;
use App\Models\EmployeeSection;
use App\Models\EmployeeType;
use App\Models\LeaversAndJoiners;
use App\Models\Termination;
use App\Models\WorkShift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Activitylog\Facades\LogBatch;

class EmployeeMovementImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        //$notice_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['notice_date'])->format('m/d/Y');
        $movementDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_movement'])->format('m/d/Y');

        $movementDate1 = \Carbon\Carbon::parse($movementDate)->format('Y-m-d');
        $employeeDetails = $this->getEmployeeDetails($row['payroll_number']);

        if ($employeeDetails == null) {
            \Log::info('employee not found');
        } else {
            $movementData['employee_id'] = $employeeDetails->employee_id;
            $movementData['payroll_number'] = $employeeDetails->payroll_number;
            $movementData['current_department'] = $employeeDetails->department_id;
            $movementData['current_designation'] = $employeeDetails->designation_id;
            $movementData['current_section_id'] = $employeeDetails->employee_section_id;
            $movementData['current_group_id'] = $employeeDetails->employee_group_id;
            $movementData['current_work_shift_id'] = $employeeDetails->work_shift_id;
            $movementData['current_branch'] = $employeeDetails->location_id;
            $movementData['current_employee_type'] = $employeeDetails->employee_type;
            $movementData['movement_date'] = $movementDate1;
            //
            $movementData['new_section_id'] = $this->getNewSection($row['new_section']);
            $movementData['new_group_id'] = $this->getNewGroup($row['new_group']);
            $movementData['new_designation_id'] = $this->getNewDesignation($row['new_designation']);
            $movementData['new_department_id'] = $this->getNewDepartment($row['new_department']);
            $movementData['new_work_shift_id'] = $this->getWorkShift($row['new_work_shift']);
            $movementData['new_branch'] = $this->getNewBranch($row['new_branch']);
            $movementData['new_employee_status'] = $row['new_employee_status'];
            $movementData['new_employee_type'] = $this->getNewEmployeeType($row['new_employee_type']);
            // new employeeData

            $updateEmployeeData['department_id'] = $employeeDetails->department_id;
            $updateEmployeeData['designation_id'] = $movementData['new_designation_id'];
            $updateEmployeeData['employee_section_id'] = $movementData['new_section_id'];
            $updateEmployeeData['employee_group_id'] = $movementData['new_group_id'];
            $updateEmployeeData['work_shift_id'] = $movementData['new_work_shift_id'];
            $updateEmployeeData['location_id'] = $movementData['new_branch'];
            $updateEmployeeData['employee_type'] = $movementData['new_employee_type'];
            $updateEmployeeData['status'] =   $movementData['new_employee_status'];
            try {
                $movement = EmployeeMovement::updateOrCreate(
                    [
                        "movement_date" => $movementDate1,
                        "employee_id" => $employeeDetails->employee_id,
                    ],
                    $movementData
                );
                $bug = 0;

                //update employee details in db
                $updateEmployee = Employee::findOrFail($employeeDetails->employee_id);
                $updateEmployee->update($updateEmployeeData);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $bug = 0;
                $failures = $e->failures();
                \Log::info($failures);
            }
        }
    }

    public function getEmployeeDetails($payrollNumber)
    {
        $employeeDetails = Employee::with(['department', 'designation', 'branch', 'employeeType', 'employeeSection', 'employeeGroup'])->where('payroll_number', $payrollNumber)->where('status', 1)->first();
        return $employeeDetails;
    }

    public function getNewSection($data)
    {
        $sectionId = EmployeeSection::where('name', $data)->pluck('id')->first();
        return $sectionId;
    }

    public function getNewGroup($data)
    {
        $groupId = EmployeeGroup::where('name', $data)->pluck('id')->first();
        return $groupId;
    }

    public function getNewEmployeeType($data)
    {
        $employeeId = EmployeeType::where('name', $data)->pluck('id')->first();
        return $employeeId;
    }

    public function getWorkShift($data)
    {
        $workShiftId = WorkShift::where('shift_name', $data)->pluck('work_shift_id')->first();
        return $workShiftId;
    }

    public function getNewBranch($data)
    {
        $branchId = Location::where('branch_name', $data)->pluck('location_id')->first();
        return $branchId;
    }

    public function getNewDepartment($data)
    {
        $branchId = Department::where('department_name', $data)->pluck('department_id')->first();
        return $branchId;
    }
    public function getNewDesignation($data)
    {
        $branchId = Designation::where('designation_name', $data)->pluck('designation_id')->first();
        return $branchId;
    }
}

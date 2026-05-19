<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupervisorsImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        $employeeDataFormat = $this->makeEmployeePersonalInformation($row);
        $newEmployee = Employee::where('payroll_number', $row['payroll_number'])->first();
        if($newEmployee)
        {
            $newEmployee1 = Employee::updateOrCreate(['payroll_number' => $row['payroll_number']], $employeeDataFormat);

        }

    }

    public
    function makeEmployeePersonalInformation($data)
    {
        $employeeData['updated_at'] = date('Y-m-d H:i s');
        $employeeData['updated_by'] = Auth::user()->id;
        if ($data['supervisor']){
            $employeeData['supervisor'] = Employee::where('payroll_number', $data['supervisor'])->pluck('employee_id')->first();

        }
        else
            $employeeData['supervisor_id'] = 1;
        return $employeeData;
    }
}

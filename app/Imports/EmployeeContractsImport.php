<?php

namespace App\Imports;

use Exception;
use App\Models\Employee;
use App\Models\StaffContract;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use App\Lib\Enumerations\StaffContractTypes;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeContractsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private static $globalRowCounter = 2;

    public function chunkSize(): int
    {
        return 300;
    }

    public function model(array $row, $rowNumber = null)
    {
        //
        $currentRow = self::$globalRowCounter++;
        try{
            $validator = Validator::make($row, [
                'payroll_number' => 'required'
            ]);
        
            if ($validator->fails()) 
            {
                $failures = $validator->errors();   
                if ($failures->isNotEmpty()) {
                    $formattedErrors = []; 
                
                    foreach ($failures->toArray() as $field => $messages) {
                        $columnValue = isset($row[$field]) ? $row[$field] : 'N/A';  
                        
                        foreach ($messages as $index => $message) { 
                            $formattedErrors[$field][$index] = "Row {$currentRow}, Column '{$field}': {$columnValue} - {$message}";
                        }
                    }
                
                    session()->flash('errors', $formattedErrors); 
                }            
                return null; // Skip this row
            } else {
                session()->flash('success', 'Import Successful', 30);
            }

        }catch(Exception $e){
            return null;
        }
        $payroll_number = "";

        if ($row['payroll_number'] != '')
        {
             //check if the dates exist otherwise assign blanks
            $date_of_joining = null;
            $date_of_leaving = null;

            $payroll_number = $row['payroll_number'];

            if ($row['date_of_joining'] != '') {
           
                $date_of_joining0 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$row['date_of_joining'])->format('d/m/Y');
                $date_of_joining = dateConvertFormtoDB($date_of_joining0);
    
            } else {
                //assign null date to the leavign date
                $date_of_joining = null;
                //dd($row['date_of_joining']);
            }

            if ($row['date_of_leaving'] != '') {
                $date_of_leaving0 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$row['date_of_leaving'])->format('d/m/Y');
                $date_of_leaving = dateConvertFormtoDB($date_of_leaving0);
            } else {
                $date_of_leaving = null;
            }
    
            $employeeData = Employee::where('payroll_number', $payroll_number)->first();

            if($employeeData)
            {
                $this->createOrUpdateContract($row, $employeeData, $date_of_joining, $date_of_leaving);
            }


        }else{
            return null;
        }

    }

    public function createOrUpdateContract($data, Employee $employee, $date_of_joining, $date_of_leaving)
    {
        $contractData = [
            'employee_id' => $employee->employee_id,
            'contract_type' => StaffContractTypes::getValue(
                strtoupper(
                    trim(
                        explode(' ', trim($data['contract_type']))[0] // Take first word only
                    )
                )
            ),
            'hire_date' => $date_of_joining,
            'start_date' => $date_of_joining,
            'end_date' =>$date_of_leaving,
            'status' => 1,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];
        // Check if a contract already exists for the employee
        $existingContract = StaffContract::where('employee_id', $employee->employee_id)
            ->where('start_date', $date_of_joining)
            ->first();
        if ($existingContract) {
            // Update the existing contract
            $existingContract->update($contractData);
        } else {
            // Create a new contract
            StaffContract::create($contractData);
        }
    }
}

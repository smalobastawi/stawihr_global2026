<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeeOvertime;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Facades\Auth;

class OvertimeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];

    public function model(array $row)
    {
        $employee = Employee::where('payroll_number', $row['payroll_number'])->first();
        if (!$employee) {
            $this->errors[] = 'Row skipped: Employee with payroll number ' . $row['payroll_number'] . ' not found.';
            return null;
        }

        $existingOvertime = EmployeeOvertime::where('employee_id', $employee->employee_id)
            ->where('month_year', $row['month_year'])
            ->first();

        if ($existingOvertime) {
            $this->errors[] = 'Row skipped: Overtime record already exists for employee ' . $employee->first_name . ' ' . $employee->last_name . ' for month ' . $row['month_year'];
            return null;
        }
        
        $overtime_rate = $employee->payGrade ? $employee->payGrade->overtime_rate : 0;
        $total_amount = $row['hours_worked'] * $overtime_rate;

        return new EmployeeOvertime([
            'employee_id' => $employee->employee_id,
            'month_year' => $row['month_year'],
            'hours_worked' => $row['hours_worked'],
            'overtime_rate' => $overtime_rate,
            'total_amount' => $total_amount,
            'created_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'payroll_number' => 'required|string',
            'month_year' => 'required|date_format:Y-m',
            'hours_worked' => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'payroll_number.required' => 'The employee payroll number is required.',
            'month_year.required' => 'The month/year is required.',
            'month_year.date_format' => 'The month/year must be in YYYY-MM format.',
            'hours_worked.required' => 'The hours worked is required.',
            'hours_worked.numeric' => 'The hours worked must be a number.',
            'hours_worked.min' => 'The hours worked must be at least 0.',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = 'An unexpected error occurred: ' . $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

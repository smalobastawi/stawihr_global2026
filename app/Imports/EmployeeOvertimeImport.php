<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeeOvertime;
use App\Models\Payroll\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class EmployeeOvertimeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['payroll_number']) || empty($row['month_year_yyyy_mm'])) {
            return null; // Ignore the row if required fields are missing
        }

        $hourColumns = [
            'weekend_hours_total_units',
            'weekend_days_total_units',
            'public_holiday_days_total_units',
            'public_holiday_hours_total_units',
            'weekday_days_total_units',
            'weekday_hours_total_units',
        ];
        
        $allHoursEmpty = true;
        foreach ($hourColumns as $column) {
            if (!empty($row[$column]) && $row[$column] != 0) {
                $allHoursEmpty = false;
                break;
            }
        }
        
        if ($allHoursEmpty) {
            return null; // Skip this row
        }

        // Get employee details by payroll number
        $employee = $this->getEmployeeByPayrollNumber($row['payroll_number']);
        
        if (!$employee) {
            $validationException = BaseValidationException::withMessages([
                'payroll_number' => ['Employee not found for payroll number: ' . $row['payroll_number']],
            ]);

            throw new ExcelValidationException($validationException, [
                (object) [
                    'row' => $row['payroll_number'],
                    'attribute' => 'payroll_number',
                    'errors' => ['Employee not found'],
                    'values' => $row,
                ],
            ]);
        }

        // Calculate amounts based on employee's payroll rates
        $calculatedAmounts = $this->calculateOvertimeAmounts($employee, $row);
        $payrollPeriod = PayrollPeriod::where('name', $row['payroll_period'])->first();
        
        $overtimeData = [
            'employee_id' => $employee->employee_id,
            'month_year' => $row['month_year_yyyy_mm'],
            'weekend_hours_totals' => $row['weekend_hours_total_units'] ?? 0,
            'weekend_days_totals' => $row['weekend_days_total_units'] ?? 0,
            'public_holiday_hours_totals' => $row['public_holiday_hours_total_units'] ?? 0,
            'public_holiday_days_totals' => $row['public_holiday_days_total_units'] ?? 0,
            'weekday_hours_total' => $row['weekday_hours_total_units'] ?? 0,
            'weekday_days_total' => $row['weekday_days_total_units'] ?? 0,
            'payroll_period_id' => $payrollPeriod->id ?? null,
            'payroll_month' => $row['payroll_month_yyyy_mm'] ?? $row['month_year_yyyy_mm'],
            // 'weekday_amount_calculated' => $calculatedAmounts['weekday_amount'],
            // 'weekend_amount_calculated' => $calculatedAmounts['weekend_amount'],
            // 'holiday_amount_calculated' => $calculatedAmounts['holiday_amount'],
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'status' => 1,
        ];

        // Calculate legacy fields for backward compatibility
        $totalHours = ($row['weekday_hours_total_units'] ?? 0) + 
                     ($row['weekend_hours_total_units'] ?? 0) + 
                     ($row['public_holiday_hours_total_units'] ?? 0);
        
        $totalAmount = $calculatedAmounts['weekday_amount'] + 
                      $calculatedAmounts['weekend_amount'] + 
                      $calculatedAmounts['holiday_amount'];

        $overtimeData['hours_worked'] = $totalHours;
        $overtimeData['total_amount'] = $totalAmount;
        $overtimeData['overtime_rate'] = $totalHours > 0 ? ($totalAmount / $totalHours) : 0;

        // Check if overtime record already exists for this employee and month
        $existingOvertime = EmployeeOvertime::where('employee_id', $employee->employee_id)
            ->where('month_year', $row['month_year_yyyy_mm'])
            ->first();

        if ($existingOvertime) {
            $existingOvertime->update($overtimeData);
            return $existingOvertime;
        } else {
            return EmployeeOvertime::create($overtimeData);
        }
    }

    private function calculateOvertimeAmounts($employee, $row)
    {
        $weekdayAmount = 0;
        $weekendAmount = 0;
        $holidayAmount = 0;

        // Try to get employee payroll rates
        if ($employee->employeePayroll) {
            $baseRate = $employee->employeePayroll->basic_salary / 30 / 8; // Daily hourly rate
            $normalRate = $employee->employeePayroll->overtime_rate_normal ?? 1.5;
            $weekendRate = $employee->employeePayroll->overtime_rate_weekend ?? 2.0;
            $holidayRate = $employee->employeePayroll->overtime_rate_holiday ?? 2.0;

            $weekdayAmount = ($row['weekday_hours_total_units'] ?? 0) * $baseRate * $normalRate;
            $weekendAmount = ($row['weekend_hours_total_units'] ?? 0) * $baseRate * $weekendRate;
            $holidayAmount = ($row['public_holiday_hours_total_units'] ?? 0) * $baseRate * $holidayRate;
        } elseif ($employee->payGrade) {
            // Fallback to pay grade rates
            $baseRate = $employee->payGrade->basic_salary / 30 / 8; // Daily hourly rate
            $overtimeRate = $employee->payGrade->overtime_rate ?? 1.5;

            $weekdayAmount = ($row['weekday_hours_total_units'] ?? 0) * $baseRate * $overtimeRate;
            $weekendAmount = ($row['weekend_hours_total_units'] ?? 0) * $baseRate * ($overtimeRate * 1.33);
            $holidayAmount = ($row['public_holiday_hours_total_units'] ?? 0) * $baseRate * ($overtimeRate * 1.33);
        }

        return [
            'weekday_amount' => $weekdayAmount,
            'weekend_amount' => $weekendAmount,
            'holiday_amount' => $holidayAmount,
        ];
    }

    private function getEmployeeByPayrollNumber($payrollNumber)
    {
        return Employee::where('payroll_number', $payrollNumber)->first();
    }
}
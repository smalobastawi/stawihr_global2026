<?php

namespace App\Imports;

use App\Models\LeaveAdjustment;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\FinancialYear;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class LeaveAdjustmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Handle validation failures and log them to the database
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $row = $failure->values();
            $employeeId = $row['payroll_number'] ?? 'unknown';

            $this->logError(
                "Validation failed during leave adjustment import: " . implode(', ', $failure->errors()),
                $employeeId,
                'validation_error',
                [
                    'row_number' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $row
                ]
            );

            // Also log to file for backward compatibility
            Log::warning("Row {$failure->row()}: " . implode(', ', $failure->errors()));
        }
    }

    /**
     * Log an error to the database
     */
    protected function logError(string $description, string $affectedEmployeeId, string $errorType, array $properties = [])
    {
        try {
            ErrorLog::create([
                'log_name' => 'Leave Adjustment Import',
                'description' => $description,
                'affected_employee_id' => $affectedEmployeeId,
                'subject' => 'LeaveAdjustment',
                'subject_id' => $affectedEmployeeId,
                'causer' => Auth::id(),
                //'logged_check_time' => now()->addMicroseconds(rand(1, 999999)),
                'date' => now()->toDateString(),
                'error_type' => $errorType,
                'module' => 'Leave Management',
                'properties' => $properties,
            ]);
        } catch (\Exception $e) {
            // If still failing due to unique constraint, log to file instead
            Log::error('Failed to log error to database: ' . $e->getMessage());
            Log::error('Original error: ' . $description, $properties);
        }
    }

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['employee_id']) || empty($row['leave_type']) || empty($row['adjustment_type'])) {
            return null;
        }

        try {
            // Find employee
            $employee = Employee::where('payroll_number', $row['payroll_number'])->first();
            if (!$employee) {
                $this->logError(
                    "Employee not found during leave adjustment import",
                    $row['payroll_number'],
                    'employee_not_found',
                    ['employee_id' => $row['payroll_number'], 'row_data' => $row]
                );
                Log::warning("Employee not found: " . $row['payroll_number']);
                return null;
            }

            // Find leave type
            $leaveType = LeaveType::where('leave_type_name', $row['leave_type'])
                ->where('status', 1)
                ->first();

            if (!$leaveType) {
                $this->logError(
                    "Leave type not found during leave adjustment import",
                    $row['payroll_number'],
                    'leave_type_not_found',
                    ['leave_type' => $row['leave_type'], 'employee_id' => $row['payroll_number'], 'row_data' => $row]
                );
                Log::warning("Leave type not found: " . $row['leave_type']);
                return null;
            }

            // Find financial year
            $financialYear = FinancialYear::where('name', $row['financial_year'])->first();
            if (!$financialYear) {
                $this->logError(
                    "Financial year not found during leave adjustment import",
                    $row['payroll_number'],
                    'financial_year_not_found',
                    ['financial_year' => $row['financial_year'], 'employee_id' => $row['payroll_number'], 'row_data' => $row]
                );
                Log::warning("Financial year not found: " . $row['financial_year']);
                return null;
            }

            // Validate adjustment type
            $adjustmentType = strtolower(trim($row['adjustment_type']));
            if (!in_array($adjustmentType, ['add', 'deduct'])) {
                $this->logError(
                    "Invalid adjustment type during leave adjustment import",
                    $row['payroll_number'],
                    'invalid_adjustment_type',
                    ['adjustment_type' => $row['adjustment_type'], 'employee_id' => $row['payroll_number'], 'row_data' => $row]
                );
                Log::warning("Invalid adjustment type: " . $row['adjustment_type']);
                return null;
            }


            // Create the adjustment
            return new LeaveAdjustment([
                'employee_id' => $employee->employee_id,
                'leave_type_id' => $leaveType->leave_type_id,
                'financial_year_id' => $financialYear->id,
                'adjustment_type' => $adjustmentType,
                'adjustment_days' => (float)$row['days'],
                'reason' => $row['reason'] ?? 'Bulk import adjustment',
                'created_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'adjusted_by' => Auth::id(),
                'status' => 'approved',
                'approved_at' => now(),
                'adjustment_date' => now(),
            ]);
        } catch (\Exception $e) {
            $this->logError(
                "Exception during leave adjustment import: " . $e->getMessage(),
                $row['payroll_number'] ?? 'unknown',
                'import_exception',
                ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'row_data' => $row]
            );
            Log::error('Error importing leave adjustment: ' . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'payroll_number' => 'required|exists:employee,payroll_number',
            'leave_type' => 'required|string',
            'financial_year' => 'required|string',
            'adjustment_type' => 'required|in:add,deduct,Add,Deduct,ADD,DEDUCT',
            'days' => 'required|numeric|min:0.01|max:365',
            'reason' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'payroll_number.required' => 'Payroll number is required',
            'payroll_number.exists' => 'Payroll number does not exist',
            'leave_type.required' => 'Leave type is required',
            'financial_year.required' => 'Financial year is required',
            'adjustment_type.required' => 'Adjustment type is required',
            'adjustment_type.in' => 'Adjustment type must be either "add" or "deduct"',
            'days.required' => 'Days is required',
            'days.numeric' => 'Days must be a number',
            'days.min' => 'Days must be at least 0.01',
            'days.max' => 'Days cannot exceed 365',
        ];
    }
}

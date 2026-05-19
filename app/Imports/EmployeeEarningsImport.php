<?php

namespace App\Imports;

use App\Models\EmployeeEarnings;
use App\Models\Employee;
use App\Models\PayrollEarningTypes;
use App\Models\FinancialYear;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\EarningCategories;
use App\Lib\Enumerations\GeneralStatus;

class EmployeeEarningsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];

    public function model(array $row)
    {

        if ($this->allValueColumnsEmpty($row)) {
            $this->errors[] = 'Row skipped for payroll number ' . ($row['payroll_number'] ?? 'unknown') . ': All value columns (amount, percentage, rate, units) are empty or zero.';
            return null;
        }
        // Custom validation to check for existing active earning of the same type
        $employee = Employee::where('payroll_number', $row['payroll_number'])->first();
        if (!$employee) {
            $this->errors[] = 'Row skipped: Employee with payroll number ' . $row['payroll_number'] . ' not found.';
            return null;
        }

        $earningType = PayrollEarningTypes::where('name', $row['earning_type_name'])->first();
        if (!$earningType) {
            $this->errors[] = 'Row skipped: Earning type ' . $row['earning_type_name'] . ' not found.';
            return null;
        }

        $employeePayroll = \App\Models\Payroll\EmployeePayroll::where('employee_id', $employee->employee_id)->first();
        if (!$employeePayroll) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': Payroll profile not found.';
            return null;
        }

        if ($employeePayroll->basic_salary > 0 && str_contains(strtolower($earningType->name), 'salary')) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': Earning type cannot be of "salary" type as the employee already has a salary assigned in their payroll profile.';
            return null;
        }

        if (str_contains(strtolower($row['earning_category']), 'salary') && $employeePayroll->basic_salary > 0) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': Earning category cannot be "salary" type as the employee already has a salary assigned in their payroll profile.';
            return null;
        }

        $existingEarning = EmployeeEarnings::where('employee_id', $employee->employee_id)
            ->where('payroll_earning_type_id', $earningType->id)
            ->where(function ($query) {
                $query->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->exists();

        if ($existingEarning) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': An active earning of type ' . $row['earning_type_name'] . ' already exists.';
            return null;
        }

        $financialYear = FinancialYear::where('name', $row['financial_year_name'])->first();
        if (!$financialYear) {
            $this->errors[] = 'Row skipped: Financial year ' . $row['financial_year_name'] . ' not found.';
            return null;
        }

        // Manual validation based on calculation_type
        $validator = Validator::make($row, [
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,daily_rate|nullable|numeric|min:0',
        ]);

        $validator->sometimes('amount', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'fixed_amount';
        });

        $validator->sometimes('percentage', 'required', function ($input) use ($earningType) {
            return in_array($earningType->calculation_type, ['percentage_of_basic', 'percentage_of_gross']);
        });

        $validator->sometimes('rate', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'daily_rate';
        });

        if ($validator->fails()) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': ' . implode(', ', $validator->errors()->all());
            return null;
        }

        return new EmployeeEarnings([
            'employee_id' => $employee->employee_id,
            'payroll_earning_type_id' => $earningType->id,
            'earning_category' => $row['earning_category'],
            'calculation_type' => $earningType->calculation_type,
            'amount' => $row['amount'] ?? 0,
            'percentage' => $row['percentage'] ?? 0,
            'rate' => $row['rate'] ?? 0,
            'units' => $row['units'] ?? 0,
            'limit_per_month' => $row['limit_per_month'] ?? 0,
            'limit_per_year' => $row['limit_per_year'] ?? 0,
            'effective_from' => $row['effective_from'],
            'effective_to' => $row['effective_to'] ?? null,
            'financial_year_id' => $financialYear->id,
            'payroll_month' => date('n', strtotime($row['payroll_month'])),
            'frequency' => $row['frequency'],
            'is_taxable' => $earningType->taxable,
            'is_pensionable' => $earningType->is_pensionable,
            'is_recurring' => filter_var($row['is_recurring'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'description' => $row['description'] ?? null,
            'status' => GeneralStatus::INACTIVE,
            'approval_status' => ApprovalStatus::DRAFT,
            'created_by' => Auth::id(), // Assuming user is authenticated
            'reference_number' => $this->generateReferenceNumber(),
        ]);
    }

    public function rules(): array
    {
        return [
            'payroll_number' => 'required|string', // Will be used for lookup
            'earning_type_name' => 'required|string', // Will be used for lookup
            'earning_category' => ['required', Rule::in(array_keys(EarningCategories::toArray()))],
            'units' => 'nullable|integer|min:0',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'financial_year_name' => 'required|string', // Will be used for lookup
            'payroll_month' => ['required', 'string', Rule::in(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'])],
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'nullable|boolean',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'payroll_number.required' => 'The employee payroll number is required.',
            'earning_type_name.required' => 'The earning type name is required.',
            'financial_year_name.required' => 'The financial year name is required.',
            'effective_from.date' => 'The effective from date is not in a valid format. Please use YYYY-MM-DD or DD/MM/YYYY format (e.g., 2025-01-31 or 31/01/2025).',
            'effective_to.date' => 'The effective to date is not in a valid format. Please use YYYY-MM-DD or DD/MM/YYYY format (e.g., 2025-12-31 or 31/12/2025).',
            'effective_to.after' => 'The effective to date must be after the effective from date.',
        ];
    }

    public function onError(Throwable $e)
    {
        // Just store the message, not the whole exception
        $this->errors[] = 'An unexpected error occurred: ' . $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle a row validation failure
        foreach ($failures as $failure) {
            $this->errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Generate a unique reference number
     */
    private function generateReferenceNumber()
    {
        $prefix = 'EE';
        $year = date('Y');
        $month = date('m');

        $lastEarning = EmployeeEarnings::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastEarning ? (intval(substr($lastEarning->reference_number, -4)) + 1) : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function allValueColumnsEmpty(array $row): bool
    {
        $amount = $row['amount'] ?? 0;
        $percentage = $row['percentage'] ?? 0;
        $rate = $row['rate'] ?? 0;
        $units = $row['units'] ?? 0;

        // Check if all values are empty, zero, or null
        return (empty($amount) || $amount == 0) &&
            (empty($percentage) || $percentage == 0) &&
            (empty($rate) || $rate == 0) &&
            (empty($units) || $units == 0);
    }
}

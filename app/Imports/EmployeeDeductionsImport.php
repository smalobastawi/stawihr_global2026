<?php

namespace App\Imports;

use App\Models\EmployeeDeductions;
use App\Models\Employee;
use App\Models\Payroll\DeductionType;
use App\Models\FinancialYear;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\ApprovalStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class EmployeeDeductionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithMapping
{
    private $errors = [];

    public function map($row): array
    {
        // Manually parse effective_from and effective_to
        $row['effective_from'] = $this->parseDate($row['effective_from']);
        $row['effective_to'] = $this->parseDate($row['effective_to']);

        return $row;
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Try parsing YYYY-MM-DD
        try {
            return Carbon::createFromFormat('Y-m-d', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            // Fallback to DD/MM/YYYY
            try {
                return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                // If both fail, return original string to let validation handle it
                return $dateString;
            }
        }
    }

    public function model(array $row)
    {
        // Check if all value columns are empty or zero
        if ($this->allValueColumnsEmpty($row)) {
            $this->errors[] = 'Row skipped for payroll number ' . ($row['payroll_number'] ?? 'unknown') . ': All value columns (amount, percentage, rate, units) are empty or zero.';
            \Log::info('Row skipped - All value columns empty for payroll number: ' . ($row['payroll_number'] ?? 'unknown'));
            return null;
        }

        $employee = Employee::where('payroll_number', $row['payroll_number'])->first();
        if (!$employee) {
            $this->errors[] = 'Row skipped: Employee with payroll number ' . $row['payroll_number'] . ' not found.';
            \Log::info('Employee Not Found: ' . $row['payroll_number']);
            return null;
        }
        \Log::info('Employee Found: ' . $row['payroll_number'] . ' (ID: ' . $employee->employee_id . ')');

        $deductionType = DeductionType::whereRaw('LOWER(name) = ?', [strtolower(trim($row['deduction_name']))])->first();
        if (!$deductionType) {
            $this->errors[] = 'Row skipped: Deduction type ' . $row['deduction_name'] . ' not found.';
            \Log::info('Deduction Type Not Found: ' . $row['deduction_name']);
            return null;
        }

        $existingDeduction = EmployeeDeductions::where('employee_id', $employee->employee_id)
            ->where('deduction_type_id', $deductionType->id)
            ->where(function ($query) {
                $query->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->exists();

        if ($existingDeduction) {
            $this->errors[] = 'Row skipped for employee ' . $row['payroll_number'] . ': An active deduction of type ' . $row['deduction_name'] . ' already exists.';
            return null;
        }

        $financialYear = FinancialYear::where('name', $row['financial_year_name'])->first();
        if (!$financialYear) {
            $this->errors[] = 'Row skipped: Financial year ' . $row['financial_year_name'] . ' not found.';
            return null;
        }

        return new EmployeeDeductions([
            'employee_id' => $employee->employee_id,
            'deduction_type_id' => $deductionType->id,
            'deduction_category' => $row['deduction_category'],
            'calculation_type' => $deductionType->default_calculation_type,
            'amount' => $row['amount'] ?? 0,
            'percentage' => $row['percentage'] ?? 0,
            'rate' => $row['rate'] ?? 0,
            'units' => $row['units'] ?? 0,
            'effective_from' => $row['effective_from'],
            'effective_to' => $row['effective_to'] ?? null,
            'financial_year_id' => $financialYear->id,
            'payroll_month' => date('n', strtotime($row['payroll_month'])),
            'frequency' => $row['frequency'],
            'is_tax_deductible' => filter_var($row['is_tax_deductible'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_pensionable' => filter_var($row['is_pensionable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_recurring' => filter_var($row['is_recurring'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'description' => $row['description'] ?? null,
            'status' => GeneralStatus::INACTIVE,
            'approval_status' => ApprovalStatus::DRAFT,
            'created_by' => Auth::id(),
            'reference_number' => $this->generateReferenceNumber(),
        ]);
    }

    /**
     * Check if all value columns are empty or zero
     */
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

    public function rules(): array
    {
        return [
            'payroll_number' => 'required|string',
            'deduction_name' => 'required|string',
            'deduction_category' => 'required|in:loan_repayment,advance_repayment,tax,nssf,nhif,other',
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,hourly_rate,daily_rate|nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'financial_year_name' => 'required|string',
            'payroll_month' => ['required', 'string', Rule::in(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'])],
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'is_tax_deductible' => 'nullable|boolean',
            'is_pensionable' => 'nullable|boolean',
            'is_recurring' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'payroll_number.required' => 'The employee payroll number is required.',
            'deduction_name.required' => 'The deduction type name is required.',
            'financial_year_name.required' => 'The financial year name is required.',
            'amount.required_if' => 'The amount is required when calculation type is fixed_amount.',
            'percentage.required_if' => 'The percentage is required when calculation type is percentage of basic or gross.',
            'rate.required_if' => 'The rate is required when calculation type is hourly or daily rate.',
            'effective_from.date_format' => 'The effective from date is not in a valid format. Please use YYYY-MM-DD or DD/MM/YYYY format (e.g., 2025-01-31 or 31/01/2025).',
            'effective_to.date_format' => 'The effective to date is not in a valid format. Please use YYYY-MM-DD or DD/MM/YYYY format (e.g., 2025-12-31 or 31/12/2025).',
            'effective_to.after' => 'The effective to date must be after the effective from date.',
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

    private function generateReferenceNumber()
    {
        $prefix = 'ED' . date('Ymd');

        $lastDeduction = EmployeeDeductions::where('reference_number', 'like', $prefix . '%')
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($lastDeduction) {
            $lastSequence = (int) substr($lastDeduction->reference_number, -4);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

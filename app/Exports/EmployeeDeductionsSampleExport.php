<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeDeductionsSampleExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'employee_payroll_number' => 'EMP001',
                'deduction_name' => 'Loan Repayment',
                'deduction_category' => 'loan_repayment',
                'calculation_type' => 'fixed_amount',
                'amount' => 1000,
                'percentage' => '',
                'rate' => '',
                'units' => '',
                'limit_per_month' => '',
                'limit_per_year' => '',
                'effective_from' => '2025-01-01',
                'effective_to' => '2025-12-31',
                'payroll_year' => 2025,
                'payroll_month' => 1,
                'frequency' => 'monthly',
                'is_recurring' => 1,
                'status' => 1,
                'description' => 'Monthly loan repayment',
            ],
            [
                'employee_payroll_number' => 'EMP002',
                'deduction_name' => 'NSSF Contribution',
                'deduction_category' => 'nssf',
                'calculation_type' => 'percentage_of_gross',
                'amount' => '',
                'percentage' => 5,
                'rate' => '',
                'units' => '',
                'limit_per_month' => '',
                'limit_per_year' => '',
                'effective_from' => '2025-01-01',
                'effective_to' => '',
                'payroll_year' => 2025,
                'payroll_month' => 1,
                'frequency' => 'monthly',
                'is_recurring' => 1,
                'status' => 1,
                'description' => 'Mandatory NSSF contribution',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'employee_payroll_number',
            'deduction_name',
            'deduction_category',
            'calculation_type',
            'amount',
            'percentage',
            'rate',
            'units',
            'limit_per_month',
            'limit_per_year',
            'effective_from',
            'effective_to',
            'payroll_year',
            'payroll_month',
            'frequency',
            'is_recurring',
            'status',
            'description',
        ];
    }
}

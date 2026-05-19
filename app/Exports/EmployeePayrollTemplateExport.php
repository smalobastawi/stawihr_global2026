<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PensionScheme;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class EmployeePayrollTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    public function array(): array
    {
        // Get all active employees with their payroll data (if exists)
        $employees = Employee::where('status', 1)
            ->with([
                'employeePayroll',
                'employeePayroll.pensionSchemes', // Load the many-to-many relationship
                'department',
                'designation'
            ])
            ->get();

        $data = [];
        foreach ($employees as $employee) {
            $payroll = $employee->employeePayroll;

            // Handle multiple pension schemes
            $pensionSchemeNames = '';
            if ($payroll && $payroll->pensionSchemes->isNotEmpty()) {
                $pensionSchemeNames = $payroll->pensionSchemes
                    ->pluck('name')
                    ->implode('; '); // Use semicolon as separator for multiple schemes
            }

            // Get pension rates if available
            $employeePensionRate = '';
            $employerPensionRate = '';
            if ($payroll && $payroll->pensionSchemes->isNotEmpty()) {
                $firstScheme = $payroll->pensionSchemes->first();
                if ($firstScheme->pivot) {
                    $employeePensionRate = $firstScheme->pivot->employee_rate ?? '';
                    $employerPensionRate = $firstScheme->pivot->employer_rate ?? '';
                }
            }

            $data[] = [
                'employee_id' => $employee->employee_id,
                'payroll_number' => $employee->payroll_number ?? '',
                'employee_name' => $employee->fullName(),
                'email' => $employee->email ?? $employee->work_email ?? '',
                'department' => $employee->department->department_name ?? '',
                'designation' => $employee->designation->designation_name ?? '',
                'basic_salary' => $payroll->basic_salary ?? '',
                'currency' => $payroll->currency ?? 'KES',
                'income_frequency' => $payroll->income_frequency ?? 'monthly',
                'phone_number' => $payroll->phone_number ?? $employee->phone ?? '',
                'payment_method' => $payroll->payment_method ?? '',
                'bank_name' => $payroll->bank_name ?? $employee->bank ?? '',
                'bank_branch' => $payroll->bank_branch ?? $employee->bank_branch ?? '',
                'account_number' => $payroll->account_number ?? $employee->bank_account_number ?? '',
                'account_name' => $payroll->account_name ?? $employee->bank_account_name ?? '',
                'tax_status' => $payroll->tax_status ?? '',
                'disability_exemption' => $payroll ? ($payroll->disability_exemption ? 'Yes' : 'No') : 'No',
                'kra_pin' => $payroll->kra_pin ?? $employee->KRA_Pin ?? '',
                'nssf_number' => $payroll->nssf_number ?? $employee->NSSF_no ?? '',
                'nssf_rate_type' => $this->getNssfRateTypeLabel($employee->nssf_rate_type ?? 2),
                'shif_number' => $payroll->shif_number ?? $employee->shif_number ?? '',
                'pension_scheme_names' => $pensionSchemeNames,
                'employee_pension_rate' => $employeePensionRate,
                'employer_pension_rate' => $employerPensionRate,
                'overtime_rate_normal' => $payroll->overtime_rate_normal ?? '1.5',
                'overtime_rate_weekend' => $payroll->overtime_rate_weekend ?? '2.0',
                'overtime_rate_holiday' => $payroll->overtime_rate_holiday ?? '2.0',
                'is_active' => $payroll ? ($payroll->is_active ? 'Active' : 'Inactive') : 'Active',
                'effective_date' => $payroll && $payroll->effective_date ? $payroll->effective_date->format('Y-m-d') : '',
                'notes' => 'Update fields as needed, leave blank to keep current values'
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'EMPLOYEE_ID',
            'PAYROLL_NUMBER',
            'EMPLOYEE_NAME',
            'EMAIL',
            'DEPARTMENT',
            'DESIGNATION',
            'BASIC_SALARY',
            'CURRENCY',
            'INCOME_FREQUENCY',
            'PHONE_NUMBER',
            'PAYMENT_METHOD',
            'BANK_NAME',
            'BANK_BRANCH',
            'ACCOUNT_NUMBER',
            'ACCOUNT_NAME',
            'TAX_STATUS',
            'DISABILITY_EXEMPTION',
            'KRA_PIN',
            'NSSF_NUMBER',
            'NSSF_RATE_TYPE',
            'SHIF_NUMBER',
            'PENSION_SCHEME_NAMES',
            'EMPLOYEE_PENSION_RATE',
            'EMPLOYER_PENSION_RATE',
            'OVERTIME_RATE_NORMAL',
            'OVERTIME_RATE_WEEKEND',
            'OVERTIME_RATE_HOLIDAY',
            'STATUS',
            'EFFECTIVE_DATE',
            'NOTES'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get all available pension schemes for dropdown
                $pensionSchemes = PensionScheme::where('is_active', true)->pluck('name')->toArray();

                // TAX_STATUS Dropdown (Column P)
                $taxStatusOptions = ['resident', 'non_resident', 'exempt'];
                $this->applyDropdown($sheet, 'P', $taxStatusOptions);

                // DISABILITY_EXEMPTION Dropdown (Column Q)
                $disabilityExemptionOptions = ['Yes', 'No'];
                $this->applyDropdown($sheet, 'Q', $disabilityExemptionOptions);

                // NSSF_RATE_TYPE Dropdown (Column T)
                $nssfRateTypeOptions = ['Tier 1 & 2', 'Tier 1 only', 'No Deduction'];
                $this->applyDropdown($sheet, 'T', $nssfRateTypeOptions);

                // STATUS Dropdown (Column AB)
                $statusOptions = ['Active', 'Inactive'];
                $this->applyDropdown($sheet, 'AB', $statusOptions);

                // PAYMENT_METHOD Dropdown (Column K)
                $paymentMethodOptions = ['bank_transfer', 'mobile_money', 'cash', 'cheque'];
                $this->applyDropdown($sheet, 'K', $paymentMethodOptions);

                // INCOME_FREQUENCY Dropdown (Column I)
                $incomeFrequencyOptions = ['daily', 'weekly', 'monthly'];
                $this->applyDropdown($sheet, 'I', $incomeFrequencyOptions);

                // CURRENCY Dropdown (Column H)
                $currencyOptions = ['KES', 'USD', 'EUR', 'GBP'];
                $this->applyDropdown($sheet, 'H', $currencyOptions);

                // PENSION_SCHEME_NAMES Dropdown (Column V) - Multiple selection hint
                if (!empty($pensionSchemes)) {
                    $this->applyDropdown($sheet, 'V', $pensionSchemes);
                }

                // Add data validation for numeric fields
                $this->applyNumericValidation($sheet, 'G'); // BASIC_SALARY
                $this->applyNumericValidation($sheet, 'W'); // EMPLOYEE_PENSION_RATE
                $this->applyNumericValidation($sheet, 'X'); // EMPLOYER_PENSION_RATE
                $this->applyNumericValidation($sheet, 'Y'); // OVERTIME_RATE_NORMAL
                $this->applyNumericValidation($sheet, 'Z'); // OVERTIME_RATE_WEEKEND
                $this->applyNumericValidation($sheet, 'AA'); // OVERTIME_RATE_HOLIDAY

                // Freeze the first row
                $sheet->freezePane('A2');

                // Add auto-filter to headers
                $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');
            },
        ];
    }

    private function applyDropdown(Worksheet $sheet, string $column, array $options)
    {
        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true); // Allow blank for optional fields
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input Error');
        $validation->setError('Value is not in the list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please select a value from the dropdown list.');
        $validation->setFormula1('"' . implode(',', $options) . '"');

        // Apply to a reasonable number of rows
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell($column . $row)->setDataValidation(clone $validation);
        }
    }

    private function applyNumericValidation(Worksheet $sheet, string $column)
    {
        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_DECIMAL);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Please enter a valid number.');
        $validation->setPromptTitle('Numeric input required');
        $validation->setPrompt('Please enter a valid number (e.g., 50000 or 5.5)');

        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell($column . $row)->setDataValidation(clone $validation);
        }
    }

    private function getNssfRateTypeLabel($type)
    {
        $types = [
            2 => 'Tier 1 & 2',
            3 => 'Tier 1 only',
            4 => 'No Deduction'
        ];

        return $types[$type] ?? 'Tier 1 & 2';
    }

    /**
     * Helper method to handle employees without payroll data
     */
    private function getPayrollValue($payroll, $field, $default = '')
    {
        return $payroll ? ($payroll->$field ?? $default) : $default;
    }
}

<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\FinancialYear;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class LeaveAdjustmentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    public function array(): array
    {
        // Get all active employees
        $employees = Employee::where('status', 1)
            ->with(['department', 'designation', 'leaveGroup'])
            ->orderBy('first_name')
            ->get();

        $data = [];
        $currentFinancialYear = getCurrentFinancialYear();
        
        // Only one row per employee
        foreach ($employees as $employee) {
            $data[] = [
                'employee_id' => $employee->employee_id,
                'payroll_number' => $employee->payroll_number ?? '',
                'employee_name' => $employee->fullName(),
                'department' => $employee->department->department_name ?? '',
                'designation' => $employee->designation->designation_name ?? '',
                'leave_type' => '',
                'financial_year' => $currentFinancialYear ? $currentFinancialYear->name : '',
                'adjustment_type' => '',
                'days' => '',
                'reason' => '',
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
            'DEPARTMENT',
            'DESIGNATION',
            'LEAVE_TYPE',
            'FINANCIAL_YEAR',
            'ADJUSTMENT_TYPE',
            'DAYS',
            'REASON',
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

                // Get leave types for dropdown
                $leaveTypes = LeaveType::where('status', 1)
                    ->pluck('leave_type_name')
                    ->toArray();

                // Get current and previous financial years only
                $currentFY = getCurrentFinancialYear();
                $financialYears = [];
                
                if ($currentFY) {
                    // Add current financial year
                    $financialYears[] = $currentFY->name;
                    
                    // Get previous financial year
                    $previousFY = FinancialYear::where('end_date', '<', $currentFY->start_date)
                        ->orderBy('end_date', 'desc')
                        ->first();
                    
                    if ($previousFY) {
                        $financialYears[] = $previousFY->name;
                    }
                }

                // LEAVE_TYPE Dropdown (Column F)
                if (!empty($leaveTypes)) {
                    $this->applyDropdown($sheet, 'F', $leaveTypes);
                }

                // FINANCIAL_YEAR Dropdown (Column G) - Only current and previous year
                if (!empty($financialYears)) {
                    $this->applyDropdown($sheet, 'G', $financialYears);
                }

                // ADJUSTMENT_TYPE Dropdown (Column H)
                $adjustmentTypes = ['add', 'deduct'];
                $this->applyDropdown($sheet, 'H', $adjustmentTypes);

                // Add numeric validation for DAYS field (Column I)
                $this->applyNumericValidation($sheet, 'I');

                // Freeze the first row
                $sheet->freezePane('A2');

                // Add auto-filter to headers
                $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');

                // Make certain columns read-only by styling them differently
                $readOnlyColumns = ['A', 'B', 'C', 'D', 'E']; // Employee info only
                foreach ($readOnlyColumns as $col) {
                    $sheet->getStyle($col . '2:' . $col . '1000')->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF0F0F0']
                        ]
                    ]);
                }
            },
        ];
    }

    private function applyDropdown(Worksheet $sheet, string $column, array $options)
    {
        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false); // Required field
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
        $validation->setAllowBlank(false); // Required field
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Please enter a valid number greater than 0.');
        $validation->setPromptTitle('Numeric input required');
        $validation->setPrompt('Please enter a valid number (e.g., 1, 1.5, 2.5)');
        $validation->setOperator(DataValidation::OPERATOR_GREATERTHAN);
        $validation->setFormula1('0');

        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell($column . $row)->setDataValidation(clone $validation);
        }
    }
}

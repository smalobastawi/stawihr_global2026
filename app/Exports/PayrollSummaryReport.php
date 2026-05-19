<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PayrollSummaryReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithStrictNullComparison
{
    protected $data;
    protected $earningTypes;
    protected $deductionTypes;
    protected $pensionSchemes;

    public function __construct($data, $earningTypes, $deductionTypes, $pensionSchemes)
    {
        $this->data = $data;
        $this->earningTypes = $earningTypes;
        $this->deductionTypes = $deductionTypes;
        $this->pensionSchemes = $pensionSchemes;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = [
            'Employee Code',
            'Employee Surname',
            'Employee First name',
            'Employee Second name',
            'Job Title',
            'Locations (HA)',
            'Sub_Programs (HA)',
            'Basic Income (Earning)',
            'Income Frequency',
        ];

        // Add earning type columns
        foreach ($this->earningTypes as $earningType) {
            if ($earningType->name !== 'Basic Income') {
                $headings[] = $earningType->name . ' (Earning)';
            }
        }

        // Add earnings totals
        $headings[] = 'Earning Total';
        $headings[] = 'Unpaid';
        $headings[] = 'Effective Earning';

        // Add statutory deductions
        $headings[] = 'PAYE (Deduction)';
        $headings[] = 'NSSF Tier I (Deduction)';
        $headings[] = 'NSSF Tier II (Deduction)';
        $headings[] = 'TOTAL NSSF (Deduction)';
        $headings[] = 'SHIF (Deduction)';
        $headings[] = 'Affordable Housing Levy (Deduction)';

        // Add custom deduction types
        foreach ($this->deductionTypes as $deductionType) {
            $headings[] = $deductionType->name . ' (Deduction)';
        }

        // Add dynamic pension scheme columns for employee contributions
        foreach ($this->pensionSchemes as $scheme) {
            $headings[] = $scheme->name . ' Employee Contribution (Deduction)';
            $headings[] = $scheme->name . ' Employee Rate %';
        }

        // Add total employee pension
        $headings[] = 'Pension Employee Total';

        // Add salary advance and total deductions
        $headings[] = 'Salary Advance (Deduction)';
        $headings[] = 'Total Deductions';

        // Add company contributions
        $headings[] = 'NITA';
        $headings[] = 'NSSF Tier I (CompanyContribution)';
        $headings[] = 'NSSF Tier II (CompanyContribution)';
        $headings[] = 'TOTAL NSSF (CompanyContribution)';
        $headings[] = 'Affordable Housing Levy (Company Contribution)';
        $headings[] = 'SHIF (Company Contribution)';

        // Add dynamic pension scheme columns for employer contributions
        foreach ($this->pensionSchemes as $scheme) {
            $headings[] = $scheme->name . ' Employer Contribution (Company)';
            $headings[] = $scheme->name . ' Employer Rate %';
        }

        // Add total employer pension and company contributions
        $headings[] = 'Pension Employer Total';
        $headings[] = 'CompanyContribution Total';

        // Add final values
        $headings[] = 'NetPay';
        $headings[] = 'Total Cost';
        $headings[] = 'Payment Reference';
        $headings[] = 'One-Third Rule (%)';

        // Add project allocation columns
        $headings[] = 'PRIMARY GRANT';
        $headings[] = 'LOE';
        $headings[] = 'SEC. GRANT';
        $headings[] = 'LOE';
        $headings[] = 'Tertiary project';
        $headings[] = 'LOE';

        return $headings;
    }

    public function map($row): array
    {
        // Start with basic employee info
        $mappedData = [
            $row['employee_code'] ?? '',
            $row['employee_surname'] ?? '',
            $row['employee_first_name'] ?? '',
            $row['employee_second_name'] ?? '',
            $row['job_title'] ?? '',
            $row['location'] ?? '',
            $row['sub_program'] ?? '',
            $this->formatNumericValue($row['Basic Income'] ?? 0),
            $row['Income Frequency'] ?? '',
        ];

        // Add earning type values
        foreach ($this->earningTypes as $earningType) {
            if ($earningType->name !== 'Basic Income') {
                $mappedData[] = $this->formatNumericValue($row[$earningType->name] ?? 0);
            }
        }

        // Add earnings totals
        $mappedData[] = $this->formatNumericValue($row['Earning Total'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Unpaid'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Effective Earning'] ?? 0);

        // Add statutory deductions
        $mappedData[] = $this->formatNumericValue($row['PAYE'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['NSSF Tier I (Deduction)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['NSSF Tier II (Deduction)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['TOTAL NSSF (Deduction)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['SHIF'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Affordable Housing Levy(Deduction)'] ?? 0);

        // Add custom deduction values
        foreach ($this->deductionTypes as $deductionType) {
            $mappedData[] = $this->formatNumericValue($row[$deductionType->name] ?? 0);
        }

        // Add dynamic pension scheme employee contributions and rates
        $totalEmployeePension = 0;
        foreach ($this->pensionSchemes as $scheme) {
            $employeeContribution = $this->formatNumericValue($row[$scheme->name . ' (Employee)'] ?? 0);
            $employeeRate = $this->formatNumericValue($row[$scheme->name . ' Employee Rate'] ?? 0);

            $mappedData[] = $employeeContribution;
            $mappedData[] = $employeeRate;

            $totalEmployeePension += (float)$employeeContribution;
        }

        // Add total employee pension
        $mappedData[] = $this->formatNumericValue($totalEmployeePension);

        // Add salary advance and total deductions
        $mappedData[] = $this->formatNumericValue($row['Salary Advance (Deduction)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Total Deductions'] ?? 0);

        // Add company contributions
        $mappedData[] = $this->formatNumericValue($row['NITA_Levy'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['NSSF Tier I (CompanyContribution)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['NSSF Tier II (CompanyContribution)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['TOTAL NSSF (CompanyContribution)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Affordable Housing Levy (Company Contribution)'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['SHIF (Company Contribution)'] ?? 0);

        // Add dynamic pension scheme employer contributions and rates
        $totalEmployerPension = 0;
        foreach ($this->pensionSchemes as $scheme) {
            $employerContribution = $this->formatNumericValue($row[$scheme->name . ' (Employer)'] ?? 0);
            $employerRate = $this->formatNumericValue($row[$scheme->name . ' Employer Rate'] ?? 0);

            $mappedData[] = $employerContribution;
            $mappedData[] = $employerRate;

            $totalEmployerPension += (float)$employerContribution;
        }

        // Add total employer pension and company contributions
        $mappedData[] = $this->formatNumericValue($totalEmployerPension);
        $mappedData[] = $this->formatNumericValue($row['CompanyContribution Total'] ?? 0);

        // Add final values
        $mappedData[] = $this->formatNumericValue($row['NetPay'] ?? 0);
        $mappedData[] = $this->formatNumericValue($row['Total Cost'] ?? 0);
        $mappedData[] = $row['payment_reference_type'] ?? '';
        $mappedData[] = $this->formatNumericValue($row['30_percent_rule'] ?? 0);

        // Add project allocation values
        $mappedData[] = $row['primary_grant'] ?? '';
        $mappedData[] = $row['primary_loe'] ?? '';
        $mappedData[] = $row['sec_grant'] ?? '';
        $mappedData[] = $row['sec_loe'] ?? '';
        $mappedData[] = $row['tertiary_project'] ?? '';
        $mappedData[] = $row['tertiary_loe'] ?? '';

        return $mappedData;
    }

    /**
     * Format numeric values to ensure zeros are displayed as 0.0
     */
    private function formatNumericValue($value)
    {
        // Convert empty strings, null, or false to 0
        if ($value === '' || $value === null || $value === false) {
            return 0;
        }

        // If it's already numeric, return as float
        if (is_numeric($value)) {
            return (float)$value;
        }

        // Try to convert string to numeric, return 0 if not possible
        $numericValue = is_string($value) ? filter_var($value, FILTER_VALIDATE_FLOAT) : $value;
        return $numericValue !== false ? (float)$numericValue : 0;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 1; // +1 for header
        $lastColumn = $sheet->getHighestColumn();

        // Apply number formatting to show zeros with one decimal place
        $sheet->getStyle('H2:' . $lastColumn . $lastRow)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

        // Style for metadata columns (text columns) - now at the end
        $metadataStartColumn = $this->getMetadataStartColumn();
        $sheet->getStyle($metadataStartColumn . '2:' . $lastColumn . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('@'); // Text format

        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],
            'A2' => ['freezePane' => 'A2'],

            // Optional: Add different background color for metadata columns in header
            $metadataStartColumn . '1:' . $lastColumn . '1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE6F3FF'] // Light blue for metadata
                ]
            ],
        ];
    }

    /**
     * Calculate the starting column for metadata based on column count
     */
    private function getMetadataStartColumn()
    {
        // Calculate total columns before metadata
        $baseColumns = 9; // Employee info + basic income + frequency
        $earningColumns = $this->earningTypes->where('name', '!=', 'Basic Income')->count();
        $standardColumns = 17; // Earning totals (3) + statutory deductions (6) + pension employee total (1) + salary advance & total deductions (2) + company contributions (5)
        $deductionColumns = $this->deductionTypes->count();
        $pensionEmployeeColumns = $this->pensionSchemes->count() * 2; // Each scheme has 2 columns
        $pensionEmployerColumns = $this->pensionSchemes->count() * 2; // Each scheme has 2 columns

        $totalColumnsBeforeMetadata = $baseColumns + $earningColumns + $standardColumns + $deductionColumns + $pensionEmployeeColumns + $pensionEmployerColumns;

        // Convert to Excel column letter (A, B, C, ... AA, AB, etc.)
        return $this->numberToExcelColumn($totalColumnsBeforeMetadata + 1);
    }

    /**
     * Convert number to Excel column letter
     */
    private function numberToExcelColumn($number)
    {
        $column = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $column = chr(65 + $remainder) . $column;
            $number = intval(($number - $remainder) / 26);
        }
        return $column;
    }
}